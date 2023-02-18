<!-- // Database Structure 
CREATE TABLE 'webpage_details' (
 'link' text NOT NULL,
 'title' text NOT NULL,
 'description' text NOT NULL,
 'internal_link' text NOT NULL,
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=latin1 -->

<?php
    $main_url="http://yourpetpa.com.au";
    $str = file_get_contents($main_url);

    // echo $str;
    // die;

    // Gets Webpage Title
    if(strlen($str)>0)
    {
    $str = trim(preg_replace('/\s+/', ' ', $str)); // supports line breaks inside <title>
    preg_match("/\<title\>(.*)\<\/title\>/i",$str,$title); // ignore case
    $title=$title[1];
    }
    // Gets Webpage product id
    $b =$main_url;
    @$url = parse_url( $b );
    @$tags = get_meta_tags($url['scheme'].'://'.$url['host'] );
    $id=$tags['id'];

    // Gets Webpage Description
    $b =$main_url;
    @$url = parse_url( $b );
    @$tags = get_meta_tags($url['scheme'].'://'.$url['host'] );
    $description=$tags['description'];

    // Gets Webpage Category
    $c =$main_url;
    @$url = parse_url( $c );
    @$tags = get_meta_tags($url['scheme'].'://'.$url['host'] );
    $category=$tags['category'];

    // Gets Webpage Price
    $p =$main_url;
    @$url = parse_url( $p );
    @$tags = get_meta_tags($url['scheme'].'://'.$url['host'] );
    $price=$tags['price'];

    // Gets Webpage Internal Links
    $doc = new DOMDocument; 
    @$doc->loadHTML($str); 

    $items = $doc->getElementsByTagName('a'); 
    foreach($items as $value) 
    { 
    $attrs = $value->attributes; 
    $sec_url[]=$attrs->getNamedItem('url')->nodeValue;
    }
    $url=implode(",",$sec_url);

      // Gets Webpage Image url
      $doc = new DOMDocument; 
      @$doc->loadHTML($str); 
  
      $items = $doc->getElementsByTagName('a'); 
      foreach($items as $value) 
      { 
      $attrs = $value->attributes; 
      $sec_url[]=$attrs->getNamedItem('image_url')->nodeValue;
      }
      $image_url=implode(",",$sec_url);

    // Store Data In Database
    $host="localhost";
    $username="root";
    $password="";
    $databasename="task_2";
    $connect=mysqli_connect($host,$username,$password);
    $db=mysqli_select_db($databasename);

    mysqli_query("insert into product values('$id','$main_url','$title','$description','$url','$category','$price','$image_url')");

?>


<!-- test-2 -->

<?php
    // Set the website URL
    $url = 'https://yourpetpa.com.au/';

    // Initialize cURL
    $curl = curl_init();

    // Set the cURL options
    curl_setopt_array($curl, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.36'
    ));

    // Execute the cURL request
    $html = curl_exec($curl);
    
    // Close the cURL session
    curl_close($curl);

    // Initialize the DOMDocument
    $dom = new DOMDocument();
    @$dom->loadHTML($html);
    
    // Get all the products on the website
    $products = $dom->getElementsByTagName('div');

    // Initialize an empty array to store the product information
    $product_data = array();

    // Loop through the products and extract the product information
    foreach ($products as $product) {
        $title = $product->getElementsByTagName('h3')->item(0)->nodeValue;
        $description = $product->getElementsByTagName('p')->item(0)->nodeValue;
        $category = $product->getElementsByTagName('span')->item(0)->nodeValue;
        $price = $product->getElementsByTagName('span')->item(1)->nodeValue;
        $product_url = $product->getElementsByTagName('a')->item(0)->getAttribute('href');
        $image_url = $product->getElementsByTagName('img')->item(0)->getAttribute('src');
        
        // Add the product information to the product_data array
        $product_data[] = array(
            'Title' => $title,
            'Description' => $description,
            'Category' => $category,
            'Price' => $price,
            'Product URL' => $product_url,
            'Image URL' => $image_url
        );
    }

    // Open a file handle for writing to the CSV file
    $file = fopen('product_feed.csv', 'w');

    // Write the header row to the CSV file
    fputcsv($file, array_keys($product_data[0]));

    // Write the product information to the CSV file
    foreach ($product_data as $data) {
        fputcsv($file, $data);
    }

    // Close the file handle
    fclose($file);
?>
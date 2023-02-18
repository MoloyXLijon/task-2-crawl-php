<?php

// set the website URL to crawl
$url = "https://yourpetpa.com.au/";

// initialize cURL session
$curl = curl_init();

// set cURL options
curl_setopt($curl, CURLOPT_URL, $url);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

// send the HTTP request and get the HTML content of the website
$html = curl_exec($curl);

// close the cURL session
curl_close($curl);

// create a new DOMDocument object
$dom = new DOMDocument();

// load the HTML content into the DOMDocument
$dom->loadHTML($html);

// create a new CSV file to store the product feed
$filename = "product_feed.csv";
$file = fopen($filename, "w");

// write the headers to the CSV file
$headers = array("Title", "Description", "Category", "Price", "Product URL", "Image URL");
fputcsv($file, $headers);

// find all the product items on the website
$product_items = $dom->getElementsByClassName("cc-filters-results");

// loop through the product items and extract the required details
foreach ($product_items as $item) {
    // find the title of the product
    $title_element = $item->getElementsByClassName("product-block__title-link")->item(0);
    $title = $title_element->textContent;
    
    // find the description of the product
    $description_element = $item->getElementsByTagName("p")->item(0);
    $description = $description_element->textContent;
    
    // find the category of the product
    $category_element = $item->getElementsByTagName("span")->item(0);
    $category = $category_element->textContent;
    
    // find the price of the product
    $price_element = $item->getElementsByClassName("product-price__reduced")->item(1);
    $price = $price_element->textContent;
    
    // find the URL of the product
    $product_url_element = $item->getElementsByTagName("a")->item(0);
    $product_url = $product_url_element->getAttribute("href");
    
    // find the image URL of the product
    $image_url_element = $item->getElementsByTagName("img")->item(0);
    $image_url = $image_url_element->getAttribute("src");
    
    // write the product details to the CSV file
    $product = array($title, $description, $category, $price, $product_url, $image_url);
    fputcsv($file, $product);
}

// close the CSV file
fclose($file);

echo "Product feed generated successfully!";

?>

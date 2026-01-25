<?php
require __DIR__ . "/../vendor/autoload.php";

use GuzzleHttp\Client;

$crawler = new Client([
    'headers' => [
        'User-Agent' => 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
        'Accept'     => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
        'Accept-Language' => 'en-US,en;q=0.5',
    ],
    'timeout' => 15,
]);
$mainResponse = $crawler->get("https://www.bebebliss.ro/");
$mainHtml = (string) $mainResponse->getBody();

libxml_use_internal_errors(true);
$doc = new DOMDocument();
$doc->loadHTML($mainHtml);
$xpath = new DOMXPath($doc);


$results = [];

$mainCategoryNodes = $xpath->query("//ul[contains(@class, 'side-menu')]//a");

foreach($mainCategoryNodes as $mainCategoryNode) {
    $categoryLink = $mainCategoryNode->getAttribute("href");
    
    if(!str_starts_with($categoryLink, "http")) {
        $categoryLink = "https://www.bebebliss.ro/" . $categoryLink;
    }
    
    $categoryResponse = $crawler->get($categoryLink);
    $categoryHtml = (string) $categoryResponse->getBody();

    $categoryDoc = new DOMDocument();
    $categoryDoc->loadHTML($categoryHtml);
    $categoryXpath = new DOMXPath($categoryDoc);

    $categoryItemNodes = $categoryXpath->query("//ul[contains(@class, 'products-grid')]//li//a");

    foreach($categoryItemNodes as $categoryItemNode) {
        $itemLink = $categoryItemNode->getAttribute("href");

        if(!str_starts_with($itemLink, "http")) {
            $itemLink = "https://www.bebebliss.ro/" . $itemLink;
        }

        $itemResponse = $crawler->get($itemLink);
        $itemHtml = (string) $itemResponse->getBody();

        $itemDoc = new DOMDocument();
        $itemDoc->loadHTML($itemHtml);
        $itemXpath = new DOMXPath($itemDoc);

        $itemTitle = trim($itemXpath->evaluate("string(//h1[contains(@class, 'product-name')])"));
        $itemCode = trim($itemXpath->evaluate("string((//div[contains(@class, 'sku')]//span)[1])"));
        $itemPrice = trim($itemXpath->evaluate("string(//span[contains(@class, 'special-price')])"));
        $itemShortDescription = trim($itemXpath->evaluate("string(//div[contains(@class, 'std-short')])"));
        $itemLongDescription = trim($itemXpath->evaluate("string(//div[contains(@class, 'std')])"));
        $itemVideoSource = trim($itemXpath->evaluate("string(//a[contains(@class, 'ytp-impression-link')]/@href)"));

        $results[] = [
            "title" => $itemTitle,
            "code" => $itemCode,
            "price" => $itemPrice,
            "short_description" => $itemShortDescription,
            "long_description" => $itemLongDescription,
            "video_source" => $itemVideoSource,
        ];

        sleep(1);
    }
}

print_r($results[0]);
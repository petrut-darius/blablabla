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

libxml_use_internal_errors(true);

/*
subcategories
$visitedLinks = [];
$results = [];

$row = 1;
if(($file = fopen("../storage/csv/mainCategories.csv", "r")) !== FALSE) {
    while(($data = fgetcsv($file, 51)) !== FALSE) {
        //print_r($data[0] . PHP_EOL);
        
        
        $subCategoryResponse = $crawler->get($data[0]);
        $subCategoryHtml = (string) $subCategoryResponse->getBody();

        $subCategoryDOM = new DOMDocument();
        $subCategoryDOM->loadHTML($subCategoryHtml);
        $subCategoryXpath = new DOMXPath($subCategoryDOM);
    
        //$outputFile = fopen("../storage/csv/subCategories.csv", "a+");
        $subCategoryNodes = $subCategoryXpath->query("//div[contains(@class, 'col-xs-4')]/a[1]");
    
        foreach($subCategoryNodes as $subCategoryNode) {
            $subCategoryLink = $subCategoryNode->getAttribute("href");

            if(in_array($subCategoryLink, $visitedLinks)) {
                continue;
            }else{
                $visitedLinks[] = $subCategoryLink;
            }

            if(!str_starts_with($subCategoryLink, "http")) {
                $subCategoryLink = "https://www.bebebliss.ro" . $subCategoryLink;
            }

            $results[] = [
                (string) $subCategoryLink
            ];
        }
    }
}
fclose($file);

$outputFile = fopen("../storage/csv/subCategories.csv", "w+");
foreach($results as $result) {
    fputcsv($outputFile, $result);
}
fclose($outputFile);
*/


/*
//mainCategories

$mainResponse = $crawler->get("https://www.bebebliss.ro/");
$mainHtml = (string) $mainResponse->getBody();

$doc = new DOMDocument();
$doc->loadHTML($mainHtml);
$xpath = new DOMXPath($doc);


$visitedProducts = [];
$results = [];

$mainCategoryNodes = $xpath->query("//li[contains(@class, 'menu-full-width')]/a[1]");
$file = fopen("../storage/csv/mainCategories.csv", "w+");

foreach($mainCategoryNodes as $mainCategoryNode) {
    $categoryLink = $mainCategoryNode->getAttribute("href");
    
    if(in_array($categoryLink, $visitedProducts)) {
        continue;
    }else{
        $visitedProducts[] = $categoryLink;
    }

    if(!str_starts_with($categoryLink, "http")) {
        $categoryLink = "https://www.bebebliss.ro/" . $categoryLink;
    }

    $results[] = [
        (string) $categoryLink
    ];
}

foreach ($results as $result) {
    fputcsv($file, $result);
}
fclose($file);
*/
<?php
require __DIR__ . "/../vendor/autoload.php";

use Dom\Mysql;
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

try{
    $connection = mysqli_connect("127.0.0.1", "root", "", "Scraper");
}catch(Throwable $e) {
    print_r($e->getMessage());
}

//subcategories
$visitedLinks = [];
$results = [];

if($mainCategories = mysqli_query($connection, "SELECT id, url FROM main_categories")) {
    while($data = mysqli_fetch_assoc($mainCategories)) {
        //print_r($data[0] . PHP_EOL);
        
        echo $data["url"] . PHP_EOL;

        $categoryResponse = $crawler->get($data["url"]);
        $categoryHtml = (string) $categoryResponse->getBody();

        $categoryDOM = new DOMDocument();
        $categoryDOM->loadHTML($categoryHtml);
        $categoryXpath = new DOMXPath($categoryDOM);
    
        //$outputFile = fopen("../storage/csv/subCategories.csv", "a+");
        $subCategoryNodes = $categoryXpath->query("//div[contains(@class, 'col-xs-4')]/a[1]");
    
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

            $subCategoryResponse = $crawler->get($subCategoryLink);
            $subCategoryHtml = (string) $subCategoryResponse->getBody();

            $subCategoryDOM = new DOMDocument();
            $subCategoryDOM->loadHTML($subCategoryHtml);
            $subCategoryXPath = new DOMXPath($subCategoryDOM);

            $pageTitleNode = $subCategoryXPath->query("//div[contains(@class, 'page-title')]/h1[1]");
            $pageInfoNode = $subCategoryXPath->query("//div[@id='category-description']");

            $pageTitle = $pageTitleNode->item(0);
            $pageTitleHtml = $subCategoryDOM->saveHTML($pageTitle);

            $pageInfoHtml = "";
            if($pageInfoNode->length > 0) {
                $pageInfo = $pageInfoNode->item(0);
                
                foreach($pageInfo->childNodes as $child) {
                    $pageInfoHtml .= $subCategoryDOM->saveHTML($child);
                }
            }


            $results[] = [
                (int) $data["id"],
                (string) trim($subCategoryLink),
                (string) trim($pageTitleHtml),
                (string) trim($pageInfoHtml),
            ];
        }
    }
}

foreach($results as $result) {
    $id   = (int)$result[0];
    $url  = mysqli_real_escape_string($connection, $result[1]);
    $name = mysqli_real_escape_string($connection, $result[2]);
    $info = mysqli_real_escape_string($connection, $result[3]);

    $sql = "INSERT INTO `sub_categories` (main_categories_id, url, name, info) 
            VALUES ('$id', '$url', '$name', '$info')";

    mysqli_query($connection, $sql);
}
echo "okey" . PHP_EOL;
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

    $categoryResponse = $crawler->get($categoryLink);
    $categoryHtml = (string) $categoryResponse->getBody();
    $categoryDoc = new DOMDOCUMENT();
    $categoryDoc->loadHTML($categoryHtml);
    $categoryXPath = new DOMXPath($categoryDoc);

    $pageTitleNode = $categoryXPath->query("//div[contains(@class, 'page-title')]/h1[1]");
    $pageInfoNode = $categoryXPath->query("//div[@id='category-description']");

    $pageTitle = $pageTitleNode->item(0);
    $pageTitleHtml = $categoryDoc->saveHTML($pageTitle);

    $pageInfoHtml = "";
    if($pageInfoNode->length > 0) {
        $pageInfo = $pageInfoNode->item(0);
        
        foreach($pageInfo->childNodes as $child) {
            $pageInfoHtml .= $categoryDoc->saveHTML($child);
        }
    }


    $results[] = [
        (string) trim($categoryLink),
        (string) trim($pageTitleHtml),
        (string) trim($pageInfoHtml),
    ];
}


foreach ($results as $result) {
    mysqli_query($connection, "INSERT INTO `main_categories` (name, url, info) VALUES ('" . $result[1] . "', '" . $result[0]. "', '" . $result[2]. "' )");
}
echo "okey" . PHP_EOL;
*/
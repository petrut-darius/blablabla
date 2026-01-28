<?php
require __DIR__ . "/../vendor/autoload.php";

use Nesk\Puphpeteer\Puppeteer;
use Nesk\Rialto\Data\JsFunction;

try{
    $connection = mysqli_connect("127.0.0.1", "root", "", "Scraper");
}catch(Throwable $e) {
    print_r($e->getMessage());
}

$puppeteer = new Puppeteer();
$browser = $puppeteer->launch([
    "headless" => false,
    "args" => [
        "--no-sandbox",
        "--disable-setuid-sandbox",
    ]
]);

/*
//subcategories
$visitedLinks = [];
$results = [];

if($mainCategories = mysqli_query($connection, "SELECT id, url FROM main_categories")) {
    while($data = mysqli_fetch_assoc($mainCategories)) {
        //print_r($data[0] . PHP_EOL);
        
        echo $data["url"] . PHP_EOL;
    
        $page = $browser->newPage();
        $page->goto($data["url"]);
        $page->waitForSelector("div.col-xs-4 > a");
        
        $subCategoryLinks = $page->evaluate(JsFunction::createWithBody("
            return Array.from(
                document.querySelectorAll('div.col-xs-4 > a')
            ).map(a => a.href);
        "));


        foreach($subCategoryLinks as $subCategoryLink) {

            if(isset($visitedLinks[$subCategoryLink])) {
                continue;
            }else{
                $visitedLinks[$subCategoryLink] = true;
            }

            if(!str_starts_with($subCategoryLink, "http")) {
                $subCategoryLink = "https://www.bebebliss.ro" . $subCategoryLink;
            }

            //echo $subCategoryLink . PHP_EOL;

            $page->goto($subCategoryLink, [
                "waitUntil" => "networkidle0"
            ]);

            $page->waitForSelector(".page-title > h1");
            
            $pageData = $page->evaluate(JsFunction::createWithBody("
                const titleElement = document.querySelector('.page-title h1');
                const infoElement = document.querySelector('#category-description');

                return {
                    title: titleElement ? titleElement.innerText.trim() : null,
                    info : infoElement ? infoElement.innerHTML.trim() : null
                }
            "));

            $results[] = [
                (int) $data["id"],
                (string) trim($subCategoryLink),
                (string) trim($pageData["title"]),
                (string) trim($pageData["info"]),
            ];
        }
        $page->close();
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
*/













/*
//mainCategories

$page = $browser->newPage();
$page->goto("https://www.bebebliss.ro");
$page->waitForSelector("li.menu-full-width > a");

$visitedCategories = [];
$results = [];

$mainCategoryLinks = $page->evaluate(JsFunction::createWithBody("
    return Array.from(
        document.querySelectorAll('li.menu-full-width > a')
    ).map(a => a.href);
"));

foreach($mainCategoryLinks as $categoryLink) {
    
    if(isset($visitedCategories[$categoryLink])) {
        continue;
    }else{
        $visitedCategories[$categoryLink] = true;
    }

    if(!str_starts_with($categoryLink, "http")) {
        $categoryLink = "https://www.bebebliss.ro/" . $categoryLink;
    }

    echo $categoryLink . PHP_EOL;

    $page->goto($categoryLink, [
        "waitUntil" => "networkidle0",
    ]);

    $page->waitForSelector(".page-title h1");

    $data = $page->evaluate(JsFunction::createWithBody("
        const titleElement = document.querySelector('.page-title h1');
        const infoElement = document.querySelector('#category-description');

        return {
            title: titleElement ? titleElement.innerText.trim() : null,
            info : infoElement ? infoElement.innerHTML.trim() : null
        }
    "));

    $results[] = [
        (string) trim($categoryLink),
        (string) trim($data["title"]),
        (string) trim($data["info"]),
    ];
}

foreach ($results as $result) {
    mysqli_query($connection, "INSERT INTO `main_categories` (name, url, info) VALUES ('" . $result[1] . "', '" . $result[0]. "', '" . $result[2]. "' )");
}
echo "okey" . PHP_EOL;

$browser->close();
*/
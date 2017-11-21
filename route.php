<?php
$requestUri= parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);

$basePath=dirname(dirname(__FILE__));


$filePath = $basePath.$requestUri;

$ext = explode(".",$filePath);

$ext = strtolower(end($ext));


if(!is_file($filePath))
{
    http_response_code(404);

    exit();
}

include "mimes.php";

if(in_array($ext,["png","jpg","jpeg","gif"]) && !empty($_GET["w"]) && is_numeric($_GET["w"]) && !empty($_GET["h"]) && is_numeric($_GET["h"]))
{
    $destFilePath = explode("/",$filePath);

    $destFileName = array_pop($destFilePath);

    $destFilePath = implode("/",$destFilePath);

    $destFilePath = $destFilePath."/{$_GET["w"]}_{$_GET["h"]}_{$destFileName}";

    if(!file_exists($destFilePath))
    {
        include "ImageResize.php";

        $image = new ImageResize($filePath);

        $image->crop($_GET["w"],$_GET["h"]);

        $image->save($destFilePath);

        header('Location: '.$_SERVER['REQUEST_URI']);

        exit();

    }


    $filePath = $destFilePath;
}




header("Content-type: {$mime_types[$ext]}");

readfile($filePath);
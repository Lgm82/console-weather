<?php

/////////////////////////////
//                         //
//          TO DO          //
//                         //
/////////////////////////////

//[] Readme.MD
//[] Test PHPUnit

/////////////////////////////
/////////////////////////////

namespace ConsoleWeather;

error_reporting(E_ERROR);

include 'vendor/autoload.php';

$app = new App();
$response = $app->makeRequest($argc, $argv);

echo $response;

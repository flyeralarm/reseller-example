<?php
// Use the autoload provided from composer
include_once '../vendor/autoload.php';
// Use our little config array
include '../config/default.php';

// FLYERALARM Reseller Api Factory
use flyeralarm\ResellerApi\client\Factory as ApiClientFactory;
use flyeralarm\ResellerApi\client\Api as ApiClient;
// Use the Api Config for Germany
use flyeralarm\ResellerApi\config\TestDE as Config;
// Just a very simple Object to put our response data
use flyeralarm\ResellerApiExample\view\Response as Response;



// This is the orderID (you get this as a response from sendFullOrder)
// please replace it with a valid id.
$orderID = 'DE123456789';

if($orderID !== 'DE123456789') {
    /**
     * API Calls
     * The following part contains the way we interact with the PHP API Binding.
     */

// Get Api Config object and set Api key.
    $config = new Config();
    $config->setAppToken($api_parameters['app_token'])
        ->setUserToken($api_parameters['user_token'])
        ->setResellerUserEmail($api_parameters['email'])
        ->setResellerUserPassword($api_parameters['pw']);


// Get client factory and client based on the config.
    $factory = new ApiClientFactory($config);
    $faApi = $factory->createClient();


    $filePath = __DIR__ . '/../resources/test_flyer_a5.pdf';
    $fileName = 'test_fyler_a5.pdf';
    $fileSize = filesize($filePath);


    $uploadPath = $faApi->createUploadTarget($fileName, $fileSize, $orderID);

    $faApi->uploadFileByPaths($uploadPath, $filePath);


}
/**
 * HTML Output
 * And now we will generate some output for the user to see:
 */

// Some very simple response object, your framework probably provides something more clever.
$response = new Response();
$response->setHeader("Upload printing data");

if($orderID !== 'DE123456789') {
// order status
    $response->addLine('UploadPath:  <pre>' . print_r($uploadPath, true) . '</pre>');
}else{
    $response->addLine('Please change the $orderID variable of this php script to a valid id.');
}

// Format our response with some template
include '../src/view/template/main.phtml';
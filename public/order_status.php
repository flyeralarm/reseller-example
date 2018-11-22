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

use flyeralarm\ResellerApi\productCatalog\Group as ProductGroup;


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
    $config = new Config();
    $config->setAppToken($api_parameters['app_token'])
        ->setUserToken($api_parameters['user_token'])
        ->setResellerUserEmail($api_parameters['email'])
        ->setResellerUserPassword($api_parameters['pw']);


// Get client factory and client based on the config.
    $factory = new ApiClientFactory($config);
    $faApi = $factory->createClient();

    $orderStatus = $faApi->getOrderStatus($orderID);


}
/**
 * HTML Output
 * And now we will generate some output for the user to see:
 */

// Some very simple response object, your framework probably provides something more clever.
$response = new Response();
$response->setHeader("Check order status");


if($orderID !== 'DE123456789') {
    // order status
    $response->addLine('All available attributes:  <pre>'.print_r($orderStatus,true).'</pre>' );
}else{
    $response->addLine('Please change the $orderID variable of this php script to a valid id.');
}


// Format our response with some template
include '../src/view/template/main.phtml';
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




/**
 * API Calls
 * The following part contains the way we interact with the PHP API Binding.
 */

// Get Api Config object and set Api key.
$config = new Config();
$config ->setAppToken($api_parameters['app_token'])
    ->setUserToken($api_parameters['user_token'])
    ->setResellerUserEmail($api_parameters['email'])
    ->setResellerUserPassword($api_parameters['pw']);


// Get client factory and client based on the config.
$factory = new ApiClientFactory($config);
$faApi = $factory->createClient();

$string = "eJzlU8Fy2yAQ/RXNXut4AMu2xM0f0FySTi+9bGAVM4NAAeTG9eTfu8h1M+2l9/T29vHeso9hL/DiQPeq7XabdgW5cCVXMEUEfYEN6I0QXEu1Bd1JIdS+FvtrUR09uxfJDvRuAazcLkC1oFXLHrYIxX2FYv5tBehr80zBUqrIxHHCcAYNsILnXzSM6InrwaVc7nEkpj7jKzMe34k5F0ojhsA8WpsoZ6YPfkhk7+7jE/m7h5Lw2yyEHaiR3bvuYC3oMHtf4+Zioq0d+73YCxYZV+pAX6tzMOnH05yeKx3nUA+uPh9NHfLW5RgDSTZ9avt+I9vdtmuB41ry7kTp/B9EdeEUnaEPn7R+4iMvB29M5I0BCfW+6+7k2d3MFV3AYsHHhCEPlB7PUx1onnxEy6Y/ztwSX6m1lGslZNeIXre9VuJvIb2W+iBziSMWZ5ovS7vm5LA5TG7N8kSso2Don1qOkvA2sPn+Gx2v6O0nJChF+Q==";

// Create an order Object ... we will step by step fill it with data.

$order = $factory->createOrder();
$order->loadByPersistencyString($string);


// ## Submit order ##
//$orderResult = $faApi->sendFullOrder($order);



/**
 * HTML Output
 * And now we will generate some output for the user to see:
 */

// Some very simple response object, your framework probably provides something more clever.
$response = new Response();
$response->setHeader("Order a flyer using an order template via Reseller API");


if( isset($orderResult) ){
    $response->addLine('Order done:  <pre>'.print_r($orderResult,true).'</pre>' );
}else{
    $response->addLine('No order has been sent.' );
    $response->addLine('For your security the command "sendFullOrder" is commented out.' );
    $response->addLine('Activate the command in this script to send the order to the server.' );
}



// Format our response with some template
include '../src/view/template/main.phtml';


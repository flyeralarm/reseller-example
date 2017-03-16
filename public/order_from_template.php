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

$string = "eJzlU0FSwzAM/EpGV9KOnaRp41sfABfKcOGixg71jGMH2yktnf4dOaV04AlwW620sta2TvCmQTRFtarLKocQKeI5DA5BnKAEUTJGcUM1E6hB1BNYgFhMoKhAFBVjRQ6cFaRmBfHnHNCkFkFZqXxCresHtEcQADm8ftHQo1EUd9qH+IC9IuoeD8QYvBFjiMr3aC3xKKVXIRC9Np1XcvbgtsrMHqPHl5Ex2amMr251aylB2NGYZCrE1snUsVmyJaOiVsc00HNSdq3/2I7+NdFutClx0RnXpiGvXXbOKk6iu6ppSl7Vi1UFZFcqo/fKH/+BVW33TrfqzztNn3hHK0B74WgvgEM677IhYdRXcUInkBhx49GGTvnNcUgDjYNxKEn0I6cn+7yes3JeML7MeCmKheDL34XqENOFjNH1GHWbPU3tsr3GbD3oeXoJj9ch2vdvtLug8yfaVjHZ";

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


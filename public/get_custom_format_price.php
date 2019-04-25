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

// Create an order Object ... we will step by step fill it with data.

$order = $factory->createOrder();

// Load product from the api.
// check find_product.php to know how we found this quantityId.
$product = $faApi->findProductByQuantityId(11119750);

$order->setProduct($product);

$order->setCustomWidth(200);
$order->setCustomHeight(330);

$netPrice = $faApi->getCurrentNetPrice($order);
$grossPrice = $faApi->getCurrentGrossPrice($order);

$string = $order->getPersistencyString();

/**
 * HTML Output
 * And now we will generate some output for the user to see:
 */

// Some very simple response object, your framework probably provides something more clever.
$response = new Response();
$response->setHeader("Order a flyer via Reseller API");

// Which product will we oder?
$response->addLine('Chosen Product:  <pre>'.(string)$product.'</pre>' );

$response->addLine('Net price:  <pre>'.(string)$netPrice.'</pre>' );

$response->addLine('Gross price:  <pre>'.(string)$grossPrice.'</pre>' );

$response->addLine('Persistency String:  <pre>'.(string)$string.'</pre>' );


// Format our response with some template
include '../src/view/template/main.phtml';


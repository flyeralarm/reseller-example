<?php

// Use the autoload provided from composer
include_once '../vendor/autoload.php';
// Use our little config array
include '../config/default.php';

// FLYERALARM Reseller Api Factory
use flyeralarm\ResellerApi\client\Factory as ApiClientFactory;
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


// Load all product groups from the api.
$product_groups = $faApi->getProductGroupIds();




/**
 * HTML Output
 * And now we will generate some output for the user to see:
 */

// Some very simple response object, your framework probably provides something more clever.
$response = new Response();
$response->setHeader("Load available product groups from Reseller API");


// Note that the ProductGroupList object is countable ...
$response->addLine('<strong>Found '.count($product_groups).' product group ids:</strong>');

// And the ProductGroupList is also traversable ...
foreach( $product_groups as $group ){
    /**
     * @var ProductGroup $group
     */
    $group = $group;
    $response->addLine('#'.$group->getProductGroupId().' - '.$group->getName() .' - <img src="'.$group->getImageURL().'" width="50px" height="50px" />' );
}


// Format our response with some template
include '../src/view/template/main.phtml';


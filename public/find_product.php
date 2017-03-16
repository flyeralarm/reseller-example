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
/**
 * @var $faApi ApiClient
 */
$faApi = $factory->createClient();


// Load all product groups from the api.
$product_groups = $faApi->getProductGroupIds();



// We want flyers!
foreach( $product_groups as $group ){

    $group = $group;
    // Search for product_group_id = 5756
    // Alternatively we could be checking for name.
    // But we pulled the catalog in the past so we know the id.
    if($group->getProductGroupId() == 5756){
        $product_group = $group;
        break; // We can stop searching!
    }
}


$attributeList = $faApi->getProductAttributesByProductGroup($product_group);

// Set 4184|Ausführung -> 21019|DIN-Format
$attributeList->getById(4184)->setSelection(
    $attributeList->getById(4184)->getPossibleValues()->getById(21019)
);

// Set A#4185|Format -> AV#121024|DIN A5
$attributeList->getById(4185)->setSelection(
    $attributeList->getById(4185)->getPossibleValues()->getById(21024)
);

// Check which attributes are possible after we have set some of them
$someAttributeList = $faApi->getAvailableAttributesByPreselectedAttributes($product_group, $attributeList);

// Set A#4186|Material -> 21049|250g Bilderdruck matt
$attributeList->getById(4186)->setSelection(
    $attributeList->getById(4186)->getPossibleValues()->getById(21049)
);

// Set A#4187|Veredelung -> AV#20819|keine Veredelung
$attributeList->getById(4187)->setSelection(
    $attributeList->getById(4187)->getPossibleValues()->getById(20819)
);

// Let's test if our selection is valid
$newAttributeList = $faApi->getAvailableAttributesByPreselectedAttributes($product_group, $attributeList);
// If the returned 'newAttributes' are filled and contain the values we expect
// than our selection was valid.
$valid_attributes = $newAttributeList->hasAttributes();

// Now we ask for the possible options for quantities and shipping (standard,express,overnight)
$quantities = $faApi->getAvailableQuantitiesByAttributes($product_group, $attributeList);

// Check if this product is available in quantity = 100
$chosen_quantity = $quantities->getByQuantity(100);

if( null !== $chosen_quantity ){
    //The chosen quantity is available.
    if($chosen_quantity->hasStandardShipping()){

        // We did it!
        // This is the ID required for adding a product to the cart.
        $quantityID = $chosen_quantity->getStandardShippingOption()->getQuantityID();
    }
}

// And to check if we can use this quantityId:
$product = $faApi->findProductByQuantityId($quantityID);


/**
 * HTML Output
 * And now we will generate some output for the user to see:
 */

// Some very simple response object, your framework probably provides something more clever.
$response = new Response();
$response->setHeader("Configure a flyer via Reseller API");

// Did we find the fylers?
$response->addLine('Product group:  #'.$product_group->getProductGroupId().' - '.$product_group->getName() .' - <img src="'.$product_group->getImageURL().'" width="50px" height="50px" />' );

$response->addLine('All available attributes:  <pre>'.(string)$attributeList.'</pre>' );

$response->addLine('Available attributes after some are set:  <pre>'.(string)$someAttributeList.'</pre>' );

$response->addLine('Available attributes after all are set:  <pre>'.(string)$newAttributeList.'</pre>' );

$response->addLine('If a list of available attributes is returned you may continue and the selections this far are valid:  <pre>'.($valid_attributes?'valid':'NOT valid').'</pre>' );

$response->addLine('Chosen QuantityID:  <b>'.$quantityID.'</b>' );

$response->addLine('Chosen Product:  <pre>'.(string)$product.'</pre>' );

// Format our response with some template
include '../src/view/template/main.phtml';


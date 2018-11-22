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
$product = $faApi->findProductByQuantityId(9248634);

$order->setProduct($product);



// ## Choose Shippment Type ##
$shippingTypes = $faApi->getShippingTypeList($product);
// Use express shippment ... Depending on the Shipping Type other Options are possible
$shippingType = $shippingTypes->getByName( 'standard' );
$order->setShippingType( $shippingType );




// ## Chose Product Options ##
$options = $faApi->getAvailableProductOptions($order);


// Set all options to default values ....
/**
 * @var $option flyeralarm\ResellerApi\productCatalog\ProductOption
 */
foreach ($options as $option){
    /**
     * @var $value flyeralarm\ResellerApi\productCatalog\ProductOptionValue
     */
    foreach($option->getPossibleValues() as $value){
        if($value->getBruttoPrice() == 0 ){

            $options->getById( $option->getOptionId() )->setSelection(
                $options->getById( $option->getOptionId() )->getPossibleValues()->getById( $value->getOptionValueId() )
            );
            break;
        }
    }
}

// Set a specific option if needed...
// O#6|Ecken abrunden -> OV#6001|Nein
$options->getById(6)->setSelection(
    $options->getById(6)->getPossibleValues()->getById(6001)
);


$order->setProductOptions($options);



// ## Choose Shipment Options ##
// The Shipping Options can also be set after the product options.
$shippingOptions = $faApi->getAvailableShippingOptions( $order );

$order->setShippingOption( $shippingOptions->getById(1) );

// Set Address
$al = $factory->createAddressList();
$al->getSender()->setCompany('')
    ->setGender('male')
    ->setFirstName('Max')
    ->setLastName('Mustermann')
    ->setAddress('Alfred-Nobel-Straße 18')
    ->setPostcode('97070')
    ->setCity('Würzburg')
    ->setPhone1('+4993146584');

$al->setDelivery( $al->getSender() ); // For this test we use the same address for all 3 fields.
$al->setInvoice( $al->getSender() ); // For this test we use the same address for all 3 fields.
// Set Shipping Options (Carrier, Pickup, etc.)

$order->setAddressList($al);
$order->setAddressHandlingUseSenderFromAddressList();




// ## Chose Payment ##
$paymentOptions = $faApi->getAvailablePaymentOptions($order);
$order->setPaymentOption( $paymentOptions->getById(1) );

// ## Chose how the printing data will be provided ##
$uploadInfo = $factory->createUploadInfo('Automatic Upload via Api.');
$order->setUploadInfo($uploadInfo);


// ## Submit order ##
//$orderResult = $faApi->sendFullOrder($order);


// This is a persistency string that allows you to save the current order as a template for later orders.
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

$response->addLine('Shipping Types:  <pre>'.(string)$shippingTypes.'</pre>' );

$response->addLine('Options:  <pre>'.(string)$options.'</pre>' );

$response->addLine('Shipping:  <pre>'.(string)$shippingOptions.'</pre>' );

$response->addLine('Payment:  <pre>'.(string)$paymentOptions.'</pre>' );

$response->addLine('Persistency String:  <pre>'.(string)$string.'</pre>' );


if( isset($orderResult) ){
    $response->addLine('Order done:  <pre>'.print_r($orderResult,true).'</pre>' );
}else{
    $response->addLine('No order has been sent.' );
    $response->addLine('For your security the command "sendFullOrder" is commented out.' );
    $response->addLine('Activate the command in this script to send the order to the server.' );
}



// Format our response with some template
include '../src/view/template/main.phtml';


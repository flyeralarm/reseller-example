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


$quantityID = (int)$_GET['quantityID'];

if($quantityID){
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

    $string = "eJzlU8FO6zAQ/JVory9UdpKmiW/9ALgA4vIuxt60lhw72E6hVPz7W6cUBJd3h9vs7Mx6J8qe4MmA6Kuma+umhJio4iVMXoI4QQ2iZoxqXq1BdJyxalNCT4aFbUG0C6DmegFVA6JqSEYWVtEoVhH/VoK0eV5EpzFkpPw4SXcEAVDC7p2GUVqkejAhphs5IlHX8oUYKz+JOSYMo3SOeKl1wBiJ3tohoL668Y9or25TkH9nxvSABe8+dVutQbjZ2pwwJuV1nthv2IaRSJmUF3rIzkGF18c57DLtZ5cbZ5/1Ki95mbL3DjmZ/jR9X/OmXXcNUFyN1hwwHH9BVOMO3ij88UnzT7yne6Aj8XQkwCG/dz6XOJuLOaMTaJnkXZAuDhjujlNeaJ6sl5pMX3pmic/aFetXFeNdwZngG8Hr70J8SfmDzMmPMhlV3C/jioORxXYyK5IHJB06hf/VUpQgLwur5w+0P6O3f/B4Q4M=";

    // Create an order Object ... we will step by step fill it with data.

    $order = $factory->createOrder();
    $order->loadByPersistencyString($string);


    $product = $faApi->findProductByQuantityId($quantityID);
    $order->setProduct($product);

    $options = $faApi->getAvailableProductOptions($order);

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

    $order->setProductOptions($options);


    // ## Submit order ##
    $orderResult = $faApi->sendFullOrder($order);

}

/**
 * HTML Output
 * And now we will generate some output for the user to see:
 */

// Some very simple response object, your framework probably provides something more clever.
$response = new Response();
$response->setHeader("Order Quantity ID using an order template via Reseller API");


if( isset($orderResult) ){
    $response->addLine('Order done:  <pre>'.print_r($orderResult,true).'</pre>' );
}else{
    $response->addLine('No order has been sent.' );
    $response->addLine('Did you use a valid quantityID?' );
}



// Format our response with some template
include '../src/view/template/main.phtml';


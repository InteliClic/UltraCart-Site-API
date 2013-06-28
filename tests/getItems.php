<?php
try {
    
    include_once('../includes/config/api.php');
    $uc = new UltraCart_Site();
    
    $vars = array('baseball');
    $vars = array('url' => 'https://secure.ultracart.com/catalog/DEMO/rest_demo/');
    
    echo "<pre>";
    print_r($uc->getItems($vars));
    echo "</pre>";
    
} catch (Exception $error) {
    echo "<pre>";
    print_r($error);
    echo "</pre>";
}
?>


<?php
try {
    
    include_once('../includes/config/api.php');
    $uc = new UltraCart_Site();
    
    $itemId = 'baseball';
    
    echo "<pre>";
    print_r($uc->getItem($itemId));
    echo "</pre>";
    
} catch (Exception $error) {
    echo "<pre>";
    print_r($error);
    echo "</pre>";
}
?>


<?php
try {
    
    include_once('../includes/config/api.php');
    $uc = new UltraCart_Site();

    echo "<pre>";
    print_r($uc->unifiedAffiliateCookieScript());
    echo "</pre>";
    
} catch (Exception $error) {
    echo "<pre>";
    print_r($error);
    echo "</pre>";
}
?>


<?php
try {
    
    include_once('../includes/config/api.php');
    $uc = new UltraCart_Site();
    
    /**
     * RESULTS WILL BE EMPTY USING DEMO ACCOUNT
     */

    echo "<pre>";
    print_r($uc->advertisingSources());
    echo "</pre>";
    
} catch (Exception $error) {
    echo "<pre>";
    print_r($error);
    echo "</pre>";
}
?>


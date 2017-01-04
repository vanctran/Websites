<?php	//checkbalance.php

if(isset($_POST['card_number'])
    && isset($_POST['store']))
{
    $card_number = $_POST['card_number'];
    $store = $_POST['store'];
    $last_line = exec("python3 ../../python/" . escapeshellarg($store) . "check.py " . escapeshellarg($card_number));
    echo "$last_line";
}
?>

<?php 

use App\Controller\PaypalController;

header('Content-Type: application/json');

require 'config.php';
require '../src/PaypalController.php';

$paypal = new PaypalController();
$response = $paypal->handlePayment($_POST);

echo $response;

?>
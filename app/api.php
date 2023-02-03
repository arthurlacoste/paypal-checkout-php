<?php 

use App\Controller\PaypalController;

error_reporting(E_ALL);
ini_set('display_errors', 'on');

header('Content-Type: application/json');

require 'config.php';


require '../src/PaypalController.php';

$paypal = new PaypalController();
$response = $paypal->handlePayment($_POST);

echo $response;

?>
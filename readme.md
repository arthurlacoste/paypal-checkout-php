A simple example of how to use PayPal Checkout in PHP.

This straightforward example shows you how to use PayPal Checkout to process a payment.
You only need :
1 - file class ('PayPalController.php') that contains the code to process the payment.
2 - provide the client ID and secret of your PayPal app.

## How to run the sample

1. Clone the repository.

```shell
git clone https://github.com/arthurlacoste/paypal-checkout-php.git
```

2. You have to use PayPal Sandbox to test the sample. You can create a sandbox account [here](https://developer.paypal.com/developer/accounts/create).

3. Create a PayPal app [here](https://developer.paypal.com/developer/applications/create).

4. Provide the client ID and secret of your PayPal app in the file 'app/config.php'.

5. In your API view, handle the payment with the following code :

```php
header('Content-Type: application/json');

require 'config.php';
require '../src/PaypalController.php';

$paypal = new PaypalController();
$response = $paypal->handlePayment($_POST);

echo $response;
```

6. Everything is ready, but you can store the payment in your database by updating handlePayment() method in 'PaypalController.php' file.
   Another simple idea is to send an email to you & the customer with the payment details.

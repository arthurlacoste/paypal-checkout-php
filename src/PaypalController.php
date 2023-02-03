<?php

namespace App\Controller;

class PaypalController
{
    private $clientId;
    private $appSecret;
    private $baseURL;

    public function __construct()
    {

        if(getenv('ENV') == 'DEV') {
            $this->baseURL = "api.sandbox.paypal.com";
        } else {
            $this->baseURL = "api.paypal.com";
        }
        $this->clientId = getenv('PAYPAL_CLIENT_ID');
        $this->appSecret = getenv('PAYPAL_CLIENT_SECRET');
    }

    /**
     * Handle & complet the payment
     * @param array $request
     * @return string json_encode(array)
     */
    public function handlePayment(array $request): string {
        $orderData = $request['orderData'];
        $orderID = $request['orderID'];

        // Verify if the paiement is completed with api call
        $response = $this->showOrder($orderData['id']);
        $response['order_id'] = $orderID;

        $amount_response = $response['purchase_units'][0]['amount']['value'];
        
        if($response['purchase_units'][0]['amount']['currency_code'] != 'EUR') {
            return json_encode(['error' => 'Currency is not EUR']);
        }

        if($response['status'] != 'COMPLETED') {
            return json_encode(['error' => 'Payment status is not completed']);
        }

        if($amount_response != $orderData['purchase_units'][0]['amount']['value']) {
            return json_encode(['error' => 'Amount is not the same']);
        }

        // Save the order in database here

        return json_encode([
            'success' => '1',
            'redirect' => 'thankyou.php'
        ]);

    }

    /**
     * Show the order details
     * @param string $orderId Order ID
     * @return array Response
     */
    private function showOrder($orderId) {
        $accessToken = $this->generateAccessToken();
        $url = "/v2/checkout/orders/{$orderId}";
        $header = [
            "Content-Type: application/json" ,
            "Authorization: Bearer " . $accessToken,
        ];

        $response = $this->makeRequest($url, 'GET', $header);
        return $response;
    }

    /**
     * Generate an access token to use in the request
     * @return string Access token
     */
    private function generateAccessToken() {
        $response = $this->makeRequest(
            '/v1/oauth2/token',
            'POST',
            array( "Accept: application/json" , "Accept-Language: en_US"),);
        return $response["access_token"];
    }

    /**
     * Create a request to PayPal API
     * @param string $url URL to request
     * @param string $request_type Type of request (POST, GET, PUT, DELETE)
     * @param array $header Header of request
     * @param string $body Body of request
     * @return array Response
     */
    private function makeRequest(
        $url,
        $request_type = 'POST',
        $header = array(),
        $body = 'grant_type=client_credentials'
    ) {
        $ch = curl_init();

        curl_setopt_array($ch, array(
            CURLOPT_URL => 'https://' . $this->baseURL . $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $request_type,
            CURLOPT_USERPWD => $this->clientId . ':' . $this->appSecret,
            CURLOPT_POSTFIELDS => $body,
            CURLOPT_HTTPHEADER => $header,
            CURLOPT_SSL_VERIFYPEER => false // set to true in production
        ));

        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            return curl_error($ch);
        }
        curl_close($ch);
        return json_decode($response, true);
    }
}

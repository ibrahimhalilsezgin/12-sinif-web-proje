<?php
/**
 * Iyzico Helper Class (Simple Integration)
 * For production, use $ composer require iyzipay/iyzipay-php
 */
class IyzicoHelper {
    public static function createForm($data) {
        $apiKey = IYZICO_API_KEY;
        $secretKey = IYZICO_SECRET_KEY;
        $baseUrl = IYZICO_BASE_URL;

        // In a real scenario, you'd construct the JSON for Iyzipay API
        // This is a placeholder for the logic.
        // For development, we'll simulate a successful form generation.
        
        $total = 0;
        foreach($data['items'] as $item) $total += $item['price'] * $item['quantity'];

        $request = [
            'locale' => 'tr',
            'conversationId' => $data['order_id'],
            'price' => $total,
            'paidPrice' => $total,
            'currency' => 'TRY',
            'basketId' => 'B' . $data['order_id'],
            'paymentGroup' => 'PRODUCT',
            'callbackUrl' => BASE_URL . 'payment-callback.php',
            'enabledInstallments' => [1, 2, 3, 6, 9],
            'buyer' => [
                'id' => $data['user_id'],
                'name' => $data['name'],
                'surname' => 'User',
                'gsmNumber' => $data['phone'],
                'email' => $data['email'],
                'identityNumber' => '11111111111',
                'lastLoginDate' => date('Y-m-d H:i:s'),
                'registrationDate' => date('Y-m-d H:i:s'),
                'registrationAddress' => $data['address'],
                'ip' => $_SERVER['REMOTE_ADDR'],
                'city' => 'Istanbul',
                'country' => 'Turkey',
                'zipCode' => '34000'
            ],
            'shippingAddress' => [
                'contactName' => $data['name'],
                'city' => 'Istanbul',
                'country' => 'Turkey',
                'address' => $data['address'],
                'zipCode' => '34000'
            ],
            'billingAddress' => [
                'contactName' => $data['name'],
                'city' => 'Istanbul',
                'country' => 'Turkey',
                'address' => $data['address'],
                'zipCode' => '34000'
            ],
            'basketItems' => []
        ];

        foreach($data['items'] as $item) {
            $request['basketItems'][] = [
                'id' => 'BI' . rand(100, 999),
                'name' => $item['name'],
                'category1' => 'General',
                'itemType' => 'PHYSICAL',
                'price' => $item['price']
            ];
        }

        return $request; // In real, this returns the API result
    }
}
?>

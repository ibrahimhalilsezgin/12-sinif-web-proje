<?php
class IyzicoHelper {
    public static function createForm($data) {
        $apiKey = IYZICO_API_KEY;
        $secretKey = IYZICO_SECRET_KEY;
        $baseUrl = IYZICO_BASE_URL;

        // Split Name and Surname
        $nameParts = explode(' ', trim($data['name']));
        $surname = array_pop($nameParts);
        $name = implode(' ', $nameParts);
        if (empty($name)) { $name = $surname; $surname = 'Bey/Hanim'; }

        $totalPrice = 0;
        foreach($data['items'] as $item) $totalPrice += $item['price'] * $item['quantity'];
        
        $formattedPrice = number_format($totalPrice, 2, '.', '');

        $request = [
            'locale' => 'tr',
            'conversationId' => (string)$data['order_id'],
            'price' => $formattedPrice,
            'paidPrice' => $formattedPrice,
            'currency' => 'TRY',
            'basketId' => 'B' . $data['order_id'],
            'paymentGroup' => 'PRODUCT',
            'callbackUrl' => BASE_URL . 'payment-callback.php?id=' . $data['order_id'],
            'enabledInstallments' => [1, 2, 3, 6, 9],
            'buyer' => [
                'id' => (string)$data['user_id'],
                'name' => $name,
                'surname' => $surname,
                'gsmNumber' => !empty($data['phone']) ? $data['phone'] : '+905000000000',
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

        foreach($data['items'] as $id => $item) {
            $itemTotal = number_format($item['price'] * $item['quantity'], 2, '.', '');
            $request['basketItems'][] = [
                'id' => 'BI' . $id,
                'name' => $item['name'] ?? 'Ürün',
                'category1' => 'Genel',
                'itemType' => 'PHYSICAL',
                'price' => $itemTotal 
            ];
        }

        $jsonRequest = json_encode($request);
        $rnd = uniqid();
        
        // Iyzico IYZWSv2 Signature Generation (HEX Format)
        $signature = hash_hmac('sha256', $rnd . $jsonRequest, $secretKey);
        $authorization = 'IYZWSv2 ' . $apiKey . ':' . $signature;

        $ch = curl_init($baseUrl . '/payment/iyzipay/checkoutform/initialize/auth/v3');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonRequest);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: ' . $authorization,
            'x-iyzi-rnd: ' . $rnd
        ]);

        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode($response);
    }
}
?>

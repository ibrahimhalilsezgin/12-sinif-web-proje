<?php
require_once 'includes/db.php';
require_once 'includes/iyzico-helper.php';
check_login();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['full_name'])) {
    $user_id = $_SESSION['user_id'];
    $name = $_POST['full_name'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $cart = $_SESSION['cart'];

    $total = 0;
    foreach($cart as $item) $total += $item['price'] * $item['quantity'];

    // Create order in DB (Pending)
    try {
        $stmt = $db->prepare("INSERT INTO orders (user_id, total_amount, status, full_name, address, phone) VALUES (?, ?, 'pending', ?, ?, ?)");
        $stmt->execute([$user_id, $total, $name, $address, $phone]);
        $order_id = $db->lastInsertId();

        // Save order items
        foreach($cart as $id => $item) {
            $stmt = $db->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
            $stmt->execute([$order_id, $id, $item['quantity'], $item['price']]);
        }

        // Prepare Iyzico Request
        $paymentData = [
            'order_id' => $order_id,
            'user_id' => $user_id,
            'name' => $name,
            'phone' => $phone,
            'email' => $_SESSION['user_email'],
            'address' => $address,
            'items' => $cart
        ];

        // For this demo, we'll simulate the checkout form being ready.
        // In reality, this part would involve calling Iyzico API
        // and echoing the returned HTML <script> for the form.
        
        // Let's redirect to a success page for demo purposes, but in real it's an Iyzico page.
        header("Location: " . BASE_URL . "payment-callback.php?id=" . $order_id . "&status=success");
        exit();

    } catch (PDOException $e) {
        die("Order creation error: " . $e->getMessage());
    }
} else {
    redirect('cart.php');
}
?>

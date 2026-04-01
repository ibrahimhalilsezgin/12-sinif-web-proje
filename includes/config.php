<?php
session_start();

// Database configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'eticaret');
define('DB_USER', 'root');
define('DB_PASS', '123123');

// Iyzico API configuration
define('IYZICO_API_KEY', 'your_api_key');
define('IYZICO_SECRET_KEY', 'your_secret_key');
define('IYZICO_BASE_URL', 'https://sandbox-api.iyzipay.com'); // Use https://api.iyzipay.com for production

// App configuration
define('APP_NAME', 'Store PHP');
define('BASE_URL', 'http://localhost:8000/'); // Adjusted to match your running server

// Core functions
function redirect($url) {
    header("Location: " . BASE_URL . $url);
    exit();
}

function check_login($role = null) {
    if (!isset($_SESSION['user_id'])) {
        redirect('login.php');
    }
    if ($role && $_SESSION['user_role'] != $role) {
        if ($_SESSION['user_role'] == 'admin') redirect('admin/dashboard.php');
        elseif ($_SESSION['user_role'] == 'vendor') redirect('vendor/dashboard.php');
        else redirect('index.php');
    }
}

function format_money($amount) {
    return number_format($amount, 2, ',', '.') . ' TL';
}

function slugify($text) {
    $text = strtolower(trim($text));
    $text = preg_replace('/[^a-z0-9-]/', '-', $text);
    $text = preg_replace('/-+/', '-', $text);
    return trim($text, '-');
}

function get_product_image($image, $name) {
    if (!$image) {
        return 'https://placehold.co/600x600/f8fafc/6366f1?text=' . urlencode($name);
    }
    
    // Check if it's a full URL
    if (strpos($image, 'http') === 0) {
        return $image;
    }
    
    return BASE_URL . 'assets/images/' . $image;
}
?>

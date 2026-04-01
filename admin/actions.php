<?php
require_once '../includes/db.php';
check_login('admin');

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];

    if ($action == 'approve_store' && isset($_POST['store_id'])) {
        $stmt = $db->prepare("UPDATE stores SET status = 'active' WHERE id = ?");
        $stmt->execute([$_POST['store_id']]);
    } elseif ($action == 'reject_store' && isset($_POST['store_id'])) {
        $stmt = $db->prepare("UPDATE stores SET status = 'suspended' WHERE id = ?");
        $stmt->execute([$_POST['store_id']]);
    } elseif ($action == 'add_category' && isset($_POST['category_name'])) {
        $name = $_POST['category_name'];
        $slug = slugify($name);
        $stmt = $db->prepare("INSERT INTO categories (category_name, slug) VALUES (?, ?)");
        $stmt->execute([$name, $slug]);
        redirect('admin/categories.php');
        return;
    }

    redirect('admin/dashboard.php');
} else {
    redirect('admin/dashboard.php');
}
?>

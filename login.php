<?php
require_once 'includes/db.php';

if (isset($_SESSION['user_id'])) {
    if ($_SESSION['user_role'] == 'admin') redirect('admin/dashboard.php');
    elseif ($_SESSION['user_role'] == 'vendor') redirect('vendor/dashboard.php');
    else redirect('index.php');
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['email']) && isset($_POST['password'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $db->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_role'] = $user['user_role'];
        $_SESSION['user_email'] = $user['email'];

        if ($user['user_role'] == 'admin') redirect('admin/dashboard.php');
        elseif ($user['user_role'] == 'vendor') redirect('vendor/dashboard.php');
        else redirect('index.php');
    } else {
        $error = "E-posta veya şifre hatalı.";
    }
}

$title = "Giriş Yap";
include 'includes/header.php';
?>

<main class="py-5 bg-light min-vh-100 d-flex align-items-center">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-5 col-lg-4">
                <div class="card p-4 p-md-5 bg-white shadow-xl border-0" style="border-radius: 32px;">
                    <div class="text-center mb-5">
                        <h2 class="fw-bold display-6 mb-3 text-dark">Tekrar Hoş Geldin!</h2>
                        <p class="text-secondary small">Hesabına giriş yaparak keşfetmeye başla.</p>
                    </div>
                    
                    <?php if(isset($error)): ?>
                        <div class="alert alert-danger border-0 rounded-3 text-center mb-4 small fw-semibold">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i><?php echo $error; ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST">
                        <div class="mb-4">
                            <label class="form-label small fw-bold text-dark opacity-75">E-posta Adresi</label>
                            <input type="email" name="email" class="form-control form-control-lg bg-light border-0 rounded-3 text-dark" style="font-size: 0.95rem; padding: 14px;" placeholder="adiniz@example.com" required>
                        </div>
                        <div class="mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <label class="form-label small fw-bold text-dark opacity-75 mb-0">Şifre</label>
                                <a href="#" class="text-primary small fw-bold text-decoration-none">Şifremi Unuttum</a>
                            </div>
                            <input type="password" name="password" class="form-control form-control-lg bg-light border-0 rounded-3 text-dark" style="font-size: 0.95rem; padding: 14px;" placeholder="••••••••" required>
                        </div>
                        <button type="submit" class="btn btn-primary d-block w-100 py-3 shadow-lg fs-5 fw-bold mb-4" style="border-radius: 16px;">Giriş Yap</button>
                        
                        <div class="text-center small">
                            <p class="text-secondary mb-0">Henüz bir hesabın yok mu? <a href="register.php" class="text-primary fw-bold text-decoration-none border-bottom border-primary border-2 pb-1 ms-2">Hemen Kayıt Ol</a></p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>

<style>
    .shadow-xl { box-shadow: 0 40px 60px -10px rgba(0,0,0,0.06) !important; }
    .form-control:focus { background-color: #fff !important; box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1); border: 1px solid #6366f1 !important; transform: translateY(-2px); }
    .form-control { transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
</style>

<?php include 'includes/footer.php'; ?>

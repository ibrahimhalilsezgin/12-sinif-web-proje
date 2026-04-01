<?php
require_once 'includes/db.php';

if (isset($_SESSION['user_id'])) {
    if ($_SESSION['user_role'] == 'admin') redirect('admin/dashboard.php');
    elseif ($_SESSION['user_role'] == 'vendor') redirect('vendor/dashboard.php');
    else redirect('index.php');
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];

    $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        $error = "Bu e-posta adresi zaten kullanılıyor.";
    } else {
        $stmt = $db->prepare("INSERT INTO users (name, email, password, user_role) VALUES (?, ?, ?, ?)");
        $stmt->execute([$name, $email, $password, $role]);
        $user_id = $db->lastInsertId();

        if ($role == 'vendor') {
            $store_name = $_POST['store_name'] ?: $name . " Mağazası";
            $slug = slugify($store_name);
            $stmt = $db->prepare("INSERT INTO stores (user_id, store_name, slug, status) VALUES (?, ?, ?, 'active')");
            $stmt->execute([$user_id, $store_name, $slug]);
        }

        $_SESSION['user_id'] = $user_id;
        $_SESSION['user_name'] = $name;
        $_SESSION['user_role'] = $role;
        $_SESSION['user_email'] = $email;

        if ($role == 'vendor') redirect('vendor/dashboard.php');
        else redirect('index.php');
    }
}

$title = "Kayıt Ol";
include 'includes/header.php';
?>

<main class="py-5 bg-light min-vh-100 d-flex align-items-center">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="card p-4 p-md-5 bg-white shadow-xl border-0" style="border-radius: 32px;">
                    <div class="text-center mb-5">
                        <h2 class="fw-bold display-6 mb-3 text-dark">Hemen Katıl!</h2>
                        <p class="text-secondary small">Kısa sürede hesabını oluştur ve alışverişe başla.</p>
                    </div>
                    
                    <?php if(isset($error)): ?>
                        <div class="alert alert-danger border-0 rounded-3 text-center mb-4 small fw-semibold">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i><?php echo $error; ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-dark opacity-75">Adınız Soyadınız</label>
                            <input type="text" name="name" class="form-control bg-light border-0 rounded-3" style="padding: 12px;" placeholder="Ad Soyad" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-dark opacity-75">E-posta Adresi</label>
                            <input type="email" name="email" class="form-control bg-light border-0 rounded-3" style="padding: 12px;" placeholder="adiniz@example.com" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-dark opacity-75">Şifre</label>
                            <input type="password" name="password" class="form-control bg-light border-0 rounded-3" style="padding: 12px;" placeholder="••••••••" required>
                        </div>
                        <div class="mb-4">
                            <label class="form-label small fw-bold text-dark opacity-75">Hesap Türü</label>
                            <select name="role" class="form-select bg-light border-0 rounded-3" style="padding: 12px;" onchange="toggleStoreName(this.value)">
                                <option value="customer">Alışveriş Yapacağım (Müşteri)</option>
                                <option value="vendor">Satış Yapacağım (Mağaza)</option>
                            </select>
                        </div>
                        
                        <div id="store-field" class="mb-4 d-none">
                            <label class="form-label small fw-bold text-dark opacity-75">Mağaza Adı</label>
                            <input type="text" name="store_name" class="form-control bg-light border-0 rounded-3" style="padding: 12px;" placeholder="Örn: Butik Store">
                        </div>

                        <button type="submit" class="btn btn-primary d-block w-100 py-3 shadow-lg fs-5 fw-bold mb-4" style="border-radius: 16px;">Kaydı Tamamla</button>
                        
                        <div class="text-center small">
                            <p class="text-secondary mb-0">Zaten bir hesabın var mı? <a href="login.php" class="text-primary fw-bold text-decoration-none border-bottom border-primary border-2 pb-1 ms-2">Giriş Yap</a></p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
function toggleStoreName(role) {
    const field = document.getElementById('store-field');
    if (role === 'vendor') {
        field.classList.remove('d-none');
        field.classList.add('animate-fade-in');
    } else {
        field.classList.add('d-none');
    }
}

// URL param check for role
const urlParams = new URLSearchParams(window.location.search);
if (urlParams.get('role') === 'vendor') {
    document.querySelector('select[name="role"]').value = 'vendor';
    toggleStoreName('vendor');
}
</script>

<style>
    .shadow-xl { box-shadow: 0 40px 60px -10px rgba(0,0,0,0.06) !important; }
    .form-control:focus, .form-select:focus { background-color: #fff !important; box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1); border: 1px solid #6366f1 !important; transform: translateY(-2px); }
    .form-control, .form-select { transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
    .animate-fade-in { animation: fadeIn 0.4s ease; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }
</style>

<?php include 'includes/footer.php'; ?>

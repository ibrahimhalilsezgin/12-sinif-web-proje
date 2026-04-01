<?php
require_once 'includes/db.php';
check_login();

$cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
if (empty($cart)) redirect('index.php');

$total = 0;
foreach($cart as $item) $total += $item['price'] * $item['quantity'];

$title = "Ödeme Bilgileri";
include 'includes/header.php';
?>

<main class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <h1 class="display-5 fw-bold text-dark mb-5 text-center" style="letter-spacing: -2px;">Teslimat ve Ödeme</h1>

            <form action="payment.php" method="POST" class="row g-4">
                <!-- Sol Kolon: Bilgiler -->
                <div class="col-lg-7">
                    <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
                        <h4 class="fw-bold mb-4 d-flex align-items-center">
                            <span class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px; font-size: 0.9rem;">1</span>
                            Alıcı Bilgileri
                        </h4>
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label class="form-label fw-semibold small text-muted text-uppercase">Tam Adınız</label>
                                <input type="text" name="full_name" class="form-control form-control-lg rounded-3 border-light bg-light" 
                                       value="<?php echo $_SESSION['user_name']; ?>" placeholder="Örn: Mert Yılmaz" required>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label fw-semibold small text-muted text-uppercase">Telefon Numaranız</label>
                                <input type="text" name="phone" class="form-control form-control-lg rounded-3 border-light bg-light" 
                                       placeholder="0 (5xx) xxx-xx-xx" required>
                            </div>
                        </div>
                    </div>

                    <div class="card border-0 shadow-sm rounded-4 p-4">
                        <h4 class="fw-bold mb-4 d-flex align-items-center">
                            <span class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px; font-size: 0.9rem;">2</span>
                            Adres Bilgileri
                        </h4>
                        <div class="col-md-12">
                            <label class="form-label fw-semibold small text-muted text-uppercase">Teslimat Adresi</label>
                            <textarea name="address" class="form-control form-control-lg rounded-3 border-light bg-light" 
                                      rows="4" placeholder="Mahalle, sokak, no, daire, il/ilçe..." style="resize:none" required></textarea>
                        </div>
                    </div>
                </div>

                <!-- Sağ Kolon: Özet ve Ödeme -->
                <div class="col-lg-5">
                    <div class="card border-0 shadow-lg rounded-4 p-4 bg-white sticky-top" style="top: 100px; border: 2px solid var(--bs-primary) !important;">
                        <h4 class="fw-bold mb-4">Sipariş Ayarı</h4>
                        
                        <div class="bg-light rounded-3 p-3 mb-4 border border-dashed text-center">
                            <span class="text-muted d-block small mb-1">Toplam Ödenecek Tutar</span>
                            <h2 class="fw-extrabold text-primary mb-0"><?php echo format_money($total); ?></h2>
                        </div>

                        <ul class="list-unstyled mb-4">
                            <li class="d-flex justify-content-between mb-2 small text-muted">
                                <span>Ara Toplam</span>
                                <span><?php echo format_money($total); ?></span>
                            </li>
                            <li class="d-flex justify-content-between mb-2 small text-muted">
                                <span>Kargo ve Hizmet</span>
                                <span class="text-success fw-bold">Ücretsiz</span>
                            </li>
                        </ul>

                        <button type="submit" class="btn btn-primary w-100 py-3 rounded-pill fw-bold fs-5 shadow mb-4">
                            iyzico ile Güvenli Öde
                        </button>

                        <div class="text-center opacity-75">
                            <div class="d-flex justify-content-center align-items-center gap-3 mb-3">
                                <img src="https://www.iyzico.com/assets/images/content/logo.svg" height="28" alt="iyzico">
                                <div class="vr mx-1"></div>
                                <span class="small fw-bold text-muted">256-bit SSL</span>
                            </div>
                            <p class="small text-muted mb-0 px-3">
                                Ödemeniz iyzico güvencesiyle uçtan uca şifrelenir ve korunur.
                            </p>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</main>

<style>
    .fw-extrabold { font-weight: 800; }
    .border-dashed { border-style: dashed !important; }
    .form-control:focus {
        background-color: white;
        border-color: var(--bs-primary);
        box-shadow: 0 0 0 0.25rem rgba(99, 102, 241, 0.1);
    }
</style>

<?php include 'includes/footer.php'; ?>

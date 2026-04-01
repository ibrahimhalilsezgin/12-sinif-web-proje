<?php
require_once 'includes/db.php';
check_login();

$cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
if (empty($cart)) redirect('index.php');

$total = 0;
foreach($cart as $item) $total += $item['price'] * $item['quantity'];
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ödeme Bilgileri | <?php echo APP_NAME; ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body style="background: #f8fafc;">
    <header>
        <div class="container nav-wrapper">
            <a href="index.php" class="logo">STORE<span>.PHP</span></a>
            <div class="nav-links">
                <a href="cart.php" class="nav-item">Sepete Geri Dön</a>
            </div>
        </div>
    </header>

    <main class="container" style="padding: 80px 0;">
        <h1 style="font-size: 2.5rem; font-weight: 800; letter-spacing: -2px; margin-bottom: 40px; color: var(--secondary); text-align: center;">Teslimat ve Ödeme Bilgileri</h1>

        <div style="max-width: 800px; margin: 0 auto;">
            <form action="payment.php" method="POST" class="card" style="padding: 48px; border-radius: 40px; border: 1px solid var(--border);">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 32px; margin-bottom: 32px;">
                    <div>
                        <h3 style="font-size: 1.25rem; font-weight: 800; margin-bottom: 24px;">Alıcı Bilgileri</h3>
                        <div style="margin-bottom: 20px;">
                            <label>Tam Adınız</label>
                            <input type="text" name="full_name" class="input-field" value="<?php echo $_SESSION['user_name']; ?>" placeholder="Örn: Mert Yılmaz" required>
                        </div>
                        <div style="margin-bottom: 20px;">
                            <label>Telefon Numaranız</label>
                            <input type="text" name="phone" class="input-field" placeholder="0 (5xx) xxx-xx-xx" required>
                        </div>
                    </div>

                    <div>
                        <h3 style="font-size: 1.25rem; font-weight: 800; margin-bottom: 24px;">Adres Bilgileri</h3>
                        <div style="margin-bottom: 20px;">
                            <label>Teslimat Adresi</label>
                            <textarea name="address" class="input-field" rows="5" placeholder="Mahalle, sokak, no, daire, il/ilçe..." style="resize:none" required></textarea>
                        </div>
                    </div>
                </div>

                <div style="background: #f1f5f9; padding: 32px; border-radius: 24px; margin-bottom: 32px; border: 1px dashed var(--border);">
                    <div style="display: flex; justify-content: space-between; align-items: center; font-size: 1.4rem; font-weight: 800; color: var(--secondary);">
                        <span>Ödenecek Tutar</span>
                        <span><?php echo format_money($total); ?></span>
                    </div>
                    <p style="font-size: 0.85rem; color: var(--text-muted); margin-top: 5px;">* %0 Kargo ve Hizmet bedeli dahil.</p>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%; border-radius: 20px; padding: 24px; font-size: 1.5rem;">iyzico ile Güvenli Öde →</button>
                <div style="display: flex; justify-content: center; align-items: center; gap: 15px; margin-top: 32px; opacity: 0.5;">
                    <img src="https://www.iyzico.com/assets/images/content/logo.svg" alt="iyzico" style="height: 32px;">
                    <span style="font-family: inherit; font-size: 0.9rem; font-weight: 700;">256-bit SSL Koruma</span>
                </div>
            </form>
        </div>
    </main>
</body>
</html>

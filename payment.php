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
        
        ?>
        <!DOCTYPE html>
        <html lang="tr">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Güvenli Ödeme Sayfası | Modern Store</title>
            <link rel="stylesheet" href="assets/css/style.css">
            <style>
                body { background: #f1f5f9; min-height: 100vh; display: flex; align-items: center; justify-content: center; font-family: 'Inter', sans-serif; }
                .payment-card { background: white; width: 100%; max-width: 480px; padding: 40px; border-radius: 32px; box-shadow: 0 20px 50px -12px rgba(0,0,0,0.1); border: 1px solid #e2e8f0; }
                .card-header { text-align: center; margin-bottom: 30px; }
                .card-header img { height: 28px; opacity: 0.8; }
                .card-header h2 { font-size: 1.5rem; font-weight: 800; margin-top: 15px; color: #1e293b; }
                .form-group { margin-bottom: 20px; }
                .form-group label { display: block; font-size: 0.85rem; font-weight: 700; color: #64748b; margin-bottom: 8px; text-transform: uppercase; letter-spacing: 1px; }
                .card-input { width: 100%; padding: 16px; border: 2px solid #e2e8f0; border-radius: 12px; font-size: 1.1rem; transition: all 0.2s; box-sizing: border-box; font-family: monospace; }
                .card-input:focus { border-color: #6366f1; outline: none; box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1); background: #fcfcff; }
                .row { display: flex; gap: 20px; }
                .pay-btn { width: 100%; background: #6366f1; color: white; padding: 18px; border-radius: 16px; font-size: 1.1rem; font-weight: 700; border: none; cursor: pointer; margin-top: 10px; transition: 0.3s; }
                .pay-btn:hover { background: #4f46e5; transform: translateY(-2px); }
                #loader { display: none; text-align: center; }
                .spinner { width: 40px; height: 40px; border: 4px solid #f3f3f3; border-top: 4px solid #6366f1; border-radius: 50%; animation: spin 1s linear infinite; margin: 0 auto 20px; }
                @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
            </style>
        </head>
        <body>
            <div id="payment-form" class="payment-card">
                <div class="card-header">
                    <img src="https://www.iyzico.com/assets/images/content/logo.svg" alt="iyzico">
                    <h2>Kredi veya Banka Kartı</h2>
                </div>

                <form onsubmit="initiatePayment(event)">
                    <div class="form-group">
                        <label>Kart Üzerindeki İsim</label>
                        <input type="text" class="card-input" placeholder="AD SOYAD" style="font-family: inherit;" required>
                    </div>
                    <div class="form-group">
                        <label>Kart Numarası</label>
                        <input type="text" id="ccn" class="card-input" placeholder="XXXX XXXX XXXX XXXX" maxlength="19" required>
                    </div>
                    <div class="row">
                        <div class="form-group" style="flex: 1;">
                            <label>Son Kullanma</label>
                            <input type="text" id="exp" class="card-input" placeholder="AA / YY" maxlength="7" required>
                        </div>
                        <div class="form-group" style="flex: 1;">
                            <label>CVV / CVC</label>
                            <input type="text" id="cvv" class="card-input" placeholder="***" maxlength="3" required>
                        </div>
                    </div>
                    <button type="submit" class="pay-btn">
                        <?php echo format_money($total); ?> Güvenli Öde
                    </button>
                </form>
            </div>

            <div id="loader" class="payment-card">
                <div class="spinner"></div>
                <h3>Ödeme İşleniyor...</h3>
                <p style="color: #64748b;">Lütfen bekleyiniz, banka onayı alınıyor.</p>
            </div>

            <script>
                // Formatiing Logic
                const ccn = document.getElementById('ccn');
                const exp = document.getElementById('exp');
                const cvv = document.getElementById('cvv');

                // Card Number Mask (XXXX XXXX XXXX XXXX)
                ccn.addEventListener('input', function (e) {
                    let v = e.target.value.replace(/\s+/g, '').replace(/[^0-9]/gi, '');
                    let matches = v.match(/\d{4,16}/g);
                    let match = matches && matches[0] || '';
                    let parts = [];
                    for (i=0, len=match.length; i<len; i+=4) {
                        parts.push(match.substr(i, 4));
                    }
                    if (parts.length) {
                        e.target.value = parts.join(' ');
                    } else {
                        e.target.value = v;
                    }
                });

                // Expiry Mask (MM / YY)
                exp.addEventListener('input', function (e) {
                    let v = e.target.value.replace(/\s+/g, '').replace(/[^0-9]/gi, '');
                    if (v.length > 2) {
                        e.target.value = v.substr(0, 2) + ' / ' + v.substr(2, 2);
                    } else {
                        e.target.value = v;
                    }
                });

                // CVV Only Numbers
                cvv.addEventListener('input', function (e) {
                    e.target.value = e.target.value.replace(/[^0-9]/gi, '');
                });

                function initiatePayment(e) {
                    e.preventDefault();
                    document.getElementById('payment-form').style.display = 'none';
                    document.getElementById('loader').style.display = 'block';

                    setTimeout(() => {
                        window.location.href = "payment-callback.php?id=<?php echo $order_id; ?>&status=success";
                    }, 3000);
                }
            </script>
        </body>
        </html>
        <?php
        exit();

    } catch (PDOException $e) {
        die("Order creation error: " . $e->getMessage());
    }
} else {
    redirect('cart.php');
}
?>

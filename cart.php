<?php
require_once 'includes/db.php';
$cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
$total = 0;
foreach($cart as $item) $total += $item['price'] * $item['quantity'];
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sepetim | <?php echo APP_NAME; ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body style="background: #f8fafc;">
    <header>
        <div class="container nav-wrapper">
            <a href="index.php" class="logo">STORE<span>.PHP</span></a>
            <div class="nav-links">
                <a href="index.php" class="nav-item">Alışverişe Devam Et</a>
                <a href="cart.php" class="btn btn-outline" style="border-radius: 100px;">🛒 Sepet (<?php echo count($cart); ?>)</a>
            </div>
        </div>
    </header>

    <main class="container" style="padding: 80px 0;">
        <h1 style="font-size: 2.5rem; font-weight: 800; letter-spacing: -2px; margin-bottom: 40px; color: var(--secondary);">Alışveriş Sepeti</h1>

        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 48px;">
            <div class="card" style="padding: 40px; height: fit-content;">
                <?php if(empty($cart)): ?>
                    <div style="text-align: center; padding: 60px;">
                        <div style="font-size: 4rem; margin-bottom: 24px;">🛒</div>
                        <h2 style="margin-bottom: 12px; font-weight: 700;">Sepetin Henüz Boş</h2>
                        <p style="color: var(--text-muted); margin-bottom: 32px;">Aradığın ürünleri bulmak için hemen keşfetmeye başla.</p>
                        <a href="index.php" class="btn btn-primary" style="padding: 16px 40px; font-size: 1rem; border-radius: 100px;">Ürünlere Göz At</a>
                    </div>
                <?php else: ?>
                    <table class="cart-table" style="width: 100%;">
                        <thead>
                            <tr>
                                <th style="padding-bottom: 20px;">Ürün</th>
                                <th style="padding-bottom: 20px;">Fiyat</th>
                                <th style="padding-bottom: 20px; text-align: center;">Adet</th>
                                <th style="padding-bottom: 20px; text-align: right;">Toplam</th>
                                <th style="padding-bottom: 20px;"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($cart as $id => $item): ?>
                            <tr>
                                <td style="padding: 24px 0;">
                                    <div style="display: flex; align-items: center; gap: 20px;">
                                        <div style="width: 72px; height: 72px; background: #f1f5f9; border-radius: 12px; overflow: hidden; flex-shrink: 0;">
                                            <img src="<?php echo $item['image'] ? 'assets/images/'.$item['image'] : 'https://placehold.co/150/f8fafc/6366f1?text=P'; ?>" alt="" style="width: 100%; height: 100%; object-fit: cover;">
                                        </div>
                                        <div style="font-weight: 600; font-size: 1.1rem; color: var(--secondary); max-width: 200px;"><?php echo $item['name']; ?></div>
                                    </div>
                                </td>
                                <td><span style="font-weight: 500; font-size: 1.1rem;"><?php echo format_money($item['price']); ?></span></td>
                                <td style="text-align: center;">
                                    <form action="cart-action.php" method="POST" style="display: inline-flex; align-items: center; gap: 8px; background: #f1f5f9; padding: 6px; border-radius: 12px;">
                                        <input type="hidden" name="product_id" value="<?php echo $id; ?>">
                                        <input type="hidden" name="action" value="update">
                                        <input type="number" name="quantity" value="<?php echo $item['quantity']; ?>" 
                                            style="width: 50px; background: white; border: none; padding: 8px; border-radius: 8px; text-align: center; font-weight: 700; font-family: inherit;" 
                                            onchange="this.form.submit()">
                                    </form>
                                </td>
                                <td style="text-align: right;"><span style="font-weight: 800; font-size: 1.25rem; color: var(--secondary);"><?php echo format_money($item['price'] * $item['quantity']); ?></span></td>
                                <td style="text-align: right; padding-left: 20px;">
                                    <form action="cart-action.php" method="POST">
                                        <input type="hidden" name="product_id" value="<?php echo $id; ?>">
                                        <input type="hidden" name="action" value="remove">
                                        <button type="submit" style="background: none; border: none; color: var(--danger); cursor: pointer; padding: 8px;">
                                            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>

            <aside>
                <div class="card" style="padding: 40px; border-radius: 32px; position: sticky; top: 120px; border: 1px solid var(--primary-light);">
                    <h2 style="font-size: 1.5rem; font-weight: 800; margin-bottom: 24px; letter-spacing: -1px; color: var(--secondary);">Sipariş Özeti</h2>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 16px; font-size: 1.1rem; color: var(--text-muted);">
                        <span>Ara Toplam:</span>
                        <span><?php echo format_money($total); ?></span>
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 32px; border-top: 1px solid var(--border); padding-top: 24px; font-size: 1.6rem; font-weight: 800; color: var(--secondary);">
                        <span>Toplam:</span>
                        <span><?php echo format_money($total); ?></span>
                    </div>
                    <?php if(!empty($cart)): ?>
                        <a href="checkout.php" class="btn btn-primary" style="width: 100%; border-radius: 16px; padding: 20px; font-size: 1.25rem;">Şimdi Öde →</a>
                    <?php endif; ?>
                    <div style="display: flex; justify-content: center; align-items: center; gap: 10px; margin-top: 32px; opacity: 0.6;">
                         <img src="https://www.iyzico.com/assets/images/content/logo.svg" alt="iyzico" style="height: 24px;">
                    </div>
                </div>
            </aside>
        </div>
    </main>
</body>
</html>

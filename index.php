<?php
require_once 'includes/db.php';

// Fetch products for showcase
try {
    $stmt = $db->query("SELECT p.*, s.store_name, c.category_name 
                        FROM products p 
                        JOIN stores s ON p.store_id = s.id 
                        LEFT JOIN categories c ON p.category_id = c.id 
                        WHERE s.status = 'active' 
                        ORDER BY p.id DESC LIMIT 12");
    $products = $stmt->fetchAll();
} catch (PDOException $e) {
    $products = [];
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo APP_NAME; ?> | Modern Alışveriş</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <header>
        <div class="container nav-wrapper">
            <a href="index.php" class="logo">STORE<span>.PHP</span></a>
            <div class="nav-links">
                <a href="index.php" class="nav-item">Keşfet</a>
                <a href="shop.php" class="nav-item">Mağazalar</a>
                <a href="cart.php" class="btn btn-outline" style="border-radius: 100px; padding: 10px 24px;">
                    🛒 Sepet <b>(<?php echo isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0; ?>)</b>
                </a>
                <?php if(isset($_SESSION['user_id'])): ?>
                    <div style="display: flex; align-items: center; gap: 12px; border-left: 1px solid var(--border); padding-left: 20px;">
                        <?php if($_SESSION['user_role'] == 'admin'): ?>
                            <a href="admin/dashboard.php" class="btn btn-primary">Panel</a>
                        <?php elseif($_SESSION['user_role'] == 'vendor'): ?>
                            <a href="vendor/dashboard.php" class="btn btn-primary">Mağaza</a>
                        <?php else: ?>
                            <a href="profile.php" class="btn btn-primary">Profil</a>
                        <?php endif; ?>
                        <a href="logout.php" style="color:var(--danger); font-size: 0.9rem; font-weight: 700;">Çıkış</a>
                    </div>
                <?php else: ?>
                    <a href="login.php" class="nav-item">Giriş</a>
                    <a href="register.php" class="btn btn-primary">Hemen Katıl</a>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <main>
        <div class="hero">
            <div class="container hero-content">
                <h1>Daha Akıllı, <br>Daha <mark>Hızlı</mark> Alışveriş.</h1>
                <p>Türkiye'nin en seçkin butik mağazaları, tek bir platformda. iyzico güvencesi ile sınırsız bir alışveriş deneyimine hazır mısın?</p>
                <div style="display: flex; gap: 16px; justify-content: center;">
                    <a href="#products" class="btn btn-primary" style="padding: 18px 40px; font-size: 1.1rem">Ürünleri Keşfet</a>
                    <a href="register.php?role=vendor" class="btn btn-outline" style="padding: 18px 40px; font-size: 1.1rem">Satış Yapmaya Başla</a>
                </div>
            </div>
        </div>

        <section id="products" class="container" style="padding: 40px 0 120px;">
            <div class="section-header">
                <div>
                    <h2 class="section-title">Yeni Gelenler</h2>
                    <p style="color: var(--text-muted); font-size: 1.1rem;">Sizin için özenle seçilmiş en yeni ürünler.</p>
                </div>
                <a href="shop.php" style="color: var(--primary); font-weight: 700; border-bottom: 2px solid var(--primary);">Tümünü Gör</a>
            </div>

            <div class="product-grid">
                <?php if(empty($products)): ?>
                    <div style="grid-column: 1 / -1; text-align: center; padding: 100px; background: white; border-radius: 32px; border: 2px dashed var(--border);">
                        <div style="font-size: 4rem; margin-bottom: 20px;">📦</div>
                        <h3>Henüz Ürün Eklenmemiş</h3>
                        <p style="color: var(--text-muted);">Admin veya Satıcı panelinden ürün ekleyerek başlayabilirsiniz.</p>
                        <br>
                        <a href="login.php" class="btn btn-primary">Giriş Yap</a>
                    </div>
                <?php else: ?>
                    <?php foreach($products as $product): ?>
                    <div class="product-card">
                        <span class="product-tag"><?php echo $product['category_name'] ?: 'Yeni'; ?></span>
                        <div class="product-image-container">
                            <img src="<?php echo get_product_image($product['image'], $product['product_name']); ?>" alt="<?php echo $product['product_name']; ?>">
                        </div>
                        <div class="product-details">
                            <div class="product-meta"><?php echo $product['store_name']; ?></div>
                            <h3 class="product-name"><?php echo $product['product_name']; ?></h3>
                            
                            <div class="product-footer">
                                <div class="price-tag"><?php echo format_money($product['price']); ?></div>
                                <form action="cart-action.php" method="POST">
                                    <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                    <input type="hidden" name="action" value="add">
                                    <button type="submit" class="add-btn" title="Sepete Ekle">
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </section>
    </main>

    <footer>
        <div class="container" style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 40px; margin-bottom: 60px;">
            <div style="grid-column: span 1;">
                <a href="index.php" class="logo" style="color: white; margin-bottom: 20px;">STORE<span>.PHP</span></a>
                <p style="font-size: 0.9rem;">Güvenilir alışverişin, seçkin butiklerin adresi. iyzico altyapısı ile %100 güvenli ödeme deneyimi.</p>
            </div>
            <div>
                <h4 style="color: white; margin-bottom: 20px;">Hızlı Linkler</h4>
                <ul style="display: grid; gap: 12px; font-size: 0.9rem;">
                    <li><a href="#">Hakkımızda</a></li>
                    <li><a href="#">Kargo Takip</a></li>
                    <li><a href="#">İade Şartları</a></li>
                    <li><a href="#">İletişim</a></li>
                </ul>
            </div>
            <div>
                <h4 style="color: white; margin-bottom: 20px;">Mağaza</h4>
                <ul style="display: grid; gap: 12px; font-size: 0.9rem;">
                    <li><a href="register.php?role=vendor">Satıcı Ol</a></li>
                    <li><a href="login.php">Kullanıcı Girişi</a></li>
                    <li><a href="#">Kampanyalar</a></li>
                </ul>
            </div>
            <div>
                <h4 style="color: white; margin-bottom: 20px;">Haber Bülteni</h4>
                <p style="font-size: 0.85rem; margin-bottom: 16px;">Yeniliklerden haberdar olmak için kaydolun.</p>
                <div style="display: flex; gap: 8px;">
                    <input type="text" placeholder="E-posta" style="background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); padding: 10px; border-radius: 8px; color: white; width: 100%;">
                    <button class="btn btn-primary" style="padding: 10px 15px;">→</button>
                </div>
            </div>
        </div>
        <div class="container" style="border-top: 1px solid rgba(255,255,255,0.1); padding-top: 30px; display: flex; justify-content: space-between; align-items: center; font-size: 0.85rem;">
            <p>&copy; 2026 Store PHP E-Ticaret Sistemi. Tüm Hakları Saklıdır.</p>
            <div style="display: flex; gap: 20px; align-items: center;">
                <img src="https://www.iyzico.com/assets/images/content/logo.svg" alt="iyzico" style="height: 24px; filter: brightness(0) invert(1); opacity: 0.6;">
            </div>
        </div>
    </footer>
</body>
</html>

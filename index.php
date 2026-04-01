<?php
$title = "Hızlı ve Güvenli Alışveriş";
include 'includes/header.php';

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

<main>
    <!-- Hero Section -->
    <div class="hero overflow-hidden py-5" style="background: radial-gradient(circle at top right, #eef2ff 0%, #ffffff 70%); margin-bottom: -150px; padding-bottom: 200px !important;">
        <div class="container text-center py-5">
            <h1 class="display-1 fw-bold text-dark mt-5" style="letter-spacing: -4px; line-height: 0.9;">
                Daha Akıllı, <br>Daha <span class="text-primary highlighted">Hızlı</span> Alışveriş.
            </h1>
            <p class="lead text-secondary mt-4 mb-5 mx-auto" style="max-width: 600px; font-size: 1.3rem;">
                Türkiye'nin en seçkin butik mağazaları, tek bir platformda. iyzico güvencesi ile sınırsız bir alışveriş deneyimine hazır mısın?
            </p>
            <div class="d-flex gap-3 justify-content-center flex-wrap">
                <a href="#products" class="btn btn-primary px-5 py-3 shadow-lg fs-5">Ürünleri Keşfet</a>
                <a href="register.php?role=vendor" class="btn btn-outline-secondary px-5 py-3 fs-5">Satış Yapmaya Başla</a>
            </div>
        </div>
    </div>

    <!-- Features / Stats -->
    <section class="container mb-5 position-relative z-1">
        <div class="row g-4 justify-content-center">
            <div class="col-6 col-md-3">
                <div class="card p-4 text-center bg-white shadow-sm h-100">
                    <h3 class="fw-bold mb-0 text-primary">150+</h3>
                    <p class="text-secondary small mb-0 fw-semibold">Aktif Mağaza</p>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card p-4 text-center bg-white shadow-sm h-100">
                    <h3 class="fw-bold mb-0 text-primary">12k+</h3>
                    <p class="text-secondary small mb-0 fw-semibold">Mutlu Müşteri</p>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card p-4 text-center bg-white shadow-sm h-100 text-nowrap">
                    <h3 class="fw-bold mb-0 text-primary">24/7</h3>
                    <p class="text-secondary small mb-0 fw-semibold">Canlı Destek</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Product Grid -->
    <section id="products" class="container py-5 mt-5">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-end mb-5 gap-3">
            <div>
                <h2 class="display-5 fw-bold text-dark mb-2" style="letter-spacing: -2px;">Yeni Gelenler</h2>
                <p class="text-secondary mb-0">Sizin için özenle seçilmiş en yeni ürünler.</p>
            </div>
            <a href="shop.php" class="text-primary fw-bold text-decoration-none border-bottom border-primary border-2 pb-1">Tümünü Gör →</a>
        </div>

        <div class="row g-4">
            <?php if(empty($products)): ?>
                <div class="col-12 text-center py-5">
                    <div class="card p-5 border-dashed bg-light opacity-75">
                        <div class="display-3 mb-4">📦</div>
                        <h3 class="fw-bold">Henüz Ürün Eklenmemiş</h3>
                        <p class="text-muted mb-4">Yeni ürünler çok yakında burada olacak.</p>
                        <a href="login.php" class="btn btn-primary d-inline-flex px-5 py-2 mx-auto">Giriş Yap</a>
                    </div>
                </div>
            <?php else: ?>
                <?php foreach($products as $product): ?>
                <div class="col-6 col-md-4 col-lg-3">
                    <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden product-premium-card">
                        <!-- Image Area -->
                        <div class="position-relative bg-light">
                            <span class="badge bg-white text-dark shadow-sm rounded-pill py-2 px-3 position-absolute top-0 start-0 m-3 z-2 small fw-bold">
                                <?php echo $product['category_name'] ?: 'Yeni'; ?>
                            </span>
                            <div class="ratio ratio-1x1 overflow-hidden">
                                <img src="<?php echo get_product_image($product['image'], $product['product_name']); ?>" 
                                     class="card-img-top object-fit-cover product-img" 
                                     alt="<?php echo $product['product_name']; ?>">
                            </div>
                        </div>

                        <!-- Content Area -->
                        <div class="card-body p-3 p-md-4 d-flex flex-column">
                            <div class="mb-2">
                                <span class="text-uppercase text-primary fw-bold" style="font-size: 0.65rem; letter-spacing: 1px;">
                                    <?php echo $product['store_name']; ?>
                                </span>
                            </div>
                            <h6 class="card-title fw-bold text-dark mb-3 product-name-clamp">
                                <?php echo $product['product_name']; ?>
                            </h6>
                            
                            <div class="mt-auto pt-3 border-top d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="text-muted d-block small opacity-75">Fiyat</span>
                                    <span class="fw-extrabold fs-5 text-dark"><?php echo format_money($product['price']); ?></span>
                                </div>
                                <form action="cart-action.php" method="POST" class="m-0">
                                    <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                    <input type="hidden" name="action" value="add">
                                    <button type="submit" class="btn btn-primary rounded-circle d-flex align-items-center justify-content-center p-0 shadow-sm buy-btn" style="width: 40px; height: 40px;">
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4Z"></path><line x1="3" y1="6" x2="21" y2="6"></line><path d="M16 10a4 4 0 01-8 0"></path></svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </section>
</main>

<style>
    .product-premium-card {
        transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
    }
    .product-premium-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 30px 60px -12px rgba(50, 50, 93, 0.15), 0 18px 36px -18px rgba(0, 0, 0, 0.2) !important;
    }
    .product-img {
        transition: transform 0.8s cubic-bezier(0.165, 0.84, 0.44, 1);
    }
    .product-premium-card:hover .product-img {
        transform: scale(1.1);
    }
    .product-name-clamp {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        height: 2.8em;
        line-height: 1.4;
        font-size: 1rem;
    }
    .buy-btn {
        transition: all 0.3s ease;
    }
    .buy-btn:hover {
        background: var(--bs-dark);
        border-color: var(--bs-dark);
        transform: rotate(15deg) scale(1.1);
    }
    .fw-extrabold { font-weight: 800; }
    .mt-n5 { margin-top: -3rem !important; }
    @media (max-width: 768px) {
        .product-name-clamp { font-size: 0.9rem; height: 2.6em; }
        .fs-5 { font-size: 1rem !important; }
    }
</style>

<?php include 'includes/footer.php'; ?>

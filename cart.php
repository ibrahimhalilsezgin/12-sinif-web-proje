<?php
require_once 'includes/db.php';
$cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
$total = 0;
foreach($cart as $item) $total += $item['price'] * $item['quantity'];

$title = "Sepetim";
include 'includes/header.php';
?>

<main class="container py-5">
    <div class="row g-5">
        <!-- Sepet Listesi -->
        <div class="col-lg-8">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="fw-bold h2 mb-0">Alışveriş Sepeti</h1>
                <span class="badge bg-light text-dark rounded-pill px-3 py-2 border fw-bold"><?php echo count($cart); ?> Ürün</span>
            </div>

            <?php if(empty($cart)): ?>
                <div class="card border-0 shadow-sm rounded-4 p-5 text-center bg-white">
                    <div class="display-1 mb-4 opacity-25">🛒</div>
                    <h3 class="fw-bold">Sepetin Henüz Boş</h3>
                    <p class="text-muted mb-4 px-lg-5">Aradığın ürünleri bulmak ve sepetine eklemek için hemen mağazamıza göz atabilirsin.</p>
                    <a href="index.php" class="btn btn-primary px-5 py-3 rounded-pill fw-bold">Alışverişe Başla</a>
                </div>
            <?php else: ?>
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light border-0">
                                <tr class="small text-uppercase fw-bold text-muted mt-2">
                                    <th class="py-3 px-4 border-0">Ürün</th>
                                    <th class="py-3 border-0">Fiyat</th>
                                    <th class="py-3 text-center border-0">Adet</th>
                                    <th class="py-3 text-end border-0">Toplam</th>
                                    <th class="py-3 px-4 border-0"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($cart as $id => $item): ?>
                                <tr>
                                    <td class="py-4 px-4">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="rounded-3 overflow-hidden border bg-light" style="width: 80px; height: 80px; flex-shrink: 0;">
                                                <img src="<?php echo get_product_image($item['image'], $item['name']); ?>" class="w-100 h-100 object-fit-cover" alt="">
                                            </div>
                                            <div>
                                                <h6 class="fw-bold mb-0 text-dark"><?php echo $item['name']; ?></h6>
                                                <span class="text-muted small opacity-75">Ürün ID: #<?php echo $id; ?></span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="fw-semibold text-dark"><?php echo format_money($item['price']); ?></td>
                                    <td>
                                        <form action="cart-action.php" method="POST" class="mx-auto" style="width: 120px;">
                                            <input type="hidden" name="product_id" value="<?php echo $id; ?>">
                                            <input type="hidden" name="action" value="update">
                                            <div class="input-group input-group-sm border rounded-pill p-1 bg-light">
                                                <input type="number" name="quantity" class="form-control border-0 bg-transparent text-center fw-bold" 
                                                       value="<?php echo $item['quantity']; ?>" min="1" onchange="this.form.submit()">
                                            </div>
                                        </form>
                                    </td>
                                    <td class="text-end fw-bold text-dark pe-3 h5 mb-0"><?php echo format_money($item['price'] * $item['quantity']); ?></td>
                                    <td class="px-4 text-end">
                                        <form action="cart-action.php" method="POST" class="m-0">
                                            <input type="hidden" name="product_id" value="<?php echo $id; ?>">
                                            <input type="hidden" name="action" value="remove">
                                            <button type="submit" class="btn btn-link text-danger p-0 border-0" title="Kaldır">
                                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"></path><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"></path><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Özet Alanı -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 p-4 sticky-top" style="top: 100px;">
                <h3 class="fw-bold mb-4 h4">Sipariş Özeti</h3>
                
                <div class="d-flex justify-content-between mb-3 text-secondary">
                    <span>Ara Toplam</span>
                    <span class="fw-semibold text-dark"><?php echo format_money($total); ?></span>
                </div>
                <div class="d-flex justify-content-between mb-3 text-secondary">
                    <span>Kargo</span>
                    <span class="text-success fw-bold small text-uppercase">Ücretsiz</span>
                </div>
                
                <hr class="my-4 opacity-10">
                
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <span class="fw-bold h5 mb-0">Toplam</span>
                    <span class="fw-extrabold h4 mb-0 text-primary"><?php echo format_money($total); ?></span>
                </div>

                <?php if(!empty($cart)): ?>
                    <a href="checkout.php" class="btn btn-primary w-100 py-3 rounded-pill fw-bold shadow-sm mb-3">
                        Ödemeye Geç <svg width="18" height="18" class="ms-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M5 12h14m-7-7l7 7-7 7"></path></svg>
                    </a>
                <?php endif; ?>
                
                <a href="index.php" class="btn btn-link w-100 text-muted text-decoration-none small fw-bold">
                    Alışverişe Devam Et
                </a>

                <div class="text-center mt-4 pt-4 border-top">
                    <p class="small text-muted mb-3 opacity-75 fw-semibold">Güvenli Ödeme Alt Yapısı</p>
                    <div class="d-flex justify-content-center align-items-center gap-3">
                        <img src="https://www.iyzico.com/assets/images/content/logo.svg" height="24" alt="iyzico">
                        <img src="https://upload.wikimedia.org/wikipedia/commons/5/5e/Visa_Inc._logo.svg" height="12" alt="visa">
                        <img src="https://upload.wikimedia.org/wikipedia/commons/2/2a/Mastercard-logo.svg" height="20" alt="mastercard">
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<style>
    .fw-extrabold { font-weight: 800; }
    .table > :not(caption) > * > * { border-bottom-width: 0; }
    tbody tr:last-child { border-bottom: 0 !important; }
    .ratio-1x1 img { object-fit: cover; }
    @media (max-width: 768px) {
        .btn-lg { padding: 1rem; }
    }
</style>

<?php include 'includes/footer.php'; ?>

<?php
require_once 'includes/db.php';
check_login(); // Sadece giriş yapmış kullanıcılar görebilir

$user_id = $_SESSION['user_id'];
$title = "Profilim";

// Siparişleri çek (En yeni en üstte)
try {
    $stmt = $db->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
    $stmt->execute([$user_id]);
    $orders = $stmt->fetchAll();
} catch (PDOException $e) {
    $orders = [];
}

// Durum etiketleri için stil ve yazı eşleştirme
$status_map = [
    'pending'   => ['label' => 'Ödeme Bekliyor', 'class' => 'bg-warning text-dark'],
    'paid'      => ['label' => 'Ödendi / Hazırlanıyor', 'class' => 'bg-success'],
    'shipped'   => ['label' => 'Kargoya Verildi', 'class' => 'bg-info'],
    'completed' => ['label' => 'Tamamlandı', 'class' => 'bg-primary'],
    'cancelled' => ['label' => 'İptal Edildi', 'class' => 'bg-danger']
];

include 'includes/header.php';
?>

<div class="bg-dark text-white py-5">
    <div class="container">
        <div class="row align-items-center g-4">
            <div class="col-auto">
                <div class="bg-primary rounded-4 d-flex align-items-center justify-content-center text-white fw-bold display-5 shadow" style="width: 100px; height: 100px;">
                    <?php echo mb_substr($_SESSION['user_name'], 0, 1); ?>
                </div>
            </div>
            <div class="col text-center text-md-start">
                <h1 class="fw-bold mb-1 display-5"><?php echo $_SESSION['user_name']; ?></h1>
                <p class="mb-0 opacity-75 fs-5"><?php echo $_SESSION['user_email']; ?> • Üye Profili</p>
            </div>
        </div>
    </div>
</div>

<main class="container py-5 mt-n5">
    <div class="row g-4">
        <!-- Yan Menü -->
        <aside class="col-lg-3 col-md-4 mt-n4">
            <div class="card shadow-sm border-0 sticky-top p-2" style="top: 100px; border-radius: 20px;">
                <div class="nav flex-column nav-pills gap-2">
                    <a href="profile.php" class="nav-link active rounded-3 py-3 px-4 fw-bold shadow-sm">
                        📦 Siparişlerin
                    </a>
                    <a href="#" class="nav-link text-dark rounded-3 py-3 px-4 fw-semibold opacity-75">
                        👤 Hesap Ayarları
                    </a>
                    <a href="#" class="nav-link text-dark rounded-3 py-3 px-4 fw-semibold opacity-75">
                        📍 Adreslerim
                    </a>
                    <hr class="my-2 mx-3 opacity-10">
                    <a href="logout.php" class="nav-link text-danger rounded-3 py-3 px-4 fw-bold">
                        Çıkış Yap
                    </a>
                </div>
            </div>
        </aside>

        <!-- İçerik -->
        <section class="col-lg-9 col-md-8">
            <div class="card shadow-sm border-0 border-radius-lg overflow-hidden" style="border-radius: 24px;">
                <div class="card-header bg-white border-0 p-4 pb-0">
                    <h2 class="fw-bold text-dark mb-0 h3">Sipariş Geçmişi</h2>
                </div>
                <div class="card-body p-4">
                    <?php if(empty($orders)): ?>
                        <div class="text-center py-5">
                            <div class="display-1 mb-3 opacity-50">🔍</div>
                            <h4 class="fw-bold opacity-75">Henüz siparişiniz yok.</h4>
                            <p class="text-muted mb-4 small">Harika ürünleri keşfetmek için mağazaya göz atın.</p>
                            <a href="index.php" class="btn btn-primary px-4 py-2">Alışverişe Başla</a>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light opacity-50 small border-0 text-uppercase fw-bold">
                                    <tr>
                                        <th class="border-0">No</th>
                                        <th class="border-0">Tarih</th>
                                        <th class="border-0">Toplam</th>
                                        <th class="border-0">Durum</th>
                                        <th class="border-0 text-end">İşlem</th>
                                    </tr>
                                </thead>
                                <tbody class="border-0">
                                    <?php foreach($orders as $order): 
                                        $status = $status_map[$order['status']] ?? $status_map['pending'];
                                    ?>
                                    <tr class="border-bottom border-light">
                                        <td class="py-3"><span class="fw-bold text-primary small">#<?php echo $order['id']; ?></span></td>
                                        <td class="text-muted small"><?php echo date('d.m.Y H:i', strtotime($order['created_at'])); ?></td>
                                        <td class="fw-bold"><?php echo format_money($order['total_amount']); ?></td>
                                        <td>
                                            <span class="badge <?php echo $status['class']; ?> rounded-pill px-3 py-2 small fw-bold">
                                                <?php echo $status['label']; ?>
                                            </span>
                                        </td>
                                        <td class="text-end">
                                            <a href="#" class="btn btn-outline-secondary btn-sm rounded-pill px-3 fw-bold">Detay</a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </section>
    </div>
</main>

<style>
    .mt-n5 { margin-top: -3rem !important; }
    .mt-n4 { margin-top: -2rem !important; }
    .nav-pills .nav-link.active { background-color: var(--bs-primary); }
    .table > :not(caption) > * > * { border-bottom-width: 0; }
    .table-responsive { min-height: 300px; }
</style>

<?php include 'includes/footer.php'; ?>

<?php require_once 'db.php'; ?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($title) ? $title . " | " . APP_NAME : APP_NAME; ?></title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts: Outfit -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/style.css">
    <style>
        body { font-family: 'Outfit', sans-serif; background-color: #f8fafc; }
        .navbar { background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(10px); border-bottom: 1px solid #eee; }
        .logo { font-weight: 800; font-size: 1.5rem; color: #0f172a; text-decoration: none; }
        .logo span { color: #6366f1; }
        .btn-primary { background-color: #6366f1; border-color: #6366f1; font-weight: 600; padding: 10px 24px; border-radius: 12px; }
        .btn-primary:hover { background-color: #4f46e5; border-color: #4f46e5; }
        .card { border-radius: 20px; border: 1px solid #f1f5f9; transition: all 0.3s ease; }
        .card:hover { transform: translateY(-5px); box-shadow: 0 20px 25px -5px rgb(0 0 0 / 0.1); }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg sticky-top">
        <div class="container">
            <a class="logo" href="<?php echo BASE_URL; ?>index.php">STORE<span>.PHP</span></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center gap-2">
                    <li class="nav-item"><a class="nav-link fw-semibold" href="<?php echo BASE_URL; ?>index.php">Keşfet</a></li>
                    <li class="nav-item"><a class="nav-link fw-semibold" href="<?php echo BASE_URL; ?>shop.php">Mağazalar</a></li>
                    <li class="nav-item">
                        <a class="btn btn-outline-secondary rounded-pill px-4" href="<?php echo BASE_URL; ?>cart.php">
                            🛒 Sepet <span class="badge bg-primary rounded-pill"><?php echo isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0; ?></span>
                        </a>
                    </li>
                    <?php if(isset($_SESSION['user_id'])): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle btn btn-primary text-white px-4" href="#" role="button" data-bs-toggle="dropdown">
                                <?php echo explode(' ', $_SESSION['user_name'])[0]; ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg p-2" style="border-radius: 16px;">
                                <?php if($_SESSION['user_role'] == 'admin'): ?>
                                    <li><a class="dropdown-item rounded-3" href="<?php echo BASE_URL; ?>admin/dashboard.php">Panel</a></li>
                                <?php elseif($_SESSION['user_role'] == 'vendor'): ?>
                                    <li><a class="dropdown-item rounded-3" href="<?php echo BASE_URL; ?>vendor/dashboard.php">Mağaza</a></li>
                                <?php else: ?>
                                    <li><a class="dropdown-item rounded-3" href="<?php echo BASE_URL; ?>profile.php">Profil</a></li>
                                <?php endif; ?>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item rounded-3 text-danger fw-bold" href="<?php echo BASE_URL; ?>logout.php">Çıkış Yap</a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-item"><a class="nav-link fw-semibold" href="<?php echo BASE_URL; ?>login.php">Giriş</a></li>
                        <li class="nav-item"><a class="btn btn-primary px-4" href="<?php echo BASE_URL; ?>register.php">Hemen Katıl</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

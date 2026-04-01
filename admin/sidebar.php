<div class="mobile-panel-header d-md-none" style="background: white; padding: 15px 20px; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border); position: sticky; top: 0; z-index: 2500; width: 100%;">
    <a href="../index.php" class="logo" style="font-size: 1.25rem;">STORE<span>.PHP</span></a>
    <button onclick="toggleAdminSidebar()" style="background: var(--secondary); color: white; border: none; padding: 8px 12px; border-radius: 8px; cursor: pointer; display: flex; align-items: center; justify-content: center;">
        <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 6h16M4 12h16m-7 6h7"></path></svg>
    </button>
</div>

<aside class="panel-sidebar" id="adminSidebar">
    <div style="margin-bottom: 40px; padding: 0 20px; display: flex; justify-content: space-between; align-items: center;">
        <div>
            <a href="../index.php" class="logo" style="color: white; font-size: 1.5rem;">STORE<span>.PHP</span></a>
            <p style="font-size: 0.75rem; color: #64748b; margin-top: 5px;">ADMİNİSTRATION</p>
        </div>
        <button class="mobile-toggle d-md-none" onclick="toggleAdminSidebar()" style="background: none; border: none; color: white; cursor: pointer;">
            <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>
    </div>
    <nav>
        <a href="dashboard.php" class="menu-link <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>">
            Dashboard
        </a>
        <a href="stores.php" class="menu-link <?php echo basename($_SERVER['PHP_SELF']) == 'stores.php' ? 'active' : ''; ?>">
            Mağazalar
        </a>
        <a href="categories.php" class="menu-link <?php echo basename($_SERVER['PHP_SELF']) == 'categories.php' ? 'active' : ''; ?>">
            Kategoriler
        </a>
        <a href="users.php" class="menu-link <?php echo basename($_SERVER['PHP_SELF']) == 'users.php' ? 'active' : ''; ?>">
            Kullanıcılar
        </a>
        <a href="orders.php" class="menu-link <?php echo basename($_SERVER['PHP_SELF']) == 'orders.php' ? 'active' : ''; ?>">
            Siparişler
        </a>
        <div style="margin-top: 40px; padding-top: 20px; border-top: 1px solid rgba(255,255,255,0.1);">
            <a href="../logout.php" class="menu-link" style="color: #f87171;">Çıkış Yap</a>
        </div>
    </nav>
</aside>

<script>
function toggleAdminSidebar() {
    const sidebar = document.getElementById('adminSidebar');
    sidebar.classList.toggle('active');
}
</script>

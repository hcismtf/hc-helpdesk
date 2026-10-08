<?php
/**
 * Global Public Header / Navbar Component
 * Reusable across landing page, pusat bantuan, ticket create, etc.
 *
 * @var string $activePage (optional: 'home', 'faq', 'track', default: '')
 */
$activePage = $activePage ?? '';

// User session data
$isLoggedIn  = (bool) session('isLoggedIn');
$userName    = session('name') ?? session('username') ?? 'User';
$employeeNo  = session('employee_no') ?? session('username') ?? '';
$initials    = strtoupper(substr(trim($userName), 0, 2));
$userRole    = session('role');
$roleId      = session('role_id');
$permissions = session('user_permissions') ?? [];
$canAdmin    = \App\Services\AuthService::canAccessAdmin();
?>

<header class="landing-header">
  <div class="header-container">
    <!-- Brand Logo & Title -->
    <a href="<?= base_url('/') ?>" class="header-brand">
      <div class="brand-icon-box">
        <img src="<?= base_url('assets/icons/helpdesk.svg') ?>" alt="HC Helpdesk Icon">
      </div>
      <div class="brand-text">
        <span class="brand-title">HC Helpdesk</span>
        <span class="brand-subtitle">Portal Bantuan Divisi Human Capital</span>
      </div>
    </a>

    <!-- Center Navigation Links -->
    <nav class="header-nav">
      <a href="<?= base_url('/') ?>" class="nav-item <?= $activePage === 'home' ? 'active' : '' ?>">
        <img src="<?= base_url('assets/icons/home.svg') ?>" alt="Beranda">
        <span>Beranda</span>
      </a>
      <a href="<?= base_url('pusat-bantuan') ?>" class="nav-item <?= $activePage === 'faq' ? 'active' : '' ?>">
        <img src="<?= base_url('assets/icons/shield-question.svg') ?>" alt="FAQ & Help Center">
        <span>FAQ & Help Center</span>
      </a>
      <a href="<?= base_url('/#lacak-tiket') ?>" class="nav-item <?= $activePage === 'track' ? 'active' : '' ?>" onclick="focusGlobalTrackInput(event)">
        <img src="<?= base_url('assets/icons/doc-search.svg') ?>" alt="Ticket Status">
        <span>Ticket Status</span>
      </a>
    </nav>

    <!-- Right Action (Profile & Action or Sign In Button) -->
    <div class="header-action">
      <?php if ($isLoggedIn): ?>
        <div class="header-profile-cluster">
          <!-- Profile Badge (Image 2 style: Circular soft avatar, Name, NIK) -->
          <div class="nav-user-profile-wrapper" onclick="toggleGlobalUserDropdown(event)" id="navUserDropdownTrigger" title="Menu Akun">
            <div class="nav-avatar-circle">
              <?= esc($initials) ?>
            </div>
            <div class="nav-user-text">
              <span class="nav-user-name"><?= esc($userName) ?></span>
              <span class="nav-user-nik"><?= !empty($employeeNo) ? 'NIK: ' . esc($employeeNo) : '' ?></span>
            </div>

            <!-- Profile Dropdown Menu -->
            <div class="nav-profile-dropdown" id="globalUserDropdown">
              <div class="nav-dropdown-header">
                <div class="dropdown-avatar-sm"><?= esc($initials) ?></div>
                <div class="dropdown-meta">
                  <div class="dropdown-meta-name"><?= esc($userName) ?></div>
                  <div class="dropdown-meta-nik"><?= !empty($employeeNo) ? 'NIK: ' . esc($employeeNo) : 'Karyawan' ?></div>
                </div>
              </div>
              <div class="nav-dropdown-divider"></div>
              <?php if ($canAdmin): ?>
                <a href="<?= base_url('admin/dashboard') ?>" class="nav-dropdown-item item-admin">
                  <i class="fas fa-user-shield"></i>
                  <span>Menu Admin</span>
                </a>
              <?php endif; ?>
              <a href="<?= base_url('ticket/create') ?>" class="nav-dropdown-item">
                <i class="fas fa-plus-circle"></i>
                <span>Buat Tiket</span>
              </a>
              <div class="nav-dropdown-divider"></div>
              <a href="<?= base_url('auth/logout') ?>" class="nav-dropdown-item item-logout">
                <i class="fas fa-sign-out-alt"></i>
                <span>Keluar</span>
              </a>
            </div>
          </div>

          <!-- Vertical Divider -->
          <div class="nav-header-separator"></div>

          <!-- Create Ticket Button -->
          <a href="<?= base_url('ticket/create') ?>" class="btn-nav-create-ticket">
            <i class="fas fa-plus"></i>
            <span>Buat Tiket Baru</span>
          </a>
        </div>
      <?php else: ?>
        <button type="button" class="btn-sign-in" onclick="openUserLoginModal()">Masuk</button>
      <?php endif; ?>
    </div>

    <!-- Mobile Hamburger Toggle Button -->
    <button type="button" class="btn-mobile-toggle" id="mobileMenuToggle" aria-label="Toggle Navigation" onclick="toggleMobileMenu()">
      <i class="fas fa-bars" id="toggleIcon"></i>
    </button>
  </div>

  <!-- Mobile Dropdown Menu -->
  <div class="mobile-dropdown-menu" id="mobileDropdownMenu">
    <div class="mobile-menu-inner">
      <?php if ($isLoggedIn): ?>
        <div class="mobile-user-card">
          <div class="mobile-avatar"><?= esc($initials) ?></div>
          <div class="mobile-user-details">
            <span class="mobile-user-name"><?= esc($userName) ?></span>
            <span class="mobile-user-nik"><?= !empty($employeeNo) ? 'NIK: ' . esc($employeeNo) : '' ?></span>
          </div>
        </div>
      <?php endif; ?>

      <a href="<?= base_url('/') ?>" class="mobile-nav-item <?= $activePage === 'home' ? 'active' : '' ?>" onclick="closeMobileMenu()">
        <div class="mobile-nav-icon">
          <img src="<?= base_url('assets/icons/home.svg') ?>" alt="Beranda">
        </div>
        <span>Beranda</span>
      </a>
      <a href="<?= base_url('pusat-bantuan') ?>" class="mobile-nav-item <?= $activePage === 'faq' ? 'active' : '' ?>" onclick="closeMobileMenu()">
        <div class="mobile-nav-icon">
          <img src="<?= base_url('assets/icons/shield-question.svg') ?>" alt="FAQ & Help Center">
        </div>
        <span>FAQ & Help Center</span>
      </a>
      <a href="<?= base_url('/#lacak-tiket') ?>" class="mobile-nav-item <?= $activePage === 'track' ? 'active' : '' ?>" onclick="focusGlobalTrackInput(event); closeMobileMenu();">
        <div class="mobile-nav-icon">
          <img src="<?= base_url('assets/icons/doc-search.svg') ?>" alt="Ticket Status">
        </div>
        <span>Ticket Status</span>
      </a>

      <?php if ($isLoggedIn): ?>
        <div class="mobile-divider"></div>
        <a href="<?= base_url('ticket/create') ?>" class="btn-mobile-create-ticket" onclick="closeMobileMenu()">
          <i class="fas fa-plus"></i>
          <span>Buat Tiket Baru</span>
        </a>
        <?php if ($canAdmin): ?>
          <a href="<?= base_url('admin/dashboard') ?>" class="mobile-nav-item item-admin" onclick="closeMobileMenu()">
            <div class="mobile-nav-icon">
              <i class="fas fa-user-shield" style="color: #0F2B5B;"></i>
            </div>
            <span>Menu Admin</span>
          </a>
        <?php endif; ?>
        <a href="<?= base_url('auth/logout') ?>" class="mobile-nav-item logout-link" onclick="closeMobileMenu()">
          <div class="mobile-nav-icon">
            <i class="fas fa-sign-out-alt" style="color: #DC2626;"></i>
          </div>
          <span>Keluar</span>
        </a>
      <?php else: ?>
        <div class="mobile-auth-area">
          <button type="button" class="btn-mobile-login" onclick="closeMobileMenu(); openUserLoginModal();">
            Masuk
          </button>
        </div>
      <?php endif; ?>
    </div>
  </div>
</header>

<script>
  // Global Dropdown Handler
  function toggleGlobalUserDropdown(e) {
    if (e) e.stopPropagation();
    const dd = document.getElementById('globalUserDropdown');
    if (dd) {
      dd.classList.toggle('show');
    }
  }

  // Global Track Input Focus
  function focusGlobalTrackInput(e) {
    const isLanding = window.location.pathname === '/' || window.location.pathname.endsWith('/index.php');
    if (isLanding) {
      if (e) e.preventDefault();
      const target = document.getElementById('lacak-tiket');
      const input = document.getElementById('trackingTicketId');
      if (target) {
        target.scrollIntoView({ behavior: 'smooth' });
        setTimeout(() => { if (input) input.focus(); }, 400);
      }
    }
  }

  // Close dropdown on outside click
  document.addEventListener('click', function(e) {
    const dd = document.getElementById('globalUserDropdown');
    const trigger = document.getElementById('navUserDropdownTrigger');
    if (dd && dd.classList.contains('show') && trigger && !trigger.contains(e.target)) {
      dd.classList.remove('show');
    }
  });

  // Mobile menu handlers
  function toggleMobileMenu() {
    const menu = document.getElementById('mobileDropdownMenu');
    const icon = document.getElementById('toggleIcon');
    if (!menu) return;
    const isOpen = menu.classList.contains('open');
    if (isOpen) {
      menu.classList.remove('open');
      if (icon) { icon.classList.remove('fa-times'); icon.classList.add('fa-bars'); }
    } else {
      menu.classList.add('open');
      if (icon) { icon.classList.remove('fa-bars'); icon.classList.add('fa-times'); }
    }
  }

  function closeMobileMenu() {
    const menu = document.getElementById('mobileDropdownMenu');
    const icon = document.getElementById('toggleIcon');
    if (menu) menu.classList.remove('open');
    if (icon) { icon.classList.remove('fa-times'); icon.classList.add('fa-bars'); }
  }
</script>

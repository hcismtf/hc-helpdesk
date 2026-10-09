<?php
$activePage = $activePage ?? '';
$isLoggedIn = (bool) session('isLoggedIn');
$userName = session('name') ?? session('username') ?? 'User';
$employeeNo = session('employee_no') ?? '';
$initials = strtoupper(substr(trim($userName), 0, 2));
$canAdmin = \App\Services\AuthService::canAccessAdmin();
?>

<nav class="navbar is-app-header" role="navigation" aria-label="main navigation">
  <div class="container is-max-widescreen">

    <!-- Brand -->
    <div class="navbar-brand">
      <a class="navbar-item" href="<?= base_url('/') ?>">
        <div class="brand-icon-box mr-3">
          <img src="<?= base_url('assets/icons/helpdesk.svg') ?>" alt="Helpdesk Icon">
        </div>
        <div>
          <span class="has-text-weight-bold is-block"
            style="color: var(--text-primary); font-size: 1.1rem; line-height: 1.2;">HC Helpdesk</span>
          <span class="is-size-7 has-text-grey">Portal Bantuan Divisi HC</span>
        </div>
      </a>

      <!-- Bulma Standard Mobile Burger -->
      <a role="button" class="navbar-burger" aria-label="menu" aria-expanded="false" data-target="publicNavMenu">
        <span aria-hidden="true"></span>
        <span aria-hidden="true"></span>
        <span aria-hidden="true"></span>
      </a>
    </div>

    <!-- Menu Links -->
    <div id="publicNavMenu" class="navbar-menu">
      <div class="navbar-start ml-auto">
        <a href="<?= base_url('/') ?>" class="navbar-item is-nav-link <?= $activePage === 'home' ? 'is-active' : '' ?>">
          <img src="<?= base_url('assets/icons/home.svg') ?>" class="mr-2" style="width:16px;"> Beranda
        </a>
        <a href="<?= base_url('pusat-bantuan') ?>"
          class="navbar-item is-nav-link <?= $activePage === 'faq' ? 'is-active' : '' ?>">
          <img src="<?= base_url('assets/icons/shield-question.svg') ?>" class="mr-2" style="width:16px;"> FAQ & Help
          Center
        </a>
        <a href="<?= base_url('/#lacak-tiket') ?>"
          class="navbar-item is-nav-link <?= $activePage === 'track' ? 'is-active' : '' ?>" id="navTrackLink">
          <img src="<?= base_url('assets/icons/doc-search.svg') ?>" class="mr-2" style="width:16px;"> Ticket Status
        </a>
      </div>

      <!-- Actions / Profile Area -->
      <div class="navbar-end">
        <div class="navbar-item">
          <?php if ($isLoggedIn): ?>
            <div class="buttons">
              <!-- Create Ticket Button -->
              <a href="<?= base_url('ticket/create') ?>" class="button is-primary is-rounded has-text-weight-semibold">
                <span class="icon is-small"><i class="fas fa-plus"></i></span>
                <span>Buat Tiket Baru</span>
              </a>

              <!-- User Profile Dropdown (Native Bulma) -->
              <div class="navbar-item has-dropdown is-hoverable">
                <a class="navbar-link is-arrowless is-flex is-align-items-center">
                  <div class="avatar-circle mr-2"><?= esc($initials) ?></div>
                  <div class="is-hidden-touch">
                    <span class="is-size-7 has-text-weight-bold is-block"><?= esc($userName) ?></span>
                    <?php if ($employeeNo): ?>
                      <span class="is-size-7 has-text-grey is-block">NIK: <?= esc($employeeNo) ?></span>
                    <?php endif; ?>
                  </div>
                </a>

                <div class="navbar-dropdown is-right is-boxed">
                  <?php if ($canAdmin): ?>
                    <a href="<?= base_url('admin/dashboard') ?>" class="navbar-item has-text-weight-bold">
                      <i class="fas fa-user-shield mr-2"></i> Menu Admin
                    </a>
                  <?php endif; ?>
                  <a href="<?= base_url('ticket/create') ?>" class="navbar-item">
                    <i class="fas fa-plus-circle mr-2"></i> Buat Tiket
                  </a>
                  <hr class="navbar-divider">
                  <a href="<?= base_url('auth/logout') ?>" class="navbar-item has-text-danger">
                    <i class="fas fa-sign-out-alt mr-2"></i> Keluar
                  </a>
                </div>
              </div>
            </div>
          <?php else: ?>
            <button type="button" class="button is-primary has-text-weight-bold" onclick="openUserLoginModal()">
              Masuk
            </button>
          <?php endif; ?>
        </div>
      </div>
    </div>

  </div>
</nav>
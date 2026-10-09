<?= $this->extend('layouts/base') ?>

<?= $this->section('layout_content') ?>

<!-- 1. Top Navbar Global -->
<?= view('components/_public_navbar', ['activePage' => $activePage ?? '']) ?>

<!-- 2. Main Page Content Injection -->
<main class="main-wrapper">
  <?= $this->renderSection('content') ?>
</main>

<!-- 3. Global Footer -->
<footer class="landing-footer py-5 mt-auto">
  <div class="container is-max-widescreen">
    <div class="level is-size-7 has-text-grey">
      <div class="level-left">
        &copy; <?= date('Y') ?> Human Capital Helpdesk Services - PT Mandiri Tunas Finance. All rights reserved.
      </div>
      <div class="level-right">
        <div class="buttons are-small">
          <a href="javascript:void(0)" class="has-text-grey mr-3">Privacy Policy</a>
          <a href="javascript:void(0)" class="has-text-grey">Terms of Service</a>
        </div>
      </div>
    </div>
  </div>
</footer>

<!-- 4. Global Modals untuk Publik -->
<?= view('components/modals/_user_login_modal') ?>
<script src="<?= base_url('assets/js/modules/login-modal.js') ?>"></script>

<?= $this->endSection() ?>
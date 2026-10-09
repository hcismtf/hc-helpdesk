<?= $this->extend('layouts/base') ?>

<?= $this->section('layout_content') ?>

<?= view('components/_admin_navbar') ?>

<div class="columns is-gapless admin-layout-body">
  <!-- Sidebar Admin -->
  <aside class="column is-2 admin-sidebar">
    <?= view('components/_admin_sidebar') ?>
  </aside>

  <!-- Konten Dashboard / Modul Admin -->
  <main class="column is-10 p-5">
    <?= $this->renderSection('content') ?>
  </main>
</div>

<?= $this->endSection() ?>
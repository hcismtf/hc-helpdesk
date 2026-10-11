<?= $this->extend('layouts/public') ?>

<?= $this->section('content') ?>

<div class="user-dashboard-container">
  <!-- 1. Header Greeting & Quick Actions -->
  <?= view('components/user/dashboard/_greeting_header', [
    'userName'    => $userName ?? 'Dimas Pratama',
    'division'    => $division ?? 'Divisi Product & Design',
    'currentDate' => $currentDate ?? '',
  ]) ?>

  <!-- 2. Metrics / Stat Summary Row -->
  <?= view('components/user/dashboard/_stat_cards', [
    'metrics' => $metrics ?? [],
  ]) ?>

  <!-- 3. Main Dashboard 2-Column Grid -->
  <div class="dashboard-main-grid">
    <!-- Left Column: Daftar Tiket Terkini (Filter Tabs, Search, Tickets, Pagination) -->
    <div class="dashboard-primary-column">
      <?= view('components/user/dashboard/_recent_tickets', [
        'tickets'        => $tickets ?? [],
        'totalActive'    => $totalActive ?? 0,
        'displayedCount' => $displayedCount ?? 0,
        'tabCounts'      => $tabCounts ?? [],
      ]) ?>
    </div>

    <!-- Right Column: Sidebar Widgets (Standar SLA Layanan HC & Pusat Bantuan Cepat) -->
    <div class="dashboard-sidebar-column">
      <?= view('components/user/dashboard/_sla_standards', [
        'slaStandards' => $slaStandards ?? [],
      ]) ?>

      <?= view('components/user/dashboard/_quick_help', [
        'quickLinks' => $quickLinks ?? [],
      ]) ?>
    </div>
  </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
  // Sinkronisasi data user dari tabel users ke localStorage (Terenkripsi sebagai 'userRef')
  document.addEventListener('DOMContentLoaded', function () {
    if (typeof CryptoHelper !== 'undefined') {
      var serverUserRef = <?= json_encode($userRefData ?? []) ?>;
      if (serverUserRef && Object.keys(serverUserRef).length > 0) {
        CryptoHelper.setUserRef(serverUserRef);
      }
    }
  });
</script>
<script src="<?= base_url('assets/js/modules/dashboard.js?v=' . time()) ?>"></script>
<?= $this->endSection() ?>

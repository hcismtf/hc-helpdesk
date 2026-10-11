<?= $this->extend('layouts/public') ?>


<?= $this->section('content') ?>

<!-- 1. Hero Search Section -->
<?= view('components/faq/_faq_hero') ?>

<!-- 2. Section: Kategori Topik Permasalahan -->
<?= view('components/faq/_faq_categories') ?>

<!-- 3. Section: FAQ Accordion & Sidebar Hotline -->
<section class="faq-main-section">
  <div class="faq-main-grid">
    <!-- LEFT: Accordion Items (Static + Dynamic DB) -->
    <?= view('components/faq/_faq_accordion', ['dbFaqs' => $dbFaqs ?? [], 'faqs' => $faqs ?? []]) ?>

    <!-- RIGHT: Sidebar Info Cards -->
    <?= view('components/faq/_faq_sidebar') ?>
  </div>
</section>

<!-- 4. Corporate Values (Reusable Global Component) -->
<?= view('components/landing/_corporate_values') ?>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('assets/js/modules/faq.js?v=' . time()) ?>"></script>
<?= $this->endSection() ?>

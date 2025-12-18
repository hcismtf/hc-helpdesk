<?php
// Custom Pager View untuk Ticket Pagination
// Hapus dots dan fix pagination links
$pager_link_open = '<li>';
$pager_link_close = '</li>';
?>
<ul class="pagination">
    <?php if ($pager->hasPreviousPage()): ?>
        <li><a href="<?= $pager->getPreviousPage() ?>">Previous</a></li>
    <?php else: ?>
        <li class="disabled"><span>Previous</span></li>
    <?php endif; ?>

    <?php foreach ($pager->links() as $link): ?>
        <?php if (strpos($link, 'class="active"') !== false): ?>
            <li class="active"><span><?= strip_tags($link) ?></span></li>
        <?php elseif (strpos($link, 'disabled') !== false): ?>
            <li class="disabled"><span>...</span></li>
        <?php else: ?>
            <li><?= $link ?></li>
        <?php endif; ?>
    <?php endforeach; ?>

    <?php if ($pager->hasNextPage()): ?>
        <li><a href="<?= $pager->getNextPage() ?>">Next</a></li>
    <?php else: ?>
        <li class="disabled"><span>Next</span></li>
    <?php endif; ?>
</ul>

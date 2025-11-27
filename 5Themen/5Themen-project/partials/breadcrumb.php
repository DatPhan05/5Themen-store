<?php
/**
 * BREADCRUMB COMPONENT - DÙNG CHUNG*/

if (!isset($breadcrumbs) || !is_array($breadcrumbs)) {
    return; // Không có dữ liệu thì không hiển thị
}
?>

<div class="breadcrumb-section">
    <div class="container">
        <div class="breadcrumb">
            <?php 
            $total = count($breadcrumbs);
            foreach ($breadcrumbs as $index => $item): 
                $isLast = ($index === $total - 1);
            ?>
                <?php if (isset($item['url']) && !$isLast): ?>
                    <a href="<?= htmlspecialchars($item['url']) ?>">
                        <?= htmlspecialchars($item['text']) ?>
                    </a>
                <?php else: ?>
                    <span><?= htmlspecialchars($item['text']) ?></span>
                <?php endif; ?>
                
                <?php if (!$isLast): ?>
                    <span>/</span>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </div>
</div>

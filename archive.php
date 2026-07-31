<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
<?php $this->need('header.php'); ?>

<div class="archive">
  <?php if ($this->is('category')): ?>
    <h1 class="category-title"><span>#</span> <?php $this->archiveTitle(array('category' => '%s'), '', ''); ?></h1>
  <?php elseif ($this->is('tag')): ?>
    <h1 class="tag-title"><span>#</span> <?php $this->archiveTitle(array('tag' => '%s'), '', ''); ?></h1>
  <?php elseif ($this->is('search')): ?>
    <h1 class="archive-title">搜索: <?php $this->archiveTitle(array('search' => '%s'), '', ''); ?></h1>
  <?php elseif ($this->is('author')): ?>
    <h1 class="archive-title">作者: <?php $this->archiveTitle(array('author' => '%s'), '', ''); ?></h1>
  <?php else: ?>
    <h1 class="archive-title">归档</h1>
  <?php endif; ?>

  <?php if ($this->have()): ?>
    <?php while ($this->next()): ?>
      <div class="archive-post">
        <span class="archive-post-date"><?php $this->date('Y-m-d'); ?></span>
        <span class="archive-post-title">
          <a href="<?php $this->permalink(); ?>"><?php $this->title(); ?></a>
        </span>
      </div>
    <?php endwhile; ?>

    <?php
    // Pagination - use Typecho's native pageNav for correct URL generation
    $this->pageNav('&laquo; Prev', 'Next &raquo;', 2, '...', 'nav', 'pagination');
    ?>
  <?php else: ?>
    <p class="no-data">没有找到内容</p>
  <?php endif; ?>
</div>

<?php $this->need('footer.php'); ?>

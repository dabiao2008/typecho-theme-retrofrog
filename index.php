<?php
/**
 * retrofrog  - 复古老玩具主题
 *
 * A Claude-inspired Typecho theme with warm paper texture
 * Converted from hexo-theme-warmpaper
 *
 * @package retrofrog
 * @author  dabiao2008
 * @version 1.0.0
 * @link    https://github.com/dabiao2008/typecho-theme-retrofrog
 */

if (!defined('__TYPECHO_ROOT_DIR__')) exit;
$this->need('header.php');
?>


<?php
/*
// Show profile card only on the first page of the homepage
$isFirstPage = (empty($this->_currentPage) || $this->_currentPage <= 1);
if ($isFirstPage && $this->is('index')):
    $options = $this->options;
    $avatar = $options->profileAvatar ? $options->profileAvatar : $options->themeUrl . '/logo.svg';
    $description = $options->profileDescription;
    $links = warmpaper_get_profile_links();
?>
<div class="profile-card">
  <?php if ($avatar): ?>
    <img class="profile-avatar" src="<?php echo $avatar; ?>" alt="<?php $this->options->title(); ?>">
  <?php endif; ?>
  <h2 class="profile-name"><?php $this->options->title(); ?></h2>
  <?php if ($description): ?>
    <p class="profile-description"><?php echo $description; ?></p>
  <?php endif; ?>
  <?php if (!empty($links)): ?>
    <div class="profile-links">
      <?php foreach ($links as $link): ?>
        <a class="profile-link" href="<?php echo $link['url']; ?>" target="_blank" rel="noopener" title="<?php echo $link['name']; ?>">
          <?php echo warmpaper_render_icon($link['icon']); ?>
          <span><?php echo $link['name']; ?></span>
        </a>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</div>
<?php endif; ?>
 **/
?>

<?php if ($this->have()): ?>
<div class="post-list">
  <?php while ($this->next()): ?>
    <article class="post-card">
      <h2 class="post-card-title">
        <a href="<?php $this->permalink(); ?>"><?php $this->title(); ?></a>
      </h2>
      <div class="post-card-meta">
        <time datetime="<?php $this->date('c'); ?>"><?php $this->date('Y-m-d'); ?></time>
        <?php if ($this->categories): ?>
          <span class="separator">&middot;</span>
          <?php $this->category(', ', false); ?>
        <?php endif; ?>
      </div>
      <div class="post-card-excerpt">
        <p><?php $this->excerpt(200, '...'); ?></p>
      </div>
      <a class="read-more" href="<?php $this->permalink(); ?>"><?php echo $this->options->excerptLink ? $this->options->excerptLink : '阅读全文'; ?> &rarr;</a>
      <?php if ($this->tags): ?>
        <div class="post-card-tags">
          <?php $this->tags('', true, ''); ?>
        </div>
      <?php endif; ?>
    </article>
  <?php endwhile; ?>
</div>

<?php
// Pagination - use Typecho's native pageNav for correct URL generation
$this->pageNav('&laquo; Prev', 'Next &raquo;', 2, '...', 'nav', 'pagination');
?>
<?php else: ?>
<p class="no-data">暂无文章</p>
<?php endif; ?>

<?php $this->need('footer.php'); ?>

<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
<?php $this->need('header.php'); ?>

<?php
// Check for special page slugs (tag cloud, category list, archive listing)
$slug = $this->slug;
if ($slug == 'tags' || $slug == 'tag'):
    // Tag cloud page
    $tags = warmpaper_get_tags();
?>
<div class="archive">
  <h1 class="archive-title">标签</h1>
  <?php if (!empty($tags)): ?>
    <div class="tag-cloud">
      <?php foreach ($tags as $tag): ?>
        <a class="tag-cloud-item" href="<?php echo $tag['url']; ?>">
          #<?php echo $tag['name']; ?>
          <span class="tag-cloud-count"><?php echo $tag['count']; ?></span>
        </a>
      <?php endforeach; ?>
    </div>
  <?php else: ?>
    <p class="no-data">暂无标签</p>
  <?php endif; ?>
</div>

<?php
elseif ($slug == 'categories' || $slug == 'category'):
    // Category list page
    $categories = warmpaper_get_categories();
?>
<div class="archive">
  <h1 class="archive-title">分类</h1>
  <?php if (!empty($categories)): ?>
    <div class="category-list">
      <?php foreach ($categories as $cat): ?>
        <a class="category-list-item" href="<?php echo $cat['url']; ?>">
          <span class="category-list-name"><?php echo $cat['name']; ?></span>
          <span class="category-list-count"><?php echo $cat['count']; ?></span>
        </a>
      <?php endforeach; ?>
    </div>
  <?php else: ?>
    <p class="no-data">暂无分类</p>
  <?php endif; ?>
</div>

<?php
elseif ($slug == 'archives' || $slug == 'archive'):
    // Chronological archive listing
    $posts = warmpaper_get_all_posts();
?>
<div class="archive">
  <h1 class="archive-title">归档</h1>
  <?php if (!empty($posts)): ?>
    <?php $lastYear = null; ?>
    <?php foreach ($posts as $post): ?>
      <?php if ($post['year'] !== $lastYear): ?>
        <h2 class="archive-year"><?php echo $post['year']; ?></h2>
        <?php $lastYear = $post['year']; ?>
      <?php endif; ?>
      <div class="archive-post">
        <span class="archive-post-date"><?php echo $post['month'] . '-' . $post['day']; ?></span>
        <span class="archive-post-title">
          <a href="<?php echo $post['url']; ?>"><?php echo $post['title']; ?></a>
        </span>
      </div>
    <?php endforeach; ?>
  <?php else: ?>
    <p class="no-data">暂无文章</p>
  <?php endif; ?>
</div>

<?php else: ?>
  <!-- Normal independent page -->
  <article class="page-content">
    <h1><?php $this->title(); ?></h1>
    <div class="post-content">
      <?php
      // Capture content for potential TOC/Math processing
      ob_start();
      $this->content();
      $pageContent = ob_get_clean();
      $pageContent = warmpaper_ensure_heading_ids($pageContent);
      echo $pageContent;
      ?>
    </div>

    <?php
    // Comments on pages too
    $commentSystem = $this->options->commentSystem;
    if ($commentSystem == 'giscus'):
    ?>
      <div class="giscus"></div>
      <script>
      (function () {
        var THEME_LIGHT = 'light';
        var THEME_DARK  = 'dark';
        function computeTheme() {
          var attr = document.documentElement.getAttribute('data-theme');
          if (attr === 'dark') return THEME_DARK;
          if (attr === 'light') return THEME_LIGHT;
          return window.matchMedia('(prefers-color-scheme: dark)').matches ? THEME_DARK : THEME_LIGHT;
        }
        var s = document.createElement('script');
        s.src = 'https://giscus.app/client.js';
        s.async = true;
        s.crossOrigin = 'anonymous';
        s.setAttribute('data-repo', '<?php echo $this->options->giscusRepo; ?>');
        s.setAttribute('data-repo-id', '<?php echo $this->options->giscusRepoId; ?>');
        s.setAttribute('data-category', '<?php echo $this->options->giscusCategory; ?>');
        s.setAttribute('data-category-id', '<?php echo $this->options->giscusCategoryId; ?>');
        s.setAttribute('data-mapping', 'pathname');
        s.setAttribute('data-strict', '0');
        s.setAttribute('data-reactions-enabled', '1');
        s.setAttribute('data-emit-metadata', '0');
        s.setAttribute('data-input-position', 'bottom');
        s.setAttribute('data-lang', 'zh-CN');
        s.setAttribute('data-loading', 'lazy');
        s.setAttribute('data-theme', computeTheme());
        document.querySelector('.giscus').appendChild(s);
        function sendTheme() {
          var frame = document.querySelector('iframe.giscus-frame');
          if (!frame) return;
          frame.contentWindow.postMessage(
            { giscus: { setConfig: { theme: computeTheme() } } },
            'https://giscus.app'
          );
        }
        new MutationObserver(sendTheme).observe(document.documentElement, {
          attributes: true,
          attributeFilter: ['data-theme']
        });
        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', sendTheme);
      })();
      </script>
    <?php
    elseif ($commentSystem == 'native'):
        $this->need('comments.php');
    endif;
    ?>

    <?php
    // MathJax for pages
    $mathEnable = in_array('enable', (array)$this->options->mathEnable);
    if ($mathEnable):
    ?>
    <script>
      window.MathJax = {
        tex: {
          inlineMath: [['$', '$'], ['\\(', '\\)']],
          displayMath: [['$$', '$$'], ['\\[', '\\]']],
          processEscapes: true
        },
        options: {
          ignoreHtmlClass: 'toc-sidebar|site-nav',
          enableMenu: false
        }
      };
    </script>
    <script defer
            src="https://cdn.jsdelivr.net/npm/mathjax@4.1.2/tex-mml-chtml.js"
            integrity="sha384-Nfw2UPOp2WaWfAjGXccSL8qKx4KWXN9NUonXtPcotBXKSqHMyNWrahYzDzIXhpTy"
            crossorigin="anonymous"></script>
    <?php endif; ?>
  </article>
<?php endif; ?>

<?php $this->need('footer.php'); ?>

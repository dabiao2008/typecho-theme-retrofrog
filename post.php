<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
<?php $this->need('header.php'); ?>

<article class="post">
  <header class="post-header">
    <h1 class="post-title"><?php $this->title(); ?></h1>
    <div class="post-meta">
      <time datetime="<?php $this->date('c'); ?>"><?php $this->date('Y-m-d'); ?></time>
      <?php if ($this->categories): ?>
        <span class="separator">&middot;</span>
        <?php $this->category(', ', false); ?>
      <?php endif; ?>
    </div>
  </header>

  <div class="post-body-wrapper">
    <?php
    // Capture post content for TOC generation
    ob_start();
    $this->content();
    $postContent = ob_get_clean();

    // Ensure headings have IDs
    $postContent = warmpaper_ensure_heading_ids($postContent);
    ?>

    <div class="post-content">
      <?php echo $postContent; ?>
    </div>

    <?php
    // Generate and display TOC
    $tocEnable = in_array('enable', (array)$this->options->tocEnable);
    if ($tocEnable):
        $tocContent = warmpaper_generate_toc($postContent);
        if (!empty($tocContent)):
    ?>
      <aside class="toc-sidebar">
        <div class="toc-wrapper">
          <h3 class="toc-title">目录</h3>
          <?php echo $tocContent; ?>
        </div>
      </aside>
    <?php
        endif;
    endif;
    ?>
  </div>

  <footer class="post-footer">
    <?php if ($this->tags): ?>
      <div class="post-tags">
        <?php $this->tags(' ', true, ''); ?>
      </div>
    <?php endif; ?>
    <nav class="post-nav">
      <?php
      // Query adjacent posts directly for reliability
      $db = Typecho_Db::get();
      $options = Typecho_Widget::widget('Widget_Options');

      // Previous post (older)
      $prevPost = $db->fetchRow($db->select('cid', 'title', 'slug', 'created')
          ->from('table.contents')
          ->where('type = ?', 'post')
          ->where('status = ?', 'publish')
          ->where('created < ?', $this->created)
          ->order('created', Typecho_Db::SORT_DESC)
          ->limit(1));

      // Next post (newer)
      $nextPost = $db->fetchRow($db->select('cid', 'title', 'slug', 'created')
          ->from('table.contents')
          ->where('type = ?', 'post')
          ->where('status = ?', 'publish')
          ->where('created > ?', $this->created)
          ->order('created', Typecho_Db::SORT_ASC)
          ->limit(1));

      // Build permalinks
      $prevUrl = '';
      $nextUrl = '';
      if ($prevPost) {
          if (class_exists('Typecho_Router')) {
              try {
                  $prevUrl = Typecho_Router::url('post', array('cid' => $prevPost['cid']), $options->index);
              } catch (Exception $e) {
                  $prevUrl = Typecho_Common::url('archives/' . $prevPost['cid'], $options->index);
              }
          } else {
              $prevUrl = Typecho_Common::url('archives/' . $prevPost['cid'], $options->index);
          }
      }
      if ($nextPost) {
          if (class_exists('Typecho_Router')) {
              try {
                  $nextUrl = Typecho_Router::url('post', array('cid' => $nextPost['cid']), $options->index);
              } catch (Exception $e) {
                  $nextUrl = Typecho_Common::url('archives/' . $nextPost['cid'], $options->index);
              }
          } else {
              $nextUrl = Typecho_Common::url('archives/' . $nextPost['cid'], $options->index);
          }
      }
      ?>
      <?php if ($prevPost): ?>
        <span class="post-nav-prev">&laquo; <a href="<?php echo $prevUrl; ?>" title="<?php echo htmlspecialchars($prevPost['title']); ?>"><?php echo htmlspecialchars($prevPost['title']); ?></a></span>
      <?php endif; ?>
      <?php if ($nextPost): ?>
        <span class="post-nav-next"><a href="<?php echo $nextUrl; ?>" title="<?php echo htmlspecialchars($nextPost['title']); ?>"><?php echo htmlspecialchars($nextPost['title']); ?></a> &raquo;</span>
      <?php endif; ?>
    </nav>
  </footer>

  <?php
  // Comments
  $commentSystem = $this->options->commentSystem;
  if ($commentSystem != 'none'):
      if ($commentSystem == 'giscus'):
  ?>
    <div class="giscus"></div>
    <script>
    (function () {
      var THEME_LIGHT = '<?php echo $this->options->giscusThemeLight ? $this->options->giscusThemeLight : "light"; ?>';
      var THEME_DARK  = '<?php echo $this->options->giscusThemeDark ? $this->options->giscusThemeDark : "dark"; ?>';

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
      else:
          // Native Typecho comments
          $this->need('comments.php');
      endif;
  endif;
  ?>

  <?php
  // MathJax
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

<?php $this->need('footer.php'); ?>

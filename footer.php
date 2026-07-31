    </main>
    <footer class="site-footer">
      <div class="footer-inner">
        <?php if ($this->options->copyright): ?>
          <p><?php echo $this->options->copyright; ?></p>
        <?php else: ?>
          <p>&copy; <?php echo date('Y'); ?> <?php $this->options->title(); ?></p>
        <?php endif; ?>
        <p>Powered by <a href="http://typecho.org/" target="_blank" rel="noopener">Typecho</a> &middot; Theme <a href="https://github.com/finch-xu/hexo-theme-warmpaper" target="_blank" rel="noopener">Warmpaper</a></p>
      </div>
    </footer>
  </div>
  <script src="<?php $this->options->themeUrl('main.js'); ?>"></script>
</body>
</html>

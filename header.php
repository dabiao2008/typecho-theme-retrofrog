<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5">
  <title><?php $this->archiveTitle(array(
      'category' => _t('分类 %s 下的文章'),
      'search'   => _t('包含关键字 %s 的文章'),
      'tag'      => _t('标签 %s 下的文章'),
      'author'   => _t('%s 发布的文章')
  ), '', ' | '); ?><?php $this->options->title(); ?></title>
  <?php $this->header(); ?>
  <link rel="icon" href="<?php $this->options->themeUrl('logo.svg'); ?>">
  <link rel="preconnect" href="https://cdn.osyb.cn" crossorigin>
  <link rel="stylesheet" href="https://cdn.osyb.cn/npm/lxgw-wenkai-gb-web@latest/lxgwwenkaigb-regular/result.css">
  <link rel="stylesheet" href="https://cdn.osyb.cn/npm/lxgw-wenkai-gb-web@latest/lxgwwenkaigb-medium/result.css">

  <?php
  $options = $this->options;
  $isMathEnabled = false;
  if ($this->is('single')) {
      $isMathEnabled = in_array('enable', (array)$options->mathEnable) ? true : false;
  }
  if ($isMathEnabled): ?>
    <link rel="stylesheet" href="<?php $this->options->themeUrl('math.css'); ?>">
  <?php endif; ?>

  <?php if ($options->commentSystem == 'giscus'): ?>
    <link rel="stylesheet" href="<?php $this->options->themeUrl('giscus.css'); ?>">
  <?php endif; ?>

  <link rel="stylesheet" href="<?php $this->options->themeUrl('style.css'); ?>">

  <script>
    (function () {
      try {
        var saved = localStorage.getItem('warmpaper-theme');
        if (saved === 'dark' || saved === 'light') {
          document.documentElement.setAttribute('data-theme', saved);
        }
      } catch (e) {}
    })();
  </script>
</head>
<body>
  <div class="site-wrapper">
    <header class="site-header">
      <div class="header-inner">
        <a class="site-title" href="<?php $this->options->siteUrl(); ?>">
          <img class="site-logo" src="<?php $this->options->themeUrl('logo.svg'); ?>" alt="Logo"><?php $this->options->title(); ?>
        </a>
        <button class="nav-toggle" aria-label="Toggle navigation" aria-expanded="false">
          <span class="nav-toggle-icon"></span>
        </button>
        <nav class="site-nav">
          <?php
          $menu = warmpaper_get_nav_menu();
          foreach ($menu as $name => $url):
              // Convert relative URLs
              if (strpos($url, 'http') !== 0 && strpos($url, '//') !== 0) {
                  $url = $this->options->siteUrl . ltrim($url, '/');
              }
          ?>
            <a class="nav-link" href="<?php echo $url; ?>"><?php echo $name; ?></a>
          <?php endforeach; ?>
          <button class="theme-toggle" type="button" aria-label="切换主题" title="切换主题" data-theme-state="light">
            <svg class="icon-sun" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <circle cx="12" cy="12" r="4"/>
              <path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M4.93 19.07l1.41-1.41M17.66 6.34l1.41-1.41"/>
            </svg>
            <svg class="icon-moon" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/>
            </svg>
          </button>
        </nav>
      </div>
    </header>
    <main class="main-content">

<?php
/**
 * retrofrog Theme Functions
 *
 * Converted from hexo-theme-warmpaper by finch-xu
 * https://github.com/finch-xu/hexo-theme-warmpaper
 */

if (!defined('__TYPECHO_ROOT_DIR__')) exit;

/**
 * Theme configuration form
 */
function themeConfig($form)
{
    $profileAvatar = new Typecho_Widget_Helper_Form_Element_Text(
        'profileAvatar',
        NULL,
        '',
        _t('头像地址'),
        _t('个人资料卡片的头像 URL，留空则使用主题默认 logo')
    );
    $form->addInput($profileAvatar);

    $profileDescription = new Typecho_Widget_Helper_Form_Element_Textarea(
        'profileDescription',
        NULL,
        '',
        _t('个人简介'),
        _t('显示在首页个人资料卡片中，支持 HTML')
    );
    $form->addInput($profileDescription);

    $profileLinks = new Typecho_Widget_Helper_Form_Element_Textarea(
        'profileLinks',
        NULL,
        "GitHub|https://github.com/|github\n网站|https://example.com|website",
        _t('个人链接'),
        _t('每行一个，格式：名称|URL|图标类型（可选：github, email, website, twitter, rss, bilibili, zhihu）')
    );
    $form->addInput($profileLinks);

    $navMenu = new Typecho_Widget_Helper_Form_Element_Textarea(
        'navMenu',
        NULL,
        "首页|/\n归档|/archives.html\n标签|/tags.html\n分类|/categories.html\n关于|/about.html",
        _t('导航菜单'),
        _t('每行一个，格式：名称|URL')
    );
    $form->addInput($navMenu);

    $tocEnable = new Typecho_Widget_Helper_Form_Element_Checkbox(
        'tocEnable',
        array('enable' => _t('启用文章目录 (TOC)')),
        array('enable'),
        _t('文章目录')
    );
    $form->addInput($tocEnable);

    $mathEnable = new Typecho_Widget_Helper_Form_Element_Checkbox(
        'mathEnable',
        array('enable' => _t('启用 MathJax 数学公式渲染')),
        array('enable'),
        _t('数学公式')
    );
    $form->addInput($mathEnable);

    $commentSystem = new Typecho_Widget_Helper_Form_Element_Radio(
        'commentSystem',
        array(
            'native' => _t('Typecho 原生评论'),
            'giscus' => _t('Giscus (GitHub Discussions)'),
            'none'   => _t('关闭评论')
        ),
        'native',
        _t('评论系统')
    );
    $form->addInput($commentSystem);

    $giscusRepo = new Typecho_Widget_Helper_Form_Element_Text(
        'giscusRepo',
        NULL,
        '',
        _t('Giscus 仓库'),
        _t('格式：user/repo')
    );
    $form->addInput($giscusRepo);

    $giscusRepoId = new Typecho_Widget_Helper_Form_Element_Text(
        'giscusRepoId',
        NULL,
        '',
        _t('Giscus 仓库 ID'),
        _t('在 https://giscus.app 生成')
    );
    $form->addInput($giscusRepoId);

    $giscusCategory = new Typecho_Widget_Helper_Form_Element_Text(
        'giscusCategory',
        NULL,
        'Announcements',
        _t('Giscus 分类名')
    );
    $form->addInput($giscusCategory);

    $giscusCategoryId = new Typecho_Widget_Helper_Form_Element_Text(
        'giscusCategoryId',
        NULL,
        '',
        _t('Giscus 分类 ID')
    );
    $form->addInput($giscusCategoryId);

    $excerptLink = new Typecho_Widget_Helper_Form_Element_Text(
        'excerptLink',
        NULL,
        '阅读全文',
        _t('摘要链接文字')
    );
    $form->addInput($excerptLink);

    $copyright = new Typecho_Widget_Helper_Form_Element_Text(
        'copyright',
        NULL,
        '',
        _t('页脚版权信息'),
        _t('留空则显示默认版权信息，支持 HTML')
    );
    $form->addInput($copyright);
}

/**
 * Theme initialization
 */
function themeInit($archive)
{
    // Comment reply and other init can be handled here if needed
}

/**
 * Parse profile links from config string
 * Format: name|url|icon (one per line)
 */
function warmpaper_get_profile_links()
{
    $options = Typecho_Widget::widget('Widget_Options');
    $raw = $options->profileLinks;
    if (empty($raw)) return array();

    $links = array();
    $lines = explode("\n", str_replace("\r\n", "\n", $raw));
    foreach ($lines as $line) {
        $line = trim($line);
        if (empty($line)) continue;
        $parts = explode('|', $line, 3);
        if (count($parts) >= 2) {
            $links[] = array(
                'name' => trim($parts[0]),
                'url'  => trim($parts[1]),
                'icon' => isset($parts[2]) ? trim($parts[2]) : 'website'
            );
        }
    }
    return $links;
}

/**
 * Parse navigation menu from config string
 * Format: name|url (one per line)
 */
function warmpaper_get_nav_menu()
{
    $options = Typecho_Widget::widget('Widget_Options');
    $raw = $options->navMenu;
    if (empty($raw)) return array();

    $menu = array();
    $lines = explode("\n", str_replace("\r\n", "\n", $raw));
    foreach ($lines as $line) {
        $line = trim($line);
        if (empty($line)) continue;
        $parts = explode('|', $line, 2);
        if (count($parts) >= 2) {
            $menu[trim($parts[0])] = trim($parts[1]);
        }
    }
    return $menu;
}

/**
 * Generate a URL-friendly slug from text
 */
function warmpaper_slugify($text)
{
    $text = trim($text);
    // Remove HTML tags
    $text = strip_tags($text);
    // Keep alphanumeric, spaces, hyphens, and CJK characters
    $text = preg_replace('/[^\p{L}\p{N}\s\-]/u', '', $text);
    // Replace spaces and hyphens with single hyphen
    $text = preg_replace('/[\s\-]+/u', '-', $text);
    $text = trim($text, '-');
    // Convert to lowercase (for ASCII)
    $text = strtolower($text);
    return $text;
}

/**
 * Ensure all headings in HTML content have id attributes
 */
function warmpaper_ensure_heading_ids($content)
{
    $counter = 0;
    return preg_replace_callback(
        '/<h([1-6])((?:[^>]*?))>(.*?)<\/h\1>/is',
        function ($match) use (&$counter) {
            $level = $match[1];
            $attrs = $match[2];
            $inner = $match[3];

            // Check if id already exists
            if (preg_match('/\bid\s*=\s*["\'][^"\']*["\']/i', $attrs)) {
                return $match[0];
            }

            $title = trim(strip_tags($inner));
            $id = warmpaper_slugify($title);
            if (empty($id)) {
                $id = 'heading-' . (++$counter);
            }

            return '<h' . $level . ' id="' . $id . '"' . $attrs . '>' . $inner . '</h' . $level . '>';
        },
        $content
    );
}

/**
 * Generate a table of contents from post content HTML
 *
 * @param string $content Rendered HTML content
 * @param int $max_depth Maximum heading level to include (default 3)
 * @param int $min_depth Minimum heading level to include (default 2)
 * @return string TOC HTML (empty string if no headings found)
 */
function warmpaper_generate_toc($content, $max_depth = 3, $min_depth = 2)
{
    // Match headings with optional id attributes
    $pattern = '/<h([1-6])[^>]*?(?:\bid\s*=\s*["\']([^"\']*)["\'])?[^>]*>(.*?)<\/h\1>/is';

    if (!preg_match_all($pattern, $content, $matches, PREG_SET_ORDER)) {
        return '';
    }

    $items = array();
    foreach ($matches as $match) {
        $level = (int)$match[1];
        if ($level < $min_depth || $level > $max_depth) continue;

        $id = isset($match[2]) && $match[2] !== '' ? $match[2] : '';
        $title = trim(strip_tags($match[3]));
        if (empty($title)) continue;

        if (empty($id)) {
            $id = 'toc-heading-' . count($items);
        }

        $items[] = array(
            'level' => $level,
            'id'    => $id,
            'title' => $title
        );
    }

    if (empty($items)) return '';

    // Find minimum level and normalize to 0-based depth
    $minLevel = PHP_INT_MAX;
    foreach ($items as $item) {
        if ($item['level'] < $minLevel) $minLevel = $item['level'];
    }

    $html = '<ol class="toc">' . "\n";
    $prevDepth = 0;

    for ($i = 0; $i < count($items); $i++) {
        $depth = $items[$i]['level'] - $minLevel;

        if ($i > 0) {
            if ($depth > $prevDepth) {
                // Going deeper - open nested ol inside previous li
                $html .= "\n<ol>\n";
            } elseif ($depth < $prevDepth) {
                // Going up - close li, then close ol+li for each level
                $html .= "</li>\n";
                for ($d = $prevDepth; $d > $depth; $d--) {
                    $html .= "</ol>\n</li>\n";
                }
            } else {
                // Same level - close previous li
                $html .= "</li>\n";
            }
        }

        $html .= '<li><a href="#' . $items[$i]['id'] . '">'
            . htmlspecialchars($items[$i]['title'], ENT_QUOTES, 'UTF-8')
            . '</a>';
        $prevDepth = $depth;
    }

    // Close the last item
    $html .= "</li>\n";

    // Close all open ol tags
    while ($prevDepth > 0) {
        $html .= "</ol>\n</li>\n";
        $prevDepth--;
    }

    $html .= '</ol>';

    return $html;
}

/**
 * Get all tags as an array for tag cloud
 */
function warmpaper_get_tags()
{
    $db = Typecho_Db::get();
    $prefix = $db->getPrefix();
    $rows = $db->fetchAll(
        $db->select('mid', 'name', 'slug', 'count')
            ->from('table.metas')
            ->where('type = ?', 'tag')
            ->where('count > 0')
            ->order('name', Typecho_Db::SORT_ASC)
    );

    $tags = array();
    foreach ($rows as $row) {
        $tags[] = array(
            'name'  => $row['name'],
            'slug'  => $row['slug'],
            'count' => $row['count'],
            'url'   => warmpaper_tag_url($row['slug'])
        );
    }
    return $tags;
}

/**
 * Get tag URL
 */
function warmpaper_tag_url($slug)
{
    $options = Typecho_Widget::widget('Widget_Options');
    if (class_exists('Typecho_Router')) {
        try {
            return Typecho_Router::url('tag', array('slug' => $slug), $options->index);
        } catch (Exception $e) {}
    }
    return Typecho_Common::url('tag/' . $slug, $options->index);
}

/**
 * Get all categories as an array for category list
 */
function warmpaper_get_categories()
{
    $db = Typecho_Db::get();
    $rows = $db->fetchAll(
        $db->select('mid', 'name', 'slug', 'count')
            ->from('table.metas')
            ->where('type = ?', 'category')
            ->where('count > 0')
            ->order('name', Typecho_Db::SORT_ASC)
    );

    $categories = array();
    foreach ($rows as $row) {
        $categories[] = array(
            'name'  => $row['name'],
            'slug'  => $row['slug'],
            'count' => $row['count'],
            'url'   => warmpaper_category_url($row['slug'])
        );
    }
    return $categories;
}

/**
 * Get category URL
 */
function warmpaper_category_url($slug)
{
    $options = Typecho_Widget::widget('Widget_Options');
    if (class_exists('Typecho_Router')) {
        try {
            return Typecho_Router::url('category', array('slug' => $slug), $options->index);
        } catch (Exception $e) {}
    }
    return Typecho_Common::url('category/' . $slug, $options->index);
}

/**
 * Get all published posts for archive listing
 */
function warmpaper_get_all_posts()
{
    $db = Typecho_Db::get();
    $rows = $db->fetchAll(
        $db->select('cid', 'title', 'slug', 'created', 'text')
            ->from('table.contents')
            ->where('type = ?', 'post')
            ->where('status = ?', 'publish')
            ->order('created', Typecho_Db::SORT_DESC)
    );

    $posts = array();
    $options = Typecho_Widget::widget('Widget_Options');
    foreach ($rows as $row) {
        // Use Typecho router to generate the correct permalink
        $url = '';
        if (class_exists('Typecho_Router')) {
            try {
                $url = Typecho_Router::url('post', array('cid' => $row['cid']), $options->index);
            } catch (Exception $e) {
                $url = '';
            }
        }
        if (empty($url)) {
            $url = Typecho_Common::url('archives/' . $row['cid'], $options->index);
        }
        $posts[] = array(
            'title'   => $row['title'],
            'url'     => $url,
            'year'    => date('Y', $row['created']),
            'month'   => date('m', $row['created']),
            'day'     => date('d', $row['created']),
            'dateStr' => date('Y-m-d', $row['created'])
        );
    }
    return $posts;
}

/**
 * Render profile link icon SVG
 */
function warmpaper_render_icon($icon)
{
    $icons = array(
        'github' => '<svg class="profile-icon" viewBox="0 0 24 24" fill="currentColor"><path d="M12 0C5.37 0 0 5.37 0 12c0 5.31 3.435 9.795 8.205 11.385.6.105.825-.255.825-.57 0-.285-.015-1.23-.015-2.235-3.015.555-3.795-.735-4.035-1.41-.135-.345-.72-1.41-1.23-1.695-.42-.225-1.02-.78-.015-.795.945-.015 1.62.87 1.845 1.23 1.08 1.815 2.805 1.305 3.495.99.105-.78.42-1.305.765-1.605-2.67-.3-5.46-1.335-5.46-5.925 0-1.305.465-2.385 1.23-3.225-.12-.3-.54-1.53.12-3.18 0 0 1.005-.315 3.3 1.23.96-.27 1.98-.405 3-.405s2.04.135 3 .405c2.295-1.56 3.3-1.23 3.3-1.23.66 1.65.24 2.88.12 3.18.765.84 1.23 1.905 1.23 3.225 0 4.605-2.805 5.625-5.475 5.925.435.375.81 1.095.81 2.22 0 1.605-.015 2.895-.015 3.3 0 .315.225.69.825.57A12.02 12.02 0 0024 12c0-6.63-5.37-12-12-12z"/></svg>',
        'email' => '<svg class="profile-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="M22 4L12 13 2 4"/></svg>',
        'website' => '<svg class="profile-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M2 12h20"/><path d="M12 2a15.3 15.3 0 014 10 15.3 15.3 0 01-4 10 15.3 15.3 0 01-4-10 15.3 15.3 0 014-10z"/></svg>',
        'twitter' => '<svg class="profile-icon" viewBox="0 0 24 24" fill="currentColor"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>',
        'rss' => '<svg class="profile-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 11a9 9 0 019 9"/><path d="M4 4a16 16 0 0116 16"/><circle cx="5" cy="19" r="1"/></svg>',
        'bilibili' => '<svg class="profile-icon" viewBox="0 0 24 24" fill="currentColor"><path d="M17.813 4.653h.854c1.51.054 2.769.578 3.773 1.574 1.004.995 1.524 2.249 1.56 3.76v7.36c-.036 1.51-.556 2.769-1.56 3.773s-2.262 1.524-3.773 1.56H5.333c-1.51-.036-2.769-.556-3.773-1.56S.036 18.858 0 17.347v-7.36c.036-1.511.556-2.765 1.56-3.76 1.004-.996 2.262-1.52 3.773-1.574h.774l-1.174-1.12a1.234 1.234 0 01-.373-.906c0-.356.124-.658.373-.907l.027-.027c.267-.249.573-.373.92-.373.347 0 .653.124.92.373L9.653 4.44c.071.071.134.142.187.213h4.267a.836.836 0 01.16-.213l2.853-2.747c.267-.249.573-.373.92-.373.347 0 .662.151.929.4.267.249.391.551.391.907 0 .355-.124.657-.373.906zM5.333 7.24c-.746.018-1.373.276-1.88.773-.506.498-.769 1.13-.786 1.894v7.52c.017.764.28 1.395.786 1.893.507.498 1.134.756 1.88.773h13.334c.746-.017 1.373-.275 1.88-.773.506-.498.769-1.129.786-1.893v-7.52c-.017-.765-.28-1.396-.786-1.894-.507-.497-1.134-.755-1.88-.773zM8 11.107c.373 0 .684.124.933.373.25.249.383.569.4.96v1.173c-.017.391-.15.711-.4.96-.249.25-.56.374-.933.374s-.684-.125-.933-.374c-.25-.249-.383-.569-.4-.96V12.44c.017-.391.15-.711.4-.96.249-.249.56-.373.933-.373zm8 0c.373 0 .684.124.933.373.25.249.383.569.4.96v1.173c-.017.391-.15.711-.4.96-.249.25-.56.374-.933.374s-.684-.125-.933-.374c-.25-.249-.383-.569-.4-.96V12.44c.017-.391.15-.711.4-.96.249-.249.56-.373.933-.373z"/></svg>',
        'zhihu' => '<svg class="profile-icon" viewBox="0 0 24 24" fill="currentColor"><path d="M5.721 0C2.251 0 0 2.25 0 5.719V18.28C0 21.751 2.252 24 5.721 24h12.56C21.751 24 24 21.75 24 18.281V5.72C24 2.249 21.75 0 18.281 0zm1.964 4.078h6.191c.193 0 .322.136.291.333l-.164.998a.334.334 0 01-.312.266H9.594c-.244.636-.863 2.076-1.399 3.107h3.727c.387 0 .259.263.259.263l-.166 1.035a.368.368 0 01-.332.291H8.35c-.681 1.06-1.532 2.043-2.404 2.854.375.168 2.268.861 3.265 1.468.157.096.18.18.096.332l-.673 1.07c-.084.156-.18.18-.336.096-1.056-.612-2.76-1.416-3.48-1.776-.576.492-1.86 1.368-2.532 1.74-.096.06-.192.06-.276-.072l-.792-1.02c-.06-.12-.024-.216.096-.276.876-.42 1.86-1.152 2.496-1.728-.384-.204-1.308-.72-1.752-.972-.084-.06-.096-.156-.024-.288l.564-.924a.213.213 0 01.288-.084c.468.24 1.464.804 1.92 1.044.576-.588 1.14-1.26 1.572-1.884H4.269a.175.175 0 01-.18-.168l-.024-1.08c0-.132.072-.204.192-.204h3.444c.372-.78.648-1.536.828-2.112H5.4a.175.175 0 01-.18-.168l-.024-1.092c0-.12.072-.204.204-.204zm7.692.168h4.632c.12 0 .204.06.204.192v.996c0 .12-.084.204-.204.204h-1.632v6.36h1.764c.12 0 .204.072.204.204v.984c0 .132-.084.204-.204.204h-1.764v3.396l1.968-.48c.096-.024.168.036.168.132v1.08c0 .108-.024.168-.168.204l-5.016 1.284c-.132.036-.216-.012-.252-.132l-.24-1.08c-.024-.132.036-.216.132-.24l1.488-.36V13.186h-1.692c-.12 0-.204-.072-.204-.204v-.984c0-.132.084-.204.204-.204h1.692v-6.36h-1.08a14.246 14.246 0 01-.876 2.796c-.06.12-.156.132-.264.06l-.972-.552c-.108-.06-.132-.156-.072-.276.684-1.44 1.116-3.468 1.236-4.68.012-.12.084-.192.216-.168l1.092.204c.12.024.18.096.168.228z"/></svg>',
    );

    return isset($icons[$icon]) ? $icons[$icon] : $icons['website'];
}

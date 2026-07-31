<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>

<div id="comments" class="comments-area">
    <?php $this->comments()->to($comments); ?>

    <?php if ($comments->have()): ?>
        <h3 class="comments-title"><?php $this->commentsNum('暂无评论', '只有1条评论', '已有%d条评论'); ?></h3>

        <?php $comments->listComments(array(
            'before'        => '<ol class="comment-list">',
            'after'         => '</ol>',
            'beforeAuthor'  => '<li class="comment-item" id="{id}">',
            'afterAuthor'   => '</li>',
            'beforeContent' => '<div class="comment-content">',
            'afterContent'  => '</div>',
            'replyWord'     => '回复',
            'avatarSize'    => 48,
        )); ?>

        <?php $comments->pageNav('&laquo; 前一页', '后一页 &raquo;'); ?>
    <?php endif; ?>

    <?php if ($this->allow('comment')): ?>
    <div id="respond" class="comment-respond">
        <h3 class="comment-reply-title"><?php _e('添加新评论'); ?></h3>
        <form method="post" action="<?php $this->commentUrl() ?>" id="comment-form" class="comment-form" role="form">
            <?php if ($this->user->hasLogin()): ?>
                <p class="comment-user-info">
                    <?php _e('登录身份: '); ?><a href="<?php $this->options->profileUrl(); ?>"><?php $this->user->screenName(); ?></a>.
                    <a href="<?php $this->options->logoutUrl(); ?>" title="Logout"><?php _e('退出'); ?> &raquo;</a>
                </p>
            <?php else: ?>
                <p class="comment-form-author">
                    <label for="author"><?php _e('称呼'); ?> <span class="required">*</span></label>
                    <input type="text" name="author" id="author" class="text" value="<?php $this->remember('author'); ?>" required />
                </p>
                <p class="comment-form-email">
                    <label for="mail"<?php if ($this->options->commentsRequireMail): ?> class="required"<?php endif; ?>><?php _e('邮箱'); ?><?php if ($this->options->commentsRequireMail): ?> <span class="required">*</span><?php endif; ?></label>
                    <input type="email" name="mail" id="mail" class="text" value="<?php $this->remember('mail'); ?>"<?php if ($this->options->commentsRequireMail): ?> required<?php endif; ?> />
                </p>
                <p class="comment-form-url">
                    <label for="url"<?php if ($this->options->commentsRequireURL): ?> class="required"<?php endif; ?>><?php _e('网站'); ?><?php if ($this->options->commentsRequireURL): ?> <span class="required">*</span><?php endif; ?></label>
                    <input type="url" name="url" id="url" class="text" value="<?php $this->remember('url'); ?>"<?php if ($this->options->commentsRequireURL): ?> required<?php endif; ?> />
                </p>
            <?php endif; ?>
            <p class="comment-form-comment">
                <label for="textarea"><?php _e('评论内容'); ?> <span class="required">*</span></label>
                <textarea name="text" id="textarea" class="textarea" rows="5" required></textarea>
            </p>
            <p class="form-submit">
                <button type="submit" class="submit"><?php _e('提交评论'); ?></button>
                <?php $comments->cancelReply(); ?>
            </p>
        </form>
    </div>
    <?php else: ?>
        <p class="comments-closed"><?php _e('评论已关闭'); ?></p>
    <?php endif; ?>
</div>

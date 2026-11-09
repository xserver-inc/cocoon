<?php

namespace Cocoon\Tests\Integration;

class SimpleThreadCommentsIntegrationTest extends IntegrationTestCase
{
    /**
     * シンプルスレッドの投稿者リンクと、親コメント内の返信リストの検証
     */
    public function testSimpleThreadRendersValidCommentList(): void
    {
        global $comment, $comment_alt, $comment_depth, $comment_thread_alt, $in_comment_loop;

        $postId = $this->createPost([
            'post_title' => 'Simple thread comments',
            'post_status' => 'publish',
            'comment_status' => 'open',
        ]);
        $commentData = ['comment_post_ID' => $postId, 'comment_author_IP' => '192.0.2.1'];
        $authorUrl = 'https://example.org/';
        $parentId = wp_insert_comment($commentData + [
            'comment_author' => 'Parent',
            'comment_author_url' => $authorUrl,
            'comment_content' => 'Parent comment',
        ]);
        $this->assertNotFalse($parentId);
        $replyId = wp_insert_comment($commentData + [
            'comment_parent' => $parentId,
            'comment_author' => 'Reply',
            'comment_content' => 'Reply comment',
        ]);
        $this->assertNotFalse($replyId);

        // コメント設定の一時指定と、描画で更新されるループ変数の退避
        $displayType = static fn() => 'simple_thread';
        $savedLoop = [$comment, $comment_alt, $comment_depth, $comment_thread_alt, $in_comment_loop];
        add_filter('theme_mod_' . OP_COMMENT_DISPLAY_TYPE, $displayType);
        try {
            $args = get_wp_list_comments_args() + ['echo' => false, 'max_depth' => 2, 'per_page' => 0];
            $html = wp_list_comments($args, [get_comment($parentId), get_comment($replyId)]);
        } finally {
            remove_filter('theme_mod_' . OP_COMMENT_DISPLAY_TYPE, $displayType);
            [$comment, $comment_alt, $comment_depth, $comment_thread_alt, $in_comment_loop] = $savedLoop;
        }

        // HTMLパーサーが補正する前の、余分な閉じタグの検出
        $this->assertSame(2, substr_count($html, '</li>'));
        $this->assertSame(substr_count($html, '<a '), substr_count($html, '</a>'));

        $document = new \DOMDocument();
        $document->loadHTML('<!doctype html><ol>' . $html . '</ol>', LIBXML_NONET | LIBXML_NOERROR | LIBXML_NOWARNING);
        $xpath = new \DOMXPath($document);
        $parent = '//ol/li[@id="comment-' . $parentId . '"]';
        $reply = $parent . '/ul[@class="children"]/li[@id="comment-' . $replyId . '"]';
        $this->assertCount(1, $xpath->query($parent . '/div//span[@class="comment-author-label"]'));
        $this->assertCount(1, $xpath->query($reply));
        $this->assertSame($authorUrl, $xpath->evaluate('string(' . $parent . '/div//cite/a/@href)'));
        $this->assertSame('Reply', $xpath->evaluate('string(' . $reply . '/div//cite)'));
        $this->assertCount(0, $xpath->query($reply . '/div//cite/a'));
    }
}

<?php
/**
 * Araneta City news article detail page.
 *
 * Displays a single news article with full content.
 *
 * @var \App\View\AppView $this
 * @var array $article The news article data
 * @var array $recentNews Recent news for sidebar
 */

$this->assign('title', h($article['news_title']) . ' - Araneta City');
$baseUrl = rtrim(env('API_BASE_URL', ''), '/');

// Fallback image shown when the article image is missing or broken.
$fallbackUrl = 'https://aci-cms.aranetacity.com/files/happenings/a79d94b8-1a1e-4aa0-9a87-774dba87118c/acilogo.png';

// Get image URL
if (!empty($article['img_url'])) {
    $path = str_replace('\\', '/', $article['img_url']);
    $dir = dirname($path);
    $file = basename($path);
    // Encode only spaces and apostrophes so they don't break the URL.
    $encodedFile = str_replace([' ', "'"], ['%20', '%27'], $file);
    // News images are served under a "thumbnail-" prefix. Thumbnail first,
    // then the original file via onerror.
    $thumbUrl = $baseUrl . $dir . '/thumbnail-' . $encodedFile;
    $fullUrl = $baseUrl . $dir . '/' . $encodedFile;
} else {
    $thumbUrl = $fallbackUrl;
    $fullUrl = $fallbackUrl;
}

// Format dates
$postedDate = !empty($article['posted_date']) ? date('d F Y', strtotime($article['posted_date'])) : date('d F Y', strtotime($article['created'] ?? 'now'));

// The body HTML (news_fulltext) embeds images with CMS-relative paths like
// "../files/image_uploads/..." or "files/..." which 404 on our site. Rewrite
// those to absolute CMS URLs so the inline images load. Absolute URLs
// (http/https or protocol-relative) are left untouched.
$bodyHtml = (string)($article['news_fulltext'] ?? '');
if ($bodyHtml !== '' && $baseUrl !== '') {
    $bodyHtml = preg_replace_callback(
        '/(<img\b[^>]*?\bsrc=)(["\'])(.*?)\2/i',
        function ($m) use ($baseUrl) {
            $src = trim($m[3]);
            // Leave absolute / data URLs alone.
            if ($src === '' || preg_match('#^(https?:)?//#i', $src) || str_starts_with($src, 'data:')) {
                return $m[0];
            }
            // Normalize leading "../", "./" and "/" then resolve to the CMS root.
            $src = preg_replace('#^(\.\./|\./)+#', '', $src);
            $src = '/' . ltrim($src, '/');
            return $m[1] . $m[2] . $baseUrl . $src . $m[2];
        },
        $bodyHtml
    );
}
?>

<div id="wrapper">
    <div class="content">
        <!-- ARTICLE HEADER -->
        <section class="pb-4" style="background:#f8f8f8;padding:30px 0;">
            <div class="container">
                <div class="row">
                    <div class="col-md-8">
                        <h1 style="font-size:32px;line-height:42px;font-weight:700;color:#00afab;margin:0 0 20px;text-align:left;">
                            <?= h($article['news_title']) ?>
                        </h1>
                        <div style="display:flex;gap:15px;color:#999;font-size:14px;margin-bottom:20px;">
                            <span><i class="fas fa-calendar"></i> <?= $postedDate ?></span>
                            <?php if (!empty($article['category'])): ?>
                                <span><i class="fas fa-tag"></i> <?= h($article['category']) ?></span>
                            <?php endif; ?>
                            <?php if (!empty($article['type'])): ?>
                                <span><i class="fas fa-folder"></i> <?= h($article['type']) ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ARTICLE CONTENT -->
        <section style="padding:40px 0;">
            <div class="container">
                <div class="row">
                    <!-- MAIN CONTENT -->
                    <div class="col-md-8">
                        <!-- Article Image -->
                        <?php if (!empty($article['img_url'])): ?>
                            <div style="margin-bottom:30px;overflow:hidden;border-radius:4px;">
                                <img src="<?= $thumbUrl ?>" data-original="<?= h($fullUrl) ?>" data-fallback="<?= h($fallbackUrl) ?>" onerror="if(this.dataset.original && this.src!==this.dataset.original){this.src=this.dataset.original;}else{this.onerror=null;this.src=this.dataset.fallback;}" alt="<?= h($article['news_title']) ?>" style="width:100%;height:auto;display:block;">
                            </div>
                            <?php if (!empty($article['photo_caption'])): ?>
                                <p style="font-size:12px;color:#999;margin-bottom:30px;text-align:center;font-style:italic;">
                                    <?= h($article['photo_caption']) ?>
                                </p>
                            <?php endif; ?>
                        <?php endif; ?>

                        <!-- Article Body -->
                        <div style="font-size:16px;line-height:1.8;color:#333;margin-bottom:30px;text-align: left;">
                            <?= $bodyHtml ?>
                        </div>

                        <!-- Article Meta -->
                        <div style="border-top:1px solid #e0e0e0;padding-top:20px;margin-top:30px;">
                            <?php if (!empty($article['metakeywords'])): ?>
                                <p style="color:#666;font-size:14px;">
                                    <strong>Keywords:</strong> <?= h($article['metakeywords']) ?>
                                </p>
                            <?php endif; ?>
                        </div>

                        <!-- Share Buttons -->
                        <div style="margin-top:30px;padding:20px;background:#f8f8f8;border-radius:4px;text-align:center;">
                            <p style="margin:0 0 15px;color:#666;font-weight:500;">Share This Article</p>
                            <div style="display:flex;justify-content:center;gap:10px;">
                                <a href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode($this->Url->build(['action' => 'newsArticle', 'slug' => $article['slug']], ['fullBase' => true])) ?>" target="_blank" style="display:inline-flex;align-items:center;justify-content:center;width:40px;height:40px;background:#3b5998;color:#fff;border-radius:50%;text-decoration:none;">
                                    <i class="fab fa-facebook-f"></i>
                                </a>
                                <a href="https://twitter.com/intent/tweet?url=<?= urlencode($this->Url->build(['action' => 'newsArticle', 'slug' => $article['slug']], ['fullBase' => true])) ?>&text=<?= urlencode($article['news_title']) ?>" target="_blank" style="display:inline-flex;align-items:center;justify-content:center;width:40px;height:40px;background:#1da1f2;color:#fff;border-radius:50%;text-decoration:none;">
                                    <i class="fab fa-twitter"></i>
                                </a>
                                <a href="mailto:?subject=<?= urlencode($article['news_title']) ?>&body=<?= urlencode($this->Url->build(['action' => 'newsArticle', 'slug' => $article['slug']], ['fullBase' => true])) ?>" style="display:inline-flex;align-items:center;justify-content:center;width:40px;height:40px;background:#ea4335;color:#fff;border-radius:50%;text-decoration:none;">
                                    <i class="fas fa-envelope"></i>
                                </a>
                            </div>
                        </div>

                        <!-- Back Link -->
                        <div style="margin-top:30px;">
                            <a href="<?= $this->Url->build('/news') ?>" style="color:#00afab;text-decoration:none;font-weight:500;">
                                ← Back to News
                            </a>
                        </div>
                    </div>

                    <!-- SIDEBAR -->
                    <div class="col-md-4" style="padding-left:30px;text-align:left;">
                        <!-- Recent News -->
                        <div style="background:#f8f8f8;padding:20px;border-radius:4px;margin-bottom:30px;">
                            <h3 style="font-size:18px;font-weight:700;color:#00afab;margin:0 0 20px;">Recent Posts</h3>
                            <?php if (!empty($recentNews)): ?>
                                <ul style="list-style:none;padding:0;margin:0;">
                                    <?php foreach ($recentNews as $recent): ?>
                                        <?php
                                        // Get thumbnail image for recent news
                                        $recentThumbUrl = $fallbackUrl;
                                        $recentFullUrl = $fallbackUrl;
                                        if (!empty($recent['img_url'])) {
                                            $recentPath = str_replace('\\', '/', $recent['img_url']);
                                            $recentDir = dirname($recentPath);
                                            $recentFile = basename($recentPath);
                                            // Encode only spaces and apostrophes so they don't break the URL.
                                            $recentEncoded = str_replace([' ', "'"], ['%20', '%27'], $recentFile);
                                            // Thumbnail first, then the original file via onerror.
                                            $recentThumbUrl = $baseUrl . $recentDir . '/thumbnail-' . $recentEncoded;
                                            $recentFullUrl = $baseUrl . $recentDir . '/' . $recentEncoded;
                                        }
                                        ?>
                                        <li style="margin-bottom:15px;padding-bottom:15px;border-bottom:1px solid #e0e0e0;display:flex;gap:12px;">
                                            <!-- Image Thumbnail -->
                                            <div style="flex-shrink:0;width:70px;height:70px;border-radius:4px;overflow:hidden;background:#e0e0e0;">
                                                <img src="<?= h($recentThumbUrl) ?>" data-original="<?= h($recentFullUrl) ?>" data-fallback="<?= h($fallbackUrl) ?>" onerror="if(this.dataset.original && this.src!==this.dataset.original){this.src=this.dataset.original;}else{this.onerror=null;this.src=this.dataset.fallback;}" alt="<?= h($recent['news_title']) ?>" style="width:100%;height:100%;object-fit:cover;display:block;">
                                            </div>
                                            <!-- Content -->
                                            <div style="flex:1;min-width:0;">
                                                <?php if ($recent['slug'] !== $article['slug']): ?>
                                                    <a href="<?= $this->Url->build('/news-article/' . h($recent['slug'])) ?>" style="color:#333;text-decoration:none;font-weight:500;display:block;margin-bottom:5px;font-size:13px;line-height:1.4;">
                                                        <?= h($recent['news_title']) ?>
                                                    </a>
                                                    <p style="color:#999;font-size:11px;margin:0;">
                                                        <?= !empty($recent['posted_date']) ? date('M d, Y', strtotime($recent['posted_date'])) : date('M d, Y', strtotime($recent['created'] ?? 'now')) ?>
                                                    </p>
                                                <?php else: ?>
                                                    <strong style="color:#333;display:block;margin-bottom:5px;font-size:13px;line-height:1.4;">
                                                        <?= h($recent['news_title']) ?>
                                                    </strong>
                                                    <p style="color:#999;font-size:11px;margin:0;">
                                                        <?= !empty($recent['posted_date']) ? date('M d, Y', strtotime($recent['posted_date'])) : date('M d, Y', strtotime($recent['created'] ?? 'now')) ?>
                                                    </p>
                                                <?php endif; ?>
                                            </div>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php endif; ?>
                        </div>

                        <!-- Categories -->
                        <?php if (!empty($article['category'])): ?>
                        <div style="background:#f8f8f8;padding:20px;border-radius:4px;margin-bottom:30px;">
                            <h3 style="font-size:18px;font-weight:700;color:#00afab;margin:0 0 15px;">Category</h3>
                            <p style="margin:0;">
                                <span style="display:inline-block;background:#00afab;color:#fff;padding:5px 10px;border-radius:3px;font-size:13px;">
                                    <?= h($article['category']) ?>
                                </span>
                            </p>
                        </div>
                        <?php endif; ?>

                        <!-- Tags -->
                        <?php if (!empty($article['metakeywords'])): ?>
                        <div style="background:#f8f8f8;padding:20px;border-radius:4px;">
                            <h3 style="font-size:18px;font-weight:700;color:#00afab;margin:0 0 15px;">Tags</h3>
                            <div style="display:flex;flex-wrap:wrap;gap:8px;">
                                <?php 
                                $tags = array_map('trim', explode(',', $article['metakeywords']));
                                foreach ($tags as $tag): 
                                ?>
                                    <a href="<?= $this->Url->build('/news?tag=' . urlencode($tag)) ?>" style="display:inline-block;background:#e0e0e0;color:#333;padding:5px 10px;border-radius:3px;font-size:12px;text-decoration:none;">
                                        <?= h($tag) ?>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>

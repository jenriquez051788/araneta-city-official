<?php
/**
 * Araneta City news listing page.
 *
 * Displays all news articles in a compact grid layout.
 *
 * @var \App\View\AppView $this
 * @var array $news Array of news articles
 * @var array $paging Pagination metadata
 */

$this->assign('title', 'News & Stories - Araneta City');
$baseUrl = rtrim(env('API_BASE_URL', ''), '/');
?>

<style>
    .news-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 24px;
        margin-top: 30px;
        margin-bottom: 50px;
    }

    .news-card {
        display: flex;
        flex-direction: column;
        height: 100%;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
    }

    .news-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.15);
    }

    .news-card-image {
        width: 100%;
        height: 180px;
        overflow: hidden;
        background: #f5f5f5;
    }

    .news-card-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s ease;
    }

    .news-card:hover .news-card-image img {
        transform: scale(1.05);
    }

    .news-card-content {
        padding: 16px;
        flex: 1;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .news-card-category {
        text-transform: uppercase;
        font-size: 11px;
        font-weight: 600;
        color: #00afab;
        letter-spacing: 0.5px;
        margin-bottom: 8px;
    }

    .news-card-title {
        font-size: 15px;
        font-weight: 600;
        line-height: 1.4;
        color: #333;
        margin-bottom: 8px;
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
        min-height: 63px;
        text-align: left;
    }

    .news-card-meta {
        font-size: 13px;
        color: #999;
        margin-bottom: 12px;
        text-align: left;
    }

    .news-card-meta i {
        margin-right: 5px;
    }

    .news-card-title a {
        color: #333;
        text-decoration: none;
    }

    .news-card-title a:hover {
        color: #00afab;
    }

    .pagination-wrapper {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
        margin-top: 50px;
        padding-top: 30px;
        border-top: 1px solid #e0e0e0;
    }

    .pagination-wrapper a,
    .pagination-wrapper span {
        padding: 10px 14px;
        border-radius: 4px;
        text-decoration: none;
        font-size: 14px;
    }

    .pagination-wrapper a {
        color: #00afab;
        border: 1px solid #e0e0e0;
        font-weight: 500;
        transition: all 0.3s ease;
    }

    .pagination-wrapper a:hover {
        background: #f5f5f5;
        border-color: #00afab;
    }

    .pagination-wrapper strong {
        color: #fff;
        background: #00afab;
        padding: 10px 14px;
        border-radius: 4px;
        font-size: 14px;
    }

    .pagination-wrapper .disabled {
        color: #ccc;
        border: 1px solid #e0e0e0;
        cursor: not-allowed;
    }
</style>

<div id="wrapper">
    <div class="content">
        <!-- NEWS & STORIES HEADER -->
        <section>
            <div class="container">
                <div style="margin-bottom: 40px;">
                    <h2 style="font-size: 35px; font-weight: 700; color: #333; margin: 0 0 15px 0;">News & Stories</h2>
                    <p style="font-size: 16px; color: #666; line-height: 1.6; margin: 0;">Press releases, announcements, and lifestyle features from Araneta City</p>
                </div>

                <!-- NEWS GRID -->
                <div class="news-grid">
                    <?php if (!empty($news)): ?>
                        <?php foreach ($news as $item): ?>
                            <?php
                            // Fallback image shown when the news image is missing or broken.
                            $fallbackUrl = 'https://aci-cms.aranetacity.com/files/happenings/a79d94b8-1a1e-4aa0-9a87-774dba87118c/acilogo.png';
                            $imageUrl = $fallbackUrl;
                            $originalUrl = '';
                            if (!empty($item['img_url'])) {
                                $path = str_replace('\\', '/', $item['img_url']);
                                $dir = dirname($path);
                                $file = basename($path);
                                // Encode only spaces and apostrophes so they don't break
                                // the URL, while leaving valid characters (hyphens, etc.) intact.
                                $encodedFile = str_replace([' ', "'"], ['%20', '%27'], $file);
                                // News images are served under a "thumbnail-" prefix. Use the
                                // thumbnail first, fall back to the original file, then the logo.
                                $imageUrl = $baseUrl . $dir . '/thumbnail-' . $encodedFile;
                                $originalUrl = $baseUrl . $dir . '/' . $encodedFile;
                            }
                            
                            $category = $item['news_category'] ?? 'News';
                            $date = !empty($item['posted_date']) ? date('M d, Y', strtotime($item['posted_date'])) : (!empty($item['created']) ? date('M d, Y', strtotime($item['created'])) : '');
                            ?>
                            <article class="news-card">
                                <div class="news-card-image">
                                    <a href="<?= $this->Url->build('/news-article/' . h($item['slug'])) ?>">
                                        <img src="<?= h($imageUrl) ?>" alt="<?= h($item['news_title']) ?>"
                                            loading="lazy" decoding="async"
                                            data-original="<?= h($originalUrl) ?>"
                                            data-fallback="<?= h($fallbackUrl) ?>"
                                            onerror="if(this.dataset.original && this.src!==this.dataset.original){this.src=this.dataset.original;}else{this.onerror=null;this.src=this.dataset.fallback;}">
                                    </a>
                                </div>
                                <div class="news-card-content">
                                    <div>
                                        <!-- <div class="news-card-category">
                                            <i class="fas fa-tag"></i> <?= h($category) ?>
                                        </div> -->
                                        <h3 class="news-card-title">
                                            <a href="<?= $this->Url->build('/news-article/' . h($item['slug'])) ?>">
                                                <?= h($item['news_title']) ?>
                                            </a>
                                        </h3>
                                        <div class="news-card-meta">
                                         <?= $date ?>
                                        </div>
                                    </div>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div style="grid-column: 1 / -1; text-align: center; padding: 60px 20px;">
                            <p style="font-size: 18px; color: #999;">No news articles available at this time.</p>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- PAGINATION -->
                <?php if (!empty($paging) && $paging['pageCount'] > 1): ?>
                <div class="pagination-wrapper">
                    <?php if ($paging['page'] > 1): ?>
                        <a href="<?= $this->Url->build(['action' => 'news', '?' => ['page' => $paging['page'] - 1]]) ?>">← Prev</a>
                    <?php else: ?>
                        <span class="disabled">← Prev</span>
                    <?php endif; ?>

                    <?php 
                    $maxPages = 5;
                    $startPage = max(1, $paging['page'] - floor($maxPages / 2));
                    $endPage = min($paging['pageCount'], $startPage + $maxPages - 1);
                    
                    if ($endPage - $startPage < $maxPages - 1) {
                        $startPage = max(1, $endPage - $maxPages + 1);
                    }
                    
                    if ($startPage > 1): ?>
                        <a href="<?= $this->Url->build(['action' => 'news', '?' => ['page' => 1]]) ?>">1</a>
                        <?php if ($startPage > 2): ?>
                            <span>...</span>
                        <?php endif; ?>
                    <?php endif; ?>
                    
                    <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
                        <?php if ($i === $paging['page']): ?>
                            <strong><?= $i ?></strong>
                        <?php else: ?>
                            <a href="<?= $this->Url->build(['action' => 'news', '?' => ['page' => $i]]) ?>"><?= $i ?></a>
                        <?php endif; ?>
                    <?php endfor; ?>
                    
                    <?php if ($endPage < $paging['pageCount']): ?>
                        <?php if ($endPage < $paging['pageCount'] - 1): ?>
                            <span>...</span>
                        <?php endif; ?>
                        <a href="<?= $this->Url->build(['action' => 'news', '?' => ['page' => $paging['pageCount']]]) ?>"><?= $paging['pageCount'] ?></a>
                    <?php endif; ?>

                    <?php if ($paging['page'] < $paging['pageCount']): ?>
                        <a href="<?= $this->Url->build(['action' => 'news', '?' => ['page' => $paging['page'] + 1]]) ?>">Next →</a>
                    <?php else: ?>
                        <span class="disabled">Next →</span>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </div>
        </section>
    </div>
</div>

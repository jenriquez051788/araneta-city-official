<?php
/**
 * Araneta City happenings listing page.
 *
 * Displays all happenings in a compact 4-column grid layout
 * (styled to match the News & Stories page).
 *
 * @var \App\View\AppView $this
 * @var array $happenings Array of happenings
 * @var array $paging Pagination metadata
 */

$this->assign('title', 'What\'s Happening - Araneta City');
$baseUrl = rtrim(env('API_BASE_URL', ''), '/');
?>

<style>
    .happenings-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 24px;
        margin-top: 30px;
        margin-bottom: 50px;
    }

    .happening-card {
        display: flex;
        flex-direction: column;
        height: 100%;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
    }

    .happening-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.15);
    }

    .happening-card-image {
        width: 100%;
        height: 180px;
        overflow: hidden;
        background: #f5f5f5;
    }

    .happening-card-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s ease;
    }

    .happening-card:hover .happening-card-image img {
        transform: scale(1.05);
    }

    .happening-card-content {
        padding: 16px;
        flex: 1;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .happening-card-meta {
        text-transform: uppercase;
        font-size: 11px;
        font-weight: 600;
        color: #00afab;
        letter-spacing: 0.5px;
        margin-bottom: 8px;
    }

    .happening-card-meta i {
        margin-right: 5px;
    }

    .happening-card-title {
        font-size: 15px;
        font-weight: 600;
        line-height: 1.4;
        color: #333;
        margin-bottom: 8px;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        min-height: 42px;
    }

    .happening-card-title a {
        color: #333;
        text-decoration: none;
    }

    .happening-card-title a:hover {
        color: #00afab;
    }

    .happening-card-desc {
        font-size: 13px;
        line-height: 1.5;
        color: #666;
        margin: 0 0 12px;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .happening-card-link {
        color: #00afab;
        font-size: 13px;
        font-weight: 500;
        text-decoration: none;
        margin-top: auto;
        display: inline-block;
    }

    .happening-card-link:hover {
        color: #008b88;
    }

    /* Responsive: 3 cols on tablet, 2 on small tablet, 1 on mobile */
    @media (max-width: 1100px) {
        .happenings-grid {
            grid-template-columns: repeat(3, 1fr);
        }
    }

    @media (max-width: 768px) {
        .happenings-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 480px) {
        .happenings-grid {
            grid-template-columns: 1fr;
        }
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
        <!-- HAPPENINGS HEADER -->
        <section>
            <div class="container">
                <div style="margin-bottom: 40px;">
                    <h2 style="font-size: 35px; font-weight: 700; color: #333; margin: 0 0 15px 0;">What's Happening</h2>
                    <p style="font-size: 16px; color: #666; line-height: 1.6; margin: 0;">Explore the newest stores, openings, and happenings across Araneta City</p>
                </div>

                <!-- HAPPENINGS GRID -->
                <div class="happenings-grid">
                    <?php if (!empty($happenings)): ?>
                        <?php foreach ($happenings as $item): ?>
                            <?php
                            $fullText = strip_tags($item['description'] ?? $item['title'] ?? '');
                            $trimstring = strlen($fullText) > 120 ? substr($fullText, 0, 120) . '...' : $fullText;

                            // Fallback image shown when the happening image is missing or broken.
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
                                // Original image first, then fallback logo on error.
                                // (Thumbnails are not reliably generated on the server.)
                                $imageUrl = $baseUrl . $dir . '/' . $encodedFile;
                                $originalUrl = $baseUrl . $dir . '/' . $encodedFile;
                            }

                            // Link to the internal detail page when a slug exists,
                            // otherwise fall back to the external link.
                            $slug = $item['slug'] ?? '';
                            $link = !empty($slug)
                                ? $this->Url->build('/happening/' . $slug)
                                : ($item['link'] ?? '#');
                            ?>
                            <article class="happening-card">
                                <div class="happening-card-image">
                                    <a href="<?= h($link) ?>">
                                        <img src="<?= h($imageUrl) ?>" alt="<?= h($item['title']) ?>"
                                            loading="lazy" decoding="async"
                                            data-original="<?= h($originalUrl) ?>"
                                            data-fallback="<?= h($fallbackUrl) ?>"
                                            onerror="if(this.dataset.original && this.src!==this.dataset.original){this.src=this.dataset.original;}else{this.onerror=null;this.src=this.dataset.fallback;}">
                                    </a>
                                </div>
                                <div class="happening-card-content">
                                    <div>
                                        <?php if (!empty($item['date'])): ?>
                                            <div class="happening-card-meta">
                                                <i class="fas fa-calendar"></i> <?= h($item['date']) ?>
                                            </div>
                                        <?php endif; ?>
                                        <h3 class="happening-card-title">
                                            <a href="<?= h($link) ?>"><?= h($item['title']) ?></a>
                                        </h3>
                                        <?php if (!empty($trimstring)): ?>
                                            <p class="happening-card-desc"><?= h($trimstring) ?></p>
                                        <?php endif; ?>
                                    </div>
                                    <a href="<?= h($link) ?>" class="happening-card-link">Learn More →</a>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div style="grid-column: 1 / -1; text-align: center; padding: 60px 20px;">
                            <p style="font-size: 18px; color: #999;">No happenings available at this time.</p>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- PAGINATION -->
                <?php if (!empty($paging) && $paging['pageCount'] > 1): ?>
                <div class="pagination-wrapper">
                    <?php if ($paging['page'] > 1): ?>
                        <a href="<?= $this->Url->build(['action' => 'happenings', '?' => ['page' => $paging['page'] - 1]]) ?>">← Prev</a>
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
                        <a href="<?= $this->Url->build(['action' => 'happenings', '?' => ['page' => 1]]) ?>">1</a>
                        <?php if ($startPage > 2): ?>
                            <span>...</span>
                        <?php endif; ?>
                    <?php endif; ?>

                    <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
                        <?php if ($i === $paging['page']): ?>
                            <strong><?= $i ?></strong>
                        <?php else: ?>
                            <a href="<?= $this->Url->build(['action' => 'happenings', '?' => ['page' => $i]]) ?>"><?= $i ?></a>
                        <?php endif; ?>
                    <?php endfor; ?>

                    <?php if ($endPage < $paging['pageCount']): ?>
                        <?php if ($endPage < $paging['pageCount'] - 1): ?>
                            <span>...</span>
                        <?php endif; ?>
                        <a href="<?= $this->Url->build(['action' => 'happenings', '?' => ['page' => $paging['pageCount']]]) ?>"><?= $paging['pageCount'] ?></a>
                    <?php endif; ?>

                    <?php if ($paging['page'] < $paging['pageCount']): ?>
                        <a href="<?= $this->Url->build(['action' => 'happenings', '?' => ['page' => $paging['page'] + 1]]) ?>">Next →</a>
                    <?php else: ?>
                        <span class="disabled">Next →</span>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </div>
        </section>
    </div>
</div>

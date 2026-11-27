<?php
/**
 * Happening detail page.
 *
 * Modern, clean, left-aligned layout: main content (banner, title, date,
 * full description) on the left, a "More to Discover" sidebar on the right.
 *
 * @var \App\View\AppView $this
 * @var array $happening The happening data
 * @var array $relatedHappenings Related happenings (max 4)
 */

$baseUrl = rtrim(env('API_BASE_URL', ''), '/');

// Resolve thumbnail + original image URLs for an item.
$buildImage = function (array $item) use ($baseUrl) {
    if (empty($item['img_url'])) {
        return ['thumb' => '', 'original' => ''];
    }
    $path = str_replace('\\', '/', $item['img_url']);
    $dir = dirname($path);
    $file = basename($path);
    // Encode only spaces and apostrophes so they don't break the URL,
    // while leaving valid characters (hyphens, etc.) intact.
    $encodedFile = str_replace([' ', "'"], ['%20', '%27'], $file);

    // Thumbnails are not reliably generated on the server, so point both
    // at the original file. The onerror fallback still catches missing images.
    $url = $baseUrl . $dir . '/' . $encodedFile;

    return [
        'thumb' => $url,
        'original' => $url,
    ];
};

// Fallback image shown when a happening's image is missing or broken.
$fallbackUrl = 'https://aci-cms.aranetacity.com/files/happenings/a79d94b8-1a1e-4aa0-9a87-774dba87118c/acilogo.png';

$title = $happening['title'] ?? ($happening['event_title'] ?? 'Happening');
$date = $happening['date'] ?? ($happening['display_date'] ?? '');
$link = $happening['link'] ?? '';

// Full body text: prefer full description fields, fall back to short description.
$bodyHtml = $happening['fulltext']
    ?? ($happening['fulldesc']
    ?? ($happening['full_description']
    ?? ($happening['description'] ?? '')));

$heroImg = $buildImage($happening);
$heroSrc = !empty($heroImg['original']) ? $heroImg['original'] : $fallbackUrl;

$this->assign('title', $title . ' - Araneta City');
?>

<style>
    .hd-page {
        background: #fff;
        padding: 36px 0 70px;
        text-align: left;
    }

    .hd-wrap {
        width: 92%;
        max-width: 1200px;
        margin: 0 auto;
        text-align: left;
    }

    .hd-back {
        display: flex;
        width: fit-content;
        align-items: center;
        gap: 8px;
        font-size: 14px;
        font-weight: 600;
        color: #00afab;
        text-decoration: none;
        margin: 0 0 28px;
        text-align: left;
    }

    .hd-main {
        text-align: left;
    }

    .hd-back:hover {
        color: #008b88;
    }

    /* Two-column layout: content left, sidebar right */
    .hd-layout {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 320px;
        gap: 48px;
        align-items: start;
    }

    /* ---- Main content (left) ---- */
    /* Prominent date card so users spot the "when" immediately */
    .hd-date-card {
        display: flex;
        width: fit-content;
        align-items: center;
        gap: 14px;
        background: linear-gradient(135deg, #e6f6f5 0%, #f0faf9 100%);
        border: 1px solid #b8e6e4;
        border-left: 4px solid #00afab;
        border-radius: 12px;
        padding: 14px 22px;
        margin: 0 0 28px;
    }

    .hd-date-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 44px;
        height: 44px;
        border-radius: 10px;
        background: #00afab;
        color: #fff;
        font-size: 20px;
        flex-shrink: 0;
    }

    .hd-date-text {
        text-align: left;
    }

    .hd-date-label {
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        color: #00afab;
        margin: 0 0 2px;
    }

    .hd-date-value {
        font-size: 18px;
        font-weight: 800;
        color: #1a1a1a;
        margin: 0;
        line-height: 1.2;
    }

    .hd-title {
        font-size: 36px;
        font-weight: 800;
        line-height: 1.15;
        color: #1a1a1a;
        margin: 0 0 26px;
        letter-spacing: -0.5px;
        text-align: left;
    }

    .hd-hero {
        width: 100%;
        border-radius: 14px;
        overflow: hidden;
        background: #f2f2f2;
        margin-bottom: 30px;
    }

    .hd-hero img {
        width: 100%;
        height: auto;
        display: block;
    }

    .hd-body {
        font-size: 16px;
        line-height: 1.8;
        color: #444;
        text-align: left;
    }

    .hd-body p {
        margin: 0 0 18px;
    }

    .hd-body img {
        max-width: 100%;
        height: auto;
        border-radius: 8px;
        margin: 18px 0;
    }

    .hd-body h2,
    .hd-body h3,
    .hd-body h4 {
        color: #1a1a1a;
        margin: 28px 0 12px;
        text-align: left;
    }

    .hd-body ul,
    .hd-body ol {
        margin: 0 0 18px;
        padding-left: 22px;
    }

    .hd-body li {
        margin-bottom: 8px;
    }

    .hd-cta {
        margin-top: 32px;
    }

    .hd-cta a {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: #00afab;
        color: #fff;
        font-size: 15px;
        font-weight: 700;
        padding: 13px 28px;
        border-radius: 30px;
        text-decoration: none;
        transition: background 0.2s ease;
    }

    .hd-cta a:hover {
        background: #008b88;
    }

    /* ---- Sidebar (right): More to Discover ---- */
    .hd-sidebar {
        position: sticky;
        top: 100px;
    }

    .hd-sidebar-title {
        font-size: 13px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: #999;
        margin: 0 0 18px;
        padding-bottom: 12px;
        border-bottom: 2px solid #00afab;
        text-align: left;
    }

    .hd-side-card {
        display: flex;
        gap: 14px;
        text-decoration: none;
        margin-bottom: 18px;
        padding-bottom: 18px;
        border-bottom: 1px solid #f0f0f0;
        align-items: center;
    }

    .hd-side-card:last-child {
        border-bottom: none;
        margin-bottom: 0;
        padding-bottom: 0;
    }

    .hd-side-thumb {
        flex: 0 0 90px;
        width: 90px;
        height: 70px;
        border-radius: 8px;
        overflow: hidden;
        background: #f5f5f5;
    }

    .hd-side-thumb img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s ease;
    }

    .hd-side-card:hover .hd-side-thumb img {
        transform: scale(1.06);
    }

    .hd-side-body {
        flex: 1;
        min-width: 0;
    }

    .hd-side-date {
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.3px;
        color: #00afab;
        margin: 0 0 4px;
        text-align: left;
    }

    .hd-side-name {
        font-size: 14px;
        font-weight: 700;
        line-height: 1.35;
        color: #1a1a1a;
        margin: 0;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        transition: color 0.2s ease;
        text-align: left;
    }

    .hd-side-card:hover .hd-side-name {
        color: #00afab;
    }

    /* ---- Responsive ---- */
    @media (max-width: 900px) {
        .hd-layout {
            grid-template-columns: 1fr;
            gap: 40px;
        }

        .hd-sidebar {
            position: static;
        }

        /* On mobile, show related happenings as a 2-column grid */
        .hd-related-list {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }

        .hd-side-card {
            flex-direction: column;
            align-items: stretch;
            border-bottom: none;
            padding-bottom: 0;
            margin-bottom: 0;
        }

        .hd-side-thumb {
            flex: none;
            width: 100%;
            height: 130px;
        }
    }

    @media (max-width: 768px) {
        .hd-title {
            font-size: 28px;
        }
    }

    @media (max-width: 480px) {
        .hd-related-list {
            grid-template-columns: 1fr;
        }
    }
</style>

<div id="wrapper">
    <div class="content">
        <div class="hd-page">
            <div class="hd-wrap">
                <a href="<?= $this->Url->build('/happenings') ?>" class="hd-back">
                    <i class="fas fa-arrow-left"></i> Back to Happenings
                </a>

                <div class="hd-layout">
                    <!-- LEFT: main content -->
                    <div class="hd-main">
                        <h1 class="hd-title"><?= h($title) ?></h1>

                        <?php if (!empty($date)): ?>
                            <div class="hd-date-card">
                                <div class="hd-date-icon"><i class="fas fa-calendar-days"></i></div>
                                <div class="hd-date-text">
                                    <p class="hd-date-label">When</p>
                                    <p class="hd-date-value"><?= h($date) ?></p>
                                </div>
                            </div>
                        <?php endif; ?>

                        <div class="hd-hero">
                            <img src="<?= h($heroSrc) ?>" alt="<?= h($title) ?>"
                                data-original="<?= h($heroImg['original']) ?>"
                                data-fallback="<?= h($fallbackUrl) ?>"
                                onerror="if(this.dataset.original && this.src!==this.dataset.original){this.src=this.dataset.original;}else{this.onerror=null;this.src=this.dataset.fallback;}">
                        </div>

                        <div class="hd-body">
                            <?php if (!empty(trim(strip_tags((string)$bodyHtml)))): ?>
                                <?= $bodyHtml ?>
                            <?php else: ?>
                                <p><strong><?= h($title) ?></strong><?= !empty($date) ? ' &middot; <strong>' . h($date) . '</strong>' : '' ?> at Araneta City.</p>
                            <?php endif; ?>
                        </div>

                        <?php if (!empty($link) && $link !== '#'): ?>
                            <div class="hd-cta">
                                <a href="<?= h($link) ?>" target="_blank" rel="noopener">
                                    Learn More <i class="fas fa-external-link-alt"></i>
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- RIGHT: More to Discover sidebar -->
                    <?php if (!empty($relatedHappenings)): ?>
                        <aside class="hd-sidebar">
                            <p class="hd-sidebar-title">More to Discover</p>
                            <div class="hd-related-list">
                                <?php foreach ($relatedHappenings as $rel): ?>
                                    <?php
                                    $relImg = $buildImage($rel);
                                    $relSrc = !empty($relImg['thumb']) ? $relImg['thumb'] : $fallbackUrl;
                                    $relTitle = $rel['title'] ?? ($rel['event_title'] ?? 'Happening');
                                    $relDate = $rel['date'] ?? ($rel['display_date'] ?? '');
                                    $relSlug = $rel['slug'] ?? '';
                                    $relUrl = !empty($relSlug)
                                        ? $this->Url->build('/happening/' . $relSlug)
                                        : ($rel['link'] ?? '#');
                                    ?>
                                    <a href="<?= h($relUrl) ?>" class="hd-side-card">
                                        <div class="hd-side-thumb">
                                            <img src="<?= h($relSrc) ?>" alt="<?= h($relTitle) ?>"
                                                loading="lazy" decoding="async"
                                                data-original="<?= h($relImg['original']) ?>"
                                                data-fallback="<?= h($fallbackUrl) ?>"
                                                onerror="if(this.dataset.original && this.src!==this.dataset.original){this.src=this.dataset.original;}else{this.onerror=null;this.src=this.dataset.fallback;}">
                                        </div>
                                        <div class="hd-side-body">
                                            <?php if (!empty($relDate)): ?>
                                                <p class="hd-side-date"><?= h($relDate) ?></p>
                                            <?php endif; ?>
                                            <p class="hd-side-name"><?= h($relTitle) ?></p>
                                        </div>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        </aside>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

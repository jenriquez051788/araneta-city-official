<?php
/**
 * Event detail page.
 *
 * Modern, clean, left-aligned layout: main content (banner, title, date,
 * venue, description) on the left, a "More to Discover" sidebar on the right.
 *
 * @var \App\View\AppView $this
 * @var array $event The event data
 * @var array $relatedEvents Related events (max 4)
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

    return [
        'thumb' => $baseUrl . $dir . '/thumbnail-' . $file,
        'original' => $baseUrl . $dir . '/' . $file,
    ];
};

$fallbackUrl = $this->Url->image('no-image.jpg');

$title = $event['event_title'] ?? ($event['title'] ?? 'Event');
$date = $event['date'] ?? ($event['display_date'] ?? '');
$venue = $event['venue'] ?? '';
$link = $event['link'] ?? '';


// the event doesn't provide a custom label.
$buttonText = $event['button_text'] ?? ($event['buttonText'] ?? 'View Details');

// Full body text: prefer full description fields, fall back to short description.
$bodyHtml = $event['content']
    ?? ($event['fulltext']
    ?? ($event['fulldesc']
    ?? ($event['full_description']
    ?? ($event['description'] ?? ($event['introtext'] ?? '')))));

$heroImg = $buildImage($event);
$heroSrc = !empty($heroImg['original']) ? $heroImg['original'] : $fallbackUrl;

$this->assign('title', $title . ' - Araneta City');
?>

<style>
    .ed-page {
        background: #fff;
        padding: 36px 0 70px;
        text-align: left;
    }

    .ed-wrap {
        width: 92%;
        max-width: 1200px;
        margin: 0 auto;
        text-align: left;
    }

    .ed-back {
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

    .ed-main {
        text-align: left;
    }

    .ed-back:hover {
        color: #008b88;
    }

    /* Two-column layout: content left, sidebar right */
    .ed-layout {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 320px;
        gap: 48px;
        align-items: start;
    }

    /* ---- Main content (left) ---- */
    /* Prominent date and venue card so users spot the "when" and "where" immediately */
    .ed-meta-card {
        display: flex;
        flex-direction: column;
        gap: 14px;
        background: linear-gradient(135deg, #e6f6f5 0%, #f0faf9 100%);
        border: 1px solid #b8e6e4;
        border-left: 4px solid #00afab;
        border-radius: 12px;
        padding: 18px 22px;
        margin: 0 0 28px;
    }

    .ed-meta-row {
        display: flex;
        align-items: flex-start;
        gap: 14px;
    }

    .ed-meta-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 44px;
        height: 44px;
        border-radius: 10px;
        background: #00afab;
        color: #fff;
        font-size: 18px;
        flex-shrink: 0;
    }

    .ed-meta-text {
        text-align: left;
    }

    .ed-meta-label {
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        color: #00afab;
        margin: 0 0 2px;
    }

    .ed-meta-value {
        font-size: 16px;
        font-weight: 700;
        color: #1a1a1a;
        margin: 0;
        line-height: 1.3;
    }

    .ed-title {
        font-size: 36px;
        font-weight: 800;
        line-height: 1.15;
        color: #1a1a1a;
        margin: 0 0 26px;
        letter-spacing: -0.5px;
        text-align: left;
    }

    .ed-hero {
        width: 100%;
        border-radius: 14px;
        overflow: hidden;
        background: #f2f2f2;
        margin-bottom: 30px;
    }

    .ed-hero img {
        width: 100%;
        height: auto;
        display: block;
    }

    .ed-body {
        font-size: 16px;
        line-height: 1.8;
        color: #444;
        text-align: left;
    }

    .ed-body p {
        margin: 0 0 18px;
    }

    .ed-body img {
        max-width: 100%;
        height: auto;
        border-radius: 8px;
        margin: 18px 0;
    }

    .ed-body h2,
    .ed-body h3,
    .ed-body h4 {
        color: #1a1a1a;
        margin: 28px 0 12px;
        text-align: left;
    }

    .ed-body ul,
    .ed-body ol {
        margin: 0 0 18px;
        padding-left: 22px;
    }

    .ed-body li {
        margin-bottom: 8px;
    }

    .ed-cta {
        margin-top: 32px;
    }

    .ed-cta a {
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

    .ed-cta a:hover {
        background: #008b88;
    }

    /* ---- Sidebar (right): More to Discover ---- */
    .ed-sidebar {
        position: sticky;
        top: 100px;
    }

    .ed-sidebar-title {
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

    .ed-side-card {
        display: flex;
        gap: 14px;
        text-decoration: none;
        margin-bottom: 18px;
        padding-bottom: 18px;
        border-bottom: 1px solid #f0f0f0;
        align-items: center;
    }

    .ed-side-card:last-child {
        border-bottom: none;
        margin-bottom: 0;
        padding-bottom: 0;
    }

    .ed-side-thumb {
        flex: 0 0 90px;
        width: 90px;
        height: 70px;
        border-radius: 8px;
        overflow: hidden;
        background: #f5f5f5;
    }

    .ed-side-thumb img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s ease;
    }

    .ed-side-card:hover .ed-side-thumb img {
        transform: scale(1.06);
    }

    .ed-side-body {
        flex: 1;
        min-width: 0;
    }

    .ed-side-date {
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.3px;
        color: #00afab;
        margin: 0 0 4px;
        text-align: left;
    }

    .ed-side-name {
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

    .ed-side-card:hover .ed-side-name {
        color: #00afab;
    }

    /* ---- Responsive ---- */
    @media (max-width: 900px) {
        .ed-layout {
            grid-template-columns: 1fr;
            gap: 40px;
        }

        .ed-sidebar {
            position: static;
        }

        /* On mobile, show related events as a 2-column grid */
        .ed-related-list {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }

        .ed-side-card {
            flex-direction: column;
            align-items: stretch;
            border-bottom: none;
            padding-bottom: 0;
            margin-bottom: 0;
        }

        .ed-side-thumb {
            flex: none;
            width: 100%;
            height: 130px;
        }
    }

    @media (max-width: 768px) {
        .ed-title {
            font-size: 28px;
        }
    }

    @media (max-width: 480px) {
        .ed-related-list {
            grid-template-columns: 1fr;
        }
    }
</style>

<div id="wrapper">
    <div class="content">
        <div class="ed-page">
            <div class="ed-wrap">
                <a href="<?= $this->Url->build('/events') ?>" class="ed-back">
                    <i class="fas fa-arrow-left"></i> Back to Events
                </a>

                <div class="ed-layout">
                    <!-- LEFT: main content -->
                    <div class="ed-main">
                        <h1 class="ed-title"><?= h($title) ?></h1>

                        <!-- Date and Venue Card -->
                        <div class="ed-meta-card">
                            <?php if (!empty($date)): ?>
                                <div class="ed-meta-row">
                                    <div class="ed-meta-icon"><i class="fas fa-calendar-days"></i></div>
                                    <div class="ed-meta-text">
                                        <p class="ed-meta-label">When</p>
                                        <p class="ed-meta-value"><?= h($date) ?></p>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <?php if (!empty($venue)): ?>
                                <div class="ed-meta-row">
                                    <div class="ed-meta-icon"><i class="fas fa-map-marker-alt"></i></div>
                                    <div class="ed-meta-text">
                                        <p class="ed-meta-label">Where</p>
                                        <p class="ed-meta-value"><?= h($venue) ?></p>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="ed-hero">
                            <img src="<?= h($heroSrc) ?>" alt="<?= h($title) ?>"
                                data-original="<?= h($heroImg['original']) ?>"
                                data-fallback="<?= h($fallbackUrl) ?>"
                                onerror="if(this.dataset.original && this.src!==this.dataset.original){this.src=this.dataset.original;}else{this.onerror=null;this.src=this.dataset.fallback;}">
                        </div>

                        <div class="ed-body">
                            <?php if (!empty(trim(strip_tags((string)$bodyHtml)))): ?>
                                <?= $bodyHtml ?>
                            <?php else: ?>
                                <p><strong><?= h($title) ?></strong><?php if (!empty($date)): ?> &middot; <strong><?= h($date) ?></strong><?php endif; ?><?php if (!empty($venue)): ?> at <strong><?= h($venue) ?></strong><?php endif; ?> at Araneta City. Visit us to experience it.</p>
                            <?php endif; ?>
                        </div>

                        <?php if (!empty($link) && $link !== '#'): ?>
                            <div class="ed-cta">
                                <a href="<?= h($link) ?>" target="_blank" rel="noopener">
                                    <?= h($buttonText) ?> <i class="fas fa-external-link-alt"></i>
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- RIGHT: More to Discover sidebar -->
                    <?php if (!empty($relatedEvents)): ?>
                        <aside class="ed-sidebar">
                            <p class="ed-sidebar-title">More to Discover</p>
                            <div class="ed-related-list">
                                <?php foreach ($relatedEvents as $rel): ?>
                                    <?php
                                    $relImg = $buildImage($rel);
                                    $relSrc = !empty($relImg['thumb']) ? $relImg['thumb'] : $fallbackUrl;
                                    $relTitle = $rel['event_title'] ?? ($rel['title'] ?? 'Event');
                                    $relDate = $rel['date'] ?? ($rel['display_date'] ?? '');
                                    $relSlug = $rel['slug'] ?? '';
                                    $relUrl = !empty($relSlug)
                                        ? $this->Url->build('/event/' . $relSlug)
                                        : ($rel['link'] ?? '#');
                                    ?>
                                    <a href="<?= h($relUrl) ?>" class="ed-side-card">
                                        <div class="ed-side-thumb">
                                            <img src="<?= h($relSrc) ?>" alt="<?= h($relTitle) ?>"
                                                loading="lazy" decoding="async"
                                                data-original="<?= h($relImg['original']) ?>"
                                                data-fallback="<?= h($fallbackUrl) ?>"
                                                onerror="if(this.dataset.original && this.src!==this.dataset.original){this.src=this.dataset.original;}else{this.onerror=null;this.src=this.dataset.fallback;}">
                                        </div>
                                        <div class="ed-side-body">
                                            <?php if (!empty($relDate)): ?>
                                                <p class="ed-side-date"><?= h($relDate) ?></p>
                                            <?php endif; ?>
                                            <p class="ed-side-name"><?= h($relTitle) ?></p>
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


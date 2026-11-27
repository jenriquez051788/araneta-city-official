<?php
/**
 * Transport Detail Page
 * Modern, clean layout: hero header, left-aligned content, and a
 * sidebar of other transport options.
 *
 * @var \App\View\AppView $this
 * @var array $transport The transport option data
 * @var array $otherTransports Related transport options
 */

$baseUrl = env('API_BASE_URL', 'https://aci-cms.aranetacity.com');

/**
 * Pick a Font Awesome icon class from a transport title.
 */
$iconFor = function (string $title): string {
    $t = strtolower($title);
    if (str_contains($t, 'shuttle')) {
        return 'fas fa-shuttle-van';
    }
    if (str_contains($t, 'jeepney') || str_contains($t, 'jeep')) {
        return 'fas fa-bus-alt';
    }
    if (str_contains($t, 'parking')) {
        return 'fas fa-parking';
    }
    if (str_contains($t, 'taxi') || str_contains($t, 'grab')) {
        return 'fas fa-taxi';
    }
    if (str_contains($t, 'train') || str_contains($t, 'lrt') || str_contains($t, 'mrt')) {
        return 'fas fa-train';
    }
    if (str_contains($t, 'uv express') || str_contains($t, 'uv-express') || str_contains($t, 'van')) {
        return 'fas fa-shuttle-van';
    }
    if (str_contains($t, 'bike') || str_contains($t, 'cycle')) {
        return 'fas fa-bicycle';
    }
    if (str_contains($t, 'walk')) {
        return 'fas fa-walking';
    }

    return 'fas fa-bus';
};

$title = $transport['title'] ?? 'Transport Option';
$heroIcon = $iconFor($title);

$this->assign('title', $title . ' - Araneta City');
?>

<style>
    .td-page {
        background: #fff;
        padding: 32px 0 70px;
    }

    .td-wrap {
        width: 92%;
        max-width: 1150px;
        margin: 0 auto;
        text-align: left;
    }

    /* Back link */
    .td-back {
        display: flex;
        width: fit-content;
        align-items: center;
        gap: 8px;
        font-size: 14px;
        font-weight: 600;
        color: #00afab;
        text-decoration: none;
        margin: 0 0 26px;
    }

    .td-back:hover {
        color: #008b88;
    }

    /* Hero header */
    .td-hero {
        display: flex;
        align-items: center;
        gap: 24px;
        background: linear-gradient(135deg, #0a8b84 0%, #00afab 45%, #6b5ba8 100%);
        border-radius: 18px;
        padding: 40px 44px;
        margin-bottom: 40px;
        color: #fff;
        position: relative;
        overflow: hidden;
    }

    .td-hero::after {
        content: "";
        position: absolute;
        right: -40px;
        top: -40px;
        width: 220px;
        height: 220px;
        background: radial-gradient(circle, rgba(255, 255, 255, 0.12) 0%, transparent 70%);
        border-radius: 50%;
        pointer-events: none;
    }

    .td-hero-icon {
        flex-shrink: 0;
        width: 92px;
        height: 92px;
        border-radius: 20px;
        background: rgba(255, 255, 255, 0.18);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 44px;
        color: #fff;
    }

    .td-hero-text {
        position: relative;
        z-index: 1;
    }

    .td-hero-eyebrow {
        display: block;
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: rgba(255, 255, 255, 0.85);
        margin-bottom: 8px;
    }

    .td-hero h1 {
        font-size: 34px;
        font-weight: 800;
        line-height: 1.15;
        margin: 0;
        letter-spacing: -0.5px;
        color: #fff;
    }

    /* Two-column layout: content left, sidebar right */
    .td-layout {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 300px;
        gap: 44px;
        align-items: start;
    }

    /* Content card */
    .td-content {
        font-size: 16px;
        line-height: 1.8;
        color: #444;
        text-align: left;
    }

    .td-content p {
        margin: 0 0 18px;
    }

    .td-content h2,
    .td-content h3,
    .td-content h4 {
        color: #1a1a1a;
        font-weight: 700;
        margin: 28px 0 12px;
        padding-bottom: 8px;
        border-bottom: 2px solid #e6f6f5;
    }

    .td-content ul,
    .td-content ol {
        margin: 0 0 18px;
        padding-left: 22px;
    }

    .td-content li {
        margin-bottom: 8px;
    }

    .td-content img {
        max-width: 100%;
        height: auto;
        border-radius: 10px;
        margin: 18px 0;
    }

    .td-content a {
        color: #00afab;
        font-weight: 600;
    }

    .td-empty {
        color: #999;
        font-style: italic;
    }

    /* Sidebar: other transport options */
    .td-sidebar {
        position: sticky;
        top: 100px;
    }

    .td-sidebar-title {
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

    .td-side-card {
        display: flex;
        align-items: center;
        gap: 14px;
        text-decoration: none;
        padding: 12px 14px;
        border-radius: 12px;
        border: 1px solid #eee;
        margin-bottom: 12px;
        transition: all 0.25s ease;
    }

    .td-side-card:hover {
        border-color: #00afab;
        background: #f7fdfc;
        transform: translateX(3px);
    }

    .td-side-icon {
        flex-shrink: 0;
        width: 46px;
        height: 46px;
        border-radius: 10px;
        background: #e6f6f5;
        color: #00afab;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        transition: all 0.25s ease;
    }

    .td-side-card:hover .td-side-icon {
        background: #00afab;
        color: #fff;
    }

    .td-side-name {
        font-size: 14px;
        font-weight: 700;
        color: #1a1a1a;
        line-height: 1.35;
    }

    .td-viewall {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        margin-top: 8px;
        font-size: 14px;
        font-weight: 700;
        color: #00afab;
        text-decoration: none;
    }

    .td-viewall:hover {
        color: #008b88;
    }

    /* Responsive */
    @media (max-width: 900px) {
        .td-layout {
            grid-template-columns: 1fr;
            gap: 40px;
        }

        .td-sidebar {
            position: static;
        }

        .td-side-list {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 12px;
        }

        .td-side-card {
            margin-bottom: 0;
        }
    }

    @media (max-width: 768px) {
        .td-hero {
            flex-direction: column;
            align-items: flex-start;
            gap: 18px;
            padding: 30px 26px;
        }

        .td-hero h1 {
            font-size: 26px;
        }

        .td-hero-icon {
            width: 72px;
            height: 72px;
            font-size: 34px;
        }
    }

    @media (max-width: 480px) {
        .td-side-list {
            grid-template-columns: 1fr;
        }
    }
</style>

<div id="wrapper">
    <div class="content">
        <div class="td-page">
            <div class="td-wrap">
                <!-- Back link -->
                <a href="<?= $this->Url->build('/transport') ?>" class="td-back">
                    <i class="fas fa-arrow-left"></i> Back to Transport Guide
                </a>

                <!-- Hero header -->
                <div class="td-hero">
                    <div class="td-hero-icon"><i class="<?= $heroIcon ?>"></i></div>
                    <div class="td-hero-text">
                        <span class="td-hero-eyebrow">Getting Around Araneta City</span>
                        <h1><?= h($title) ?></h1>
                    </div>
                </div>

                <!-- Two-column layout -->
                <div class="td-layout">
                    <!-- LEFT: content -->
                    <div class="td-content">
                        <?php if (!empty(trim(strip_tags((string)($transport['content'] ?? ''))))): ?>
                            <?= $transport['content'] ?>
                        <?php else: ?>
                            <p class="td-empty">Details for this transport option are coming soon.</p>
                        <?php endif; ?>
                    </div>

                    <!-- RIGHT: other transport options -->
                    <?php if (!empty($otherTransports)): ?>
                        <aside class="td-sidebar">
                            <p class="td-sidebar-title">Other Transport Options</p>
                            <div class="td-side-list">
                                <?php foreach ($otherTransports as $related): ?>
                                    <?php
                                    $relTitle = $related['title'] ?? '';
                                    $relSlug = $related['slug'] ?? '';
                                    $relIcon = $iconFor($relTitle);
                                    ?>
                                    <a href="<?= $this->Url->build('/transport/' . $relSlug) ?>" class="td-side-card">
                                        <div class="td-side-icon"><i class="<?= $relIcon ?>"></i></div>
                                        <div class="td-side-name"><?= h($relTitle) ?></div>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                            <a href="<?= $this->Url->build('/transport') ?>" class="td-viewall">
                                View all <i class="fas fa-arrow-right"></i>
                            </a>
                        </aside>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="limit-box fl-wrap"></div>
    </div>
</div>

<script>
    window.scrollTo(0, 0);
</script>

<?php
/**
 * Office Detail Page
 * Modern, clean two-column detail layout with a banner hero.
 *
 * @var \App\View\AppView $this
 * @var array $office The office data
 * @var array $relatedOffices Related offices
 */

$baseUrl = env('API_BASE_URL', 'https://aci-cms.aranetacity.com');
$fallbackImg = $this->Url->image('no-image.jpg');

/**
 * Build an office image URL. The API stores img_dir as a filesystem path
 * like "webroot\files\Offices\img\" — normalize it to a public web path.
 */
$officeImageUrl = function ($img, $dir) use ($baseUrl) {
    if (empty($img) || empty($dir)) {
        return null;
    }
    // Office images live at /files/offices/{img_dir}/{img} on the CMS, the same
    // pattern used by hotels/establishments/events. img_dir is the per-record
    // folder (usually a UUID).
    return $baseUrl . '/files/offices/' . rawurlencode($dir) . '/' . rawurlencode($img);
};

$officeTitle = $office['title'] ?? 'Office';
$subtitle = $office['subtitle'] ?? '';
$imageUrl = $officeImageUrl($office['img'] ?? '', $office['img_dir'] ?? '') ?? $fallbackImg;

// Description HTML
$descHtml = !empty($office['content']) ? $office['content'] : '';

$createdStr = !empty($office['created']) ? date('M Y', strtotime($office['created'])) : '';
$updatedStr = !empty($office['modified']) ? date('M d, Y', strtotime($office['modified'])) : '';

$this->assign('title', $officeTitle . ' - Araneta City');
?>

<style>
    .od-page {
        background: #fff;
        padding: 32px 0 70px;
        text-align: left;
    }

    .od-wrap {
        width: 92%;
        max-width: 1200px;
        margin: 0 auto;
    }

    /* Breadcrumb */
    .od-crumb {
        font-size: 13px;
        color: #999;
        margin-bottom: 20px;
    }

    .od-crumb a {
        color: #999;
        text-decoration: none;
    }

    .od-crumb a:hover {
        color: #00afab;
    }

    .od-crumb .current {
        color: #6b2d7b;
        font-weight: 600;
    }

    /* Hero: portrait image left, info right */
    .od-hero {
        display: flex;
        gap: 30px;
        align-items: center;
        margin-bottom: 26px;
    }

    .od-hero-media {
        flex: 0 0 240px;
        max-width: 240px;
        aspect-ratio: 3 / 4;
        border-radius: 16px;
        overflow: hidden;
        background: #f0f0f0;
        box-shadow: 0 8px 26px rgba(0, 0, 0, 0.12);
    }

    .od-hero-media img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    .od-hero-body {
        flex: 1 1 auto;
        min-width: 0;
    }

    .od-hero-eyebrow {
        display: inline-block;
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: #00afab;
        background: #e6f6f5;
        padding: 6px 14px;
        border-radius: 20px;
        margin-bottom: 16px;
    }

    .od-hero-title {
        font-size: 34px;
        font-weight: 800;
        color: #1a1a1a;
        margin: 0;
        line-height: 1.1;
        letter-spacing: -0.5px;
    }

    .od-hero-subtitle {
        font-size: 16px;
        color: #777;
        margin: 12px 0 0;
        line-height: 1.6;
    }

    .od-hero-loc {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 14px;
        font-weight: 600;
        color: #6b2d7b;
        margin: 16px 0 0;
    }

    .od-hero-loc i {
        color: #00afab;
    }

    /* Two-column layout */
    .od-layout {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 320px;
        gap: 30px;
        align-items: start;
    }

    /* Card */
    .od-card {
        border: 1px solid #eee;
        border-radius: 14px;
        padding: 26px 28px;
        margin-bottom: 22px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.03);
    }

    .od-card-title {
        font-size: 20px;
        font-weight: 800;
        color: #1a1a1a;
        margin: 0 0 16px;
        padding-bottom: 14px;
        border-bottom: 1px solid #f0f0f0;
        letter-spacing: -0.2px;
        text-align: left;
    }

    /* Description content */
    .od-desc {
        font-size: 15px;
        line-height: 1.75;
        color: #555;
        text-align: left;
    }

    .od-desc ul,
    .od-desc ol {
        margin: 0 0 16px;
        padding-left: 20px;
    }

    .od-desc li {
        margin-bottom: 6px;
        color: #1a1a1a;
    }

    .od-desc p {
        margin: 0 0 14px;
    }

    .od-desc h2,
    .od-desc h3,
    .od-desc h4 {
        color: #1a1a1a;
        margin: 22px 0 10px;
        text-align: left;
    }

    .od-desc img {
        max-width: 100%;
        height: auto;
        border-radius: 8px;
        margin: 14px 0;
    }

    /* Sidebar */
    .od-sidebar {
        position: sticky;
        top: 100px;
    }

    .od-address .lbl {
        display: block;
        font-weight: 700;
        color: #1a1a1a;
        font-size: 13px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 4px;
    }

    .od-address .val {
        color: #666;
        font-size: 14px;
        line-height: 1.5;
    }

    .od-socials {
        display: flex;
        gap: 10px;
        margin-top: 18px;
    }

    .od-social {
        width: 38px;
        height: 38px;
        border-radius: 50%;
        background: #00afab;
        color: #fff;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 15px;
        text-decoration: none;
        transition: transform 0.2s ease, background 0.2s ease;
    }

    .od-social:hover {
        transform: translateY(-2px);
        background: #008b88;
        color: #fff;
    }

    .od-side-row {
        display: flex;
        justify-content: space-between;
        gap: 12px;
        padding: 8px 0;
        font-size: 14px;
        border-bottom: 1px solid #f5f5f5;
    }

    .od-side-row:last-child {
        border-bottom: none;
    }

    .od-side-row .lbl {
        font-weight: 700;
        color: #1a1a1a;
    }

    .od-side-row .val {
        color: #888;
    }

    /* Nearby */
    .od-nearby-item {
        display: flex;
        align-items: center;
        gap: 12px;
        text-decoration: none;
        padding: 10px;
        border-radius: 10px;
        transition: background 0.2s ease;
    }

    .od-nearby-item:hover {
        background: #f7fdfc;
    }

    .od-nearby-img {
        width: 54px;
        height: 54px;
        border-radius: 10px;
        overflow: hidden;
        flex-shrink: 0;
        background: #f4f4f4;
        border: 1px solid #eee;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 4px;
    }

    .od-nearby-img img {
        max-width: 100%;
        max-height: 100%;
        object-fit: contain;
    }

    .od-nearby-name {
        font-size: 15px;
        font-weight: 700;
        color: #1a1a1a;
        margin: 0 0 2px;
    }

    .od-nearby-loc {
        font-size: 13px;
        color: #6b2d7b;
        margin: 0;
    }

    /* Responsive */
    @media (max-width: 900px) {
        .od-layout {
            grid-template-columns: 1fr;
        }

        .od-sidebar {
            position: static;
        }

        .od-hero-title {
            font-size: 30px;
        }
    }

    @media (max-width: 600px) {
        .od-hero {
            flex-direction: column;
            align-items: flex-start;
            gap: 20px;
        }

        .od-hero-media {
            flex: none;
            width: 200px;
            max-width: 200px;
        }

        .od-hero-title {
            font-size: 26px;
        }

        .od-hero-loc {
            font-size: 13px;
        }
    }
</style>

<div id="wrapper">
    <div class="content">
        <div class="od-page">
            <div class="od-wrap">
                <!-- Breadcrumb -->
                <div class="od-crumb">
                    <a href="/">Explore</a> / <a href="/offices">Offices</a> / <span class="current"><?= h($officeTitle) ?></span>
                </div>

                <!-- Two-column: portrait image + info (left), sidebar (right) -->
                <div class="od-layout">
                    <!-- Left column: image + info side by side, then description -->
                    <div class="od-main">
                        <div class="od-hero">
                            <div class="od-hero-media">
                                <img src="<?= $imageUrl ?>" alt="<?= h($officeTitle) ?>"
                                    onerror="this.onerror=null;this.src='<?= $fallbackImg ?>';">
                            </div>
                            <div class="od-hero-body">
                                <span class="od-hero-eyebrow">Offices at Araneta City</span>
                                <h1 class="od-hero-title"><?= h($officeTitle) ?></h1>
                                <?php if (!empty($subtitle)): ?>
                                    <p class="od-hero-loc">
                                        <i class="fas fa-map-marker-alt"></i>
                                        <?= h($subtitle) ?>
                                    </p>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Overview / description -->
                        <div class="od-card">
                            <h2 class="od-card-title">Overview</h2>
                            <div class="od-desc">
                                <?php if (!empty(trim(strip_tags((string)$descHtml)))): ?>
                                    <?= $descHtml ?>
                                <?php else: ?>
                                    <p>For details about this office, please contact our leasing team at
                                    <a href="mailto:leaseinquiry@aranetagroup.com" class="od-lease-link">leaseinquiry@aranetagroup.com</a>.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Right sidebar -->
                    <div class="od-sidebar">
                        <!-- Location / Contacts -->
                        <div class="od-card">
                            <h2 class="od-card-title">Leasing Inquiries</h2>
                            <p class="od-address">
                                <span class="lbl">Email</span>
                                <span class="val">
                                    <a href="mailto:leaseinquiry@aranetagroup.com" class="od-lease-link">
                                        <i class="fas fa-envelope"></i> leaseinquiry@aranetagroup.com
                                    </a>
                                </span>
                            </p>
                            <!-- <div class="od-socials">
                                <a href="#" target="_blank" rel="noopener" class="od-social" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                                <a href="#" target="_blank" rel="noopener" class="od-social" aria-label="X"><i class="fab fa-x-twitter"></i></a>
                                <a href="#" target="_blank" rel="noopener" class="od-social" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                            </div> -->
                        </div>

                        

                        <!-- Nearby -->
                        <?php if (!empty($relatedOffices)): ?>
                            <div class="od-card">
                                <h2 class="od-card-title">Nearby Offices</h2>
                                <?php foreach (array_slice($relatedOffices, 0, 3) as $rel): ?>
                                    <?php
                                    $relTitle = $rel['title'] ?? 'Office';
                                    $relSlug = $rel['slug'] ?? '';
                                    $relUrl = $officeImageUrl($rel['img'] ?? '', $rel['img_dir'] ?? '') ?? $fallbackImg;
                                    ?>
                                    <a href="/office/<?= h($relSlug) ?>" class="od-nearby-item">
                                        <div class="od-nearby-img">
                                            <img src="<?= $relUrl ?>" alt="<?= h($relTitle) ?>"
                                                onerror="this.onerror=null;this.src='<?= $fallbackImg ?>';">
                                        </div>
                                        <div>
                                            <p class="od-nearby-name"><?= h($relTitle) ?></p>
                                        </div>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="limit-box fl-wrap"></div>
    </div>
</div>

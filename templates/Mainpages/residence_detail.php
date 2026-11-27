<?php
/**
 * Residence Detail Page
 * Modeled on the Office Detail page: hero (image + info) on the left,
 * a sidebar (location + nearby) on the right.
 *
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Residence $residence
 * @var array $relatedResidences Other residences for the sidebar
 */

$fallbackImg = $this->Url->image('no-image.jpg');
$baseUrl = env('API_BASE_URL', 'https://aci-cms.aranetacity.com');

/**
 * Resolve an image for a residence from the API's img/img_dir fields.
 * Same CMS pattern as offices/hotels: /files/residences/{dir}/{img}.
 */
$residenceImage = function ($res) use ($fallbackImg, $baseUrl) {
    $img = $res['img'] ?? '';
    $dir = $res['img_dir'] ?? '';
    if (!empty($img) && !empty($dir)) {
        return $baseUrl . '/files/residences/' . rawurlencode($dir) . '/' . rawurlencode($img);
    }
    return $fallbackImg;
};

/**
 * Pull the first usable image out of the CMS "content" HTML. Used as a hero
 * fallback when a residence has no img/img_dir of its own (e.g. Manhattan
 * Gardens). Relative theme paths (img/..) are normalized to absolute /img/..
 * which is where those images actually live in this app's web root.
 */
$firstContentImage = function (string $html): string {
    if (trim($html) === '' || !preg_match('#<img[^>]+src=["\']([^"\']+)["\']#i', $html, $m)) {
        return '';
    }
    $src = $m[1];
    if (!preg_match('#^(https?:)?//#', $src)) {
        $src = '/' . ltrim(ltrim($src, '.'), '/');
    }
    return $src;
};

$title = $residence['title'] ?? 'Residence';
$subtitle = $residence['subtitle'] ?? '';
// The API has no dedicated address field; the address is stored in "subtitle".
// Use it as the location, but only when it looks like an address (short, has a
// comma) rather than a long marketing paragraph — otherwise fall back.
$location = !empty($residence['location']) ? $residence['location'] : 'Araneta City, Quezon City';
if (empty($residence['location']) && !empty($subtitle)
    && mb_strlen($subtitle) <= 120 && str_contains($subtitle, ',')) {
    $location = $subtitle;
}
$website = $residence['website'] ?? '';

// Hero image: prefer the record's own image, then the first image embedded in
// its content HTML, then the generic placeholder.
$imageUrl = $residenceImage($residence);
if ($imageUrl === $fallbackImg) {
    $fromContent = $firstContentImage((string)($residence['content'] ?? ''));
    if ($fromContent !== '') {
        $imageUrl = $fromContent;
    }
}

/**
 * Clean the CMS "content" HTML for display on this page.
 *
 * The stored content was authored for the OLD frontend theme and includes
 * things that break here: a <pre> code dump, parallax/owl-carousel <section>s
 * that need plugins we don't load, a "cascading-images" block whose images
 * don't exist, and relative image paths (img/... , ./img/...) that 404 when
 * resolved against /residence/{slug}. We:
 *   1. Remove the plugin/theme blocks (<pre>, <section>, .cascading-images).
 *   2. Rewrite relative image src paths to absolute /img/... (served from the
 *      app web root, where the residence images actually live).
 *   3. Drop any images that still can't resolve via an onerror handler.
 */
$cleanResidenceContent = function (string $html): string {
    if (trim($html) === '') {
        return '';
    }

    $doc = new \DOMDocument();
    // Load as UTF-8 without adding <html>/<body> wrappers or doctype.
    libxml_use_internal_errors(true);
    $doc->loadHTML(
        '<?xml encoding="UTF-8"><div id="__root">' . $html . '</div>',
        LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD
    );
    libxml_clear_errors();

    $xpath = new \DOMXPath($doc);

    // 1. Remove theme blocks that don't belong on this page.
    //    - <pre> code dumps
    //    - <section> parallax / carousel wrappers
    //    - .cascading-images wrappers (missing images)
    $removeQueries = [
        '//pre',
        '//section',
        '//*[contains(concat(" ", normalize-space(@class), " "), " cascading-images-wrapper ")]',
    ];
    foreach ($removeQueries as $q) {
        $nodes = $xpath->query($q);
        // Iterate over a static list (removing during a live NodeList is unsafe).
        $toRemove = [];
        foreach ($nodes as $n) {
            $toRemove[] = $n;
        }
        foreach ($toRemove as $n) {
            if ($n->parentNode) {
                $n->parentNode->removeChild($n);
            }
        }
    }

    // 2. Rewrite relative image paths to absolute /img/... and add a graceful
    //    fallback so any still-missing image hides itself instead of showing a
    //    broken icon.
    foreach ($xpath->query('//img') as $img) {
        /** @var \DOMElement $img */
        $src = $img->getAttribute('src');
        if ($src !== '' && !preg_match('#^(https?:)?//#', $src)) {
            // Normalize "./img/..", "img/.." and "/img/.." to "/img/..".
            $src = ltrim($src, '.');
            $src = '/' . ltrim($src, '/');
            $img->setAttribute('src', $src);
        }
        $img->setAttribute('loading', 'lazy');
        $img->setAttribute('onerror', "this.style.display='none';");
        // Drop theme animation hooks that would hide the image (opacity:0).
        $img->removeAttribute('data-appear-animation');
        $img->removeAttribute('class');
    }

    // Extract the inner HTML of our wrapper div.
    $root = $doc->getElementById('__root');
    if (!$root) {
        return '';
    }
    $out = '';
    foreach ($root->childNodes as $child) {
        $out .= $doc->saveHTML($child);
    }

    return $out;
};

// Body: sanitized rich HTML content from the API.
$bodyHtml = !empty($residence['content']) ? $cleanResidenceContent((string)$residence['content']) : '';

$this->assign('title', $title . ' - Araneta City');
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

    /* Hero: image left, info right */
    .od-hero {
        display: flex;
        gap: 30px;
        align-items: center;
        margin-bottom: 26px;
    }

    .od-hero-media {
        flex: 0 0 260px;
        max-width: 260px;
        aspect-ratio: 4 / 5;
        border-radius: 16px;
        overflow: hidden;
        background: #f0f0f0;
        box-shadow: 0 8px 26px rgba(0, 0, 0, 0.12);
    }

    .od-hero-media img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        object-position: center top;
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
        color: #6b2d7b;
        background: #f3eef6;
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
        font-size: 15px;
        color: #777;
        margin: 12px 0 0;
        line-height: 1.6;
    }

    .od-hero-loc {
        display: flex;
        align-items: flex-start;
        gap: 8px;
        font-size: 14px;
        font-weight: 600;
        color: #6b2d7b;
        margin: 14px 0 0;
    }

    .od-hero-loc i {
        color: #00afab;
        margin-top: 3px;
    }

    .od-hero-cta {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: #6b2d7b;
        color: #fff;
        font-size: 14px;
        font-weight: 700;
        padding: 11px 24px;
        border-radius: 30px;
        text-decoration: none;
        margin-top: 18px;
        transition: background 0.25s ease;
    }

    .od-hero-cta:hover {
        background: #582465;
        color: #fff;
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

    /* Towers / phases pills */
    .od-towers {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }

    .od-tower {
        font-size: 13px;
        font-weight: 600;
        color: #444;
        border: 1px solid #e2e2e2;
        border-radius: 20px;
        padding: 8px 16px;
    }

    /* Gallery */
    .od-gallery {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 14px;
    }

    .od-gallery-item {
        aspect-ratio: 4 / 3;
        border-radius: 10px;
        overflow: hidden;
        background: #f4f4f4;
        display: block;
    }

    .od-gallery-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
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
    }

    .od-nearby-img img {
        width: 100%;
        height: 100%;
        object-fit: cover;
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

        .od-gallery {
            grid-template-columns: repeat(2, 1fr);
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
            width: 220px;
            max-width: 220px;
        }

        .od-hero-title {
            font-size: 26px;
        }

        .od-gallery {
            grid-template-columns: 1fr;
        }
    }
</style>

<div id="wrapper">
    <div class="content">
        <div class="od-page">
            <div class="od-wrap">
                <!-- Breadcrumb -->
                <div class="od-crumb">
                    <a href="/">Explore</a> / <a href="/residences">Residences</a> / <span class="current"><?= h($title) ?></span>
                </div>

                <!-- Two-column: hero image + info (left), sidebar (right) -->
                <div class="od-layout">
                    <div class="od-main">
                        <div class="od-hero">
                            <div class="od-hero-media">
                                <img src="<?= h($imageUrl) ?>" alt="<?= h($title) ?>"
                                    onerror="this.onerror=null;this.src='<?= $fallbackImg ?>';">
                            </div>
                            <div class="od-hero-body">
                                <span class="od-hero-eyebrow">Residences at Araneta City</span>
                                <h1 class="od-hero-title"><?= h($title) ?></h1>
                                <?php if (!empty($subtitle) && $subtitle !== $location): ?>
                                    <p class="od-hero-subtitle"><?= h($subtitle) ?></p>
                                <?php endif; ?>
                                <p class="od-hero-loc">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <?= h($location) ?>
                                </p>
                                <?php if (!empty($website)): ?>
                                    <a href="<?= h($website) ?>" target="_blank" rel="noopener" class="od-hero-cta">
                                        Visit Website &rarr;
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Overview / description -->
                        <div class="od-card">
                            <h2 class="od-card-title">Overview</h2>
                            <div class="od-desc">
                                <?php if (!empty(trim(strip_tags((string)$bodyHtml)))): ?>
                                    <?= $bodyHtml ?>
                                <?php else: ?>
                                    <p><?= h($title) ?> is located at Araneta City, Quezon City.</p>
                                <?php endif; ?>
                            </div>
                        </div>

                    </div>

                    <!-- Right sidebar -->
                    <div class="od-sidebar">
                        <!-- Location -->
                        <div class="od-card">
                            <h2 class="od-card-title">Location</h2>
                            <p class="od-address">
                                <span class="lbl">Address</span>
                                <span class="val"><?= h($location) ?></span>
                            </p>
                        </div>

                        <!-- Nearby -->
                        <?php if (!empty($relatedResidences)): ?>
                            <div class="od-card">
                                <h2 class="od-card-title">Nearby Residences</h2>
                                <?php foreach ($relatedResidences as $rel): ?>
                                    <?php
                                    $relTitle = $rel['title'] ?? 'Residence';
                                    $relSlug = $rel['slug'] ?? '';
                                    $relImg = $residenceImage($rel);
                                    ?>
                                    <a href="<?= $relSlug !== '' ? '/residence/' . h(rawurlencode($relSlug)) : '#' ?>" class="od-nearby-item">
                                        <div class="od-nearby-img">
                                            <img src="<?= h($relImg) ?>" alt="<?= h($relTitle) ?>"
                                                onerror="this.onerror=null;this.src='<?= $fallbackImg ?>';">
                                        </div>
                                        <div>
                                            <p class="od-nearby-name"><?= h($relTitle) ?></p>
                                            <p class="od-nearby-loc">Araneta City</p>
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

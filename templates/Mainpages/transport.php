<?php
/**
 * Transport Guide Page
 * Modern, clean layout: how-to-get-here cards + at-a-glance lists.
 */

$baseUrl = env('API_BASE_URL', 'https://aci-cms.aranetacity.com');

/**
 * Pick a Font Awesome icon based on a transport title.
 */
$transportIcon = function ($title) {
    $t = strtolower($title);
    if (strpos($t, 'train') !== false || strpos($t, 'mrt') !== false || strpos($t, 'lrt') !== false) {
        return 'fas fa-train';
    }
    if (strpos($t, 'bus') !== false) {
        return 'fas fa-bus';
    }
    if (strpos($t, 'jeep') !== false) {
        return 'fas fa-shuttle-van';
    }
    if (strpos($t, 'uv express') !== false || strpos($t, 'shuttle') !== false || strpos($t, 'e-shuttle') !== false) {
        return 'fas fa-van-shuttle';
    }
    if (strpos($t, 'taxi') !== false || strpos($t, 'grab') !== false) {
        return 'fas fa-taxi';
    }
    if (strpos($t, 'parking') !== false) {
        return 'fas fa-square-parking';
    }
    return 'fas fa-map-marker-alt';
};

/**
 * Turn a transport's HTML content into a short plain-text excerpt.
 */
$transportExcerpt = function ($content, $limit = 110) {
    $text = trim(preg_replace('/\s+/', ' ', strip_tags((string)$content)));
    if ($text === '') {
        return '';
    }
    if (mb_strlen($text) > $limit) {
        $text = mb_substr($text, 0, $limit) . '…';
    }
    return $text;
};
?>

<style>
    .tp-page {
        background: #fff;
        padding: 46px 0 70px;
        text-align: left;
    }

    .tp-page * {
        text-align: left;
    }

    .tp-wrap {
        width: 92%;
        max-width: 1200px;
        margin: 0 auto;
    }

    /* Header */
    .tp-eyebrow {
        display: inline-block;
        color: #6b2d7b;
        font-size: 13px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: 8px;
    }

    .tp-title {
        font-size: 44px;
        font-weight: 800;
        color: #1a1a1a;
        margin: 0 0 10px;
        letter-spacing: -0.5px;
        line-height: 1.05;
    }

    .tp-subtitle {
        font-size: 16px;
        color: #777;
        margin: 0 0 44px;
        max-width: 460px;
        line-height: 1.5;
    }

    .tp-section-label {
        font-size: 13px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: #999;
        margin: 0 0 20px;
    }

    /* How-to-get-here cards */
    .tp-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 22px;
        margin-bottom: 56px;
    }

    .tp-card {
        display: block;
        border: 1px solid #eee;
        border-radius: 14px;
        padding: 24px 24px 26px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.03);
        transition: transform 0.25s ease, box-shadow 0.25s ease;
        text-decoration: none;
        color: inherit;
    }

    a.tp-card:hover {
        border-color: #b8e6e4;
    }

    .tp-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 22px rgba(0, 0, 0, 0.08);
    }

    .tp-card-icon {
        width: 46px;
        height: 46px;
        border-radius: 12px;
        background: #e6f6f5;
        color: #00afab;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        margin-bottom: 18px;
    }

    .tp-card-title {
        font-size: 17px;
        font-weight: 700;
        color: #1a1a1a;
        margin: 0 0 8px;
    }

    .tp-card-desc {
        font-size: 14px;
        line-height: 1.6;
        color: #888;
        margin: 0;
    }

    /* At a glance */
    .tp-glance {
        border-top: 1px solid #eee;
        padding-top: 40px;
    }

    .tp-glance-cols {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 50px;
    }

    .tp-glance-title {
        font-size: 20px;
        font-weight: 800;
        color: #1a1a1a;
        margin: 0 0 18px;
    }

    .tp-list {
        list-style: none;
        margin: 0 0 18px;
        padding: 0;
    }

    .tp-list li {
        position: relative;
        padding: 12px 0 12px 20px;
        font-size: 15px;
        font-weight: 600;
        color: #1a1a1a;
        border-bottom: 1px solid #f2f2f2;
    }

    .tp-list li:last-child {
        border-bottom: none;
    }

    .tp-list li::before {
        content: "";
        position: absolute;
        left: 0;
        top: 50%;
        transform: translateY(-50%);
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: #00afab;
    }

    .tp-glance-note {
        font-size: 14px;
        line-height: 1.6;
        color: #888;
        margin: 0;
    }

    /* Responsive */
    @media (max-width: 991px) {
        .tp-grid {
            grid-template-columns: repeat(2, 1fr);
        }

        .tp-title {
            font-size: 36px;
        }
    }

    @media (max-width: 700px) {
        .tp-grid {
            grid-template-columns: 1fr;
        }

        .tp-glance-cols {
            grid-template-columns: 1fr;
            gap: 34px;
        }

        .tp-title {
            font-size: 30px;
        }
    }
</style>

<div id="wrapper">
    <div class="content">
        <div class="tp-page">
            <div class="tp-wrap">
                <!-- Header -->
                <span class="tp-eyebrow">Getting Around</span>
                <h1 class="tp-title">Transport Guide</h1>
                <p class="tp-subtitle">Araneta City is one of the most accessible destinations in Metro Manila, served
                    by multiple transit options.</p>

                <!-- How to Get Here -->
                <p class="tp-section-label">How to Get Here</p>
                <?php if (empty($transports)): ?>
                    <p class="tp-glance-note">No transport options available at the moment.</p>
                <?php else: ?>
                    <div class="tp-grid">
                        <?php foreach ($transports as $transport): ?>
                            <?php
                            $tTitle = $transport['title'] ?? 'Transport';
                            $tSlug = $transport['slug'] ?? '';
                            $tIcon = $transportIcon($tTitle);
                            $tDesc = $transportExcerpt($transport['content'] ?? '');
                            $tLink = !empty($tSlug) ? '/transport/' . $tSlug : null;
                            ?>
                            <?php if ($tLink): ?>
                                <a href="<?= h($tLink) ?>" class="tp-card">
                            <?php else: ?>
                                <div class="tp-card">
                            <?php endif; ?>
                                    <div class="tp-card-icon"><i class="<?= $tIcon ?>"></i></div>
                                    <h3 class="tp-card-title"><?= h($tTitle) ?></h3>
                                    <?php if (!empty($tDesc)): ?>
                                        <p class="tp-card-desc"><?= h($tDesc) ?></p>
                                    <?php endif; ?>
                            <?php if ($tLink): ?>
                                </a>
                            <?php else: ?>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <!-- At a Glance -->
                <div class="tp-glance">
                    <p class="tp-section-label">At a Glance</p>
                    <div class="tp-glance-cols">
                        <!-- Train Stations -->
                        <div>
                            <h3 class="tp-glance-title">Train Stations</h3>
                            <ul class="tp-list">
                                <li>MRT-3 Araneta Center&ndash;Cubao Station (EDSA)</li>
                                <li>LRT-2 Araneta Center&ndash;Cubao Station (Aurora Blvd.)</li>
                            </ul>
                            <p class="tp-glance-note">Both stations connect to the complex via elevated walkways and
                                pedestrian bridges, with direct access to Gateway Mall and other buildings.</p>
                        </div>

                        <!-- Bus & Road Transport -->
                        <div>
                            <h3 class="tp-glance-title">Bus &amp; Road Transport</h3>
                            <ul class="tp-list">
                                <li>Araneta City Bus Station</li>
                                <li>UV Express Terminal</li>
                                <li>Beep Jeep / E-Shuttle</li>
                                <li>Jeepney Terminal</li>
                                <li>Taxi Bay / Grab Pick-up</li>
                            </ul>
                            <p class="tp-glance-note">Multiple land transport options are available within and around the
                                complex for convenient access from all parts of Metro Manila and nearby provinces.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="limit-box fl-wrap"></div>
    </div>
</div>

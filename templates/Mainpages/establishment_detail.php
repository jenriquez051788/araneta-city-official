<?php
/**
 * Establishment Detail Page
 * Modern, clean detail layout: info left (logo, title, tags, schedule,
 * get directions, socials), facade banner on the right (landscape),
 * followed by "You Might Also Love" related stores in the same
 * building and category.
 */

$baseUrl = env('API_BASE_URL', 'https://aci-cms.aranetacity.com');

// Logo image (image / img_dir)
$logoImg = $establishment['image'] ?? '';
$logoDir = $establishment['img_dir'] ?? '';
$logoUrl = (!empty($logoImg) && !empty($logoDir))
    ? "{$baseUrl}/files/establishments/{$logoDir}/{$logoImg}"
    : $this->Url->image('no-image.jpg');

// Facade image (facade_img / facade_dir) - large banner. Fallback to logo.
$facadeImg = $establishment['facade_img'] ?? '';
$facadeDir = $establishment['facade_dir'] ?? '';
$facadeUrl = (!empty($facadeImg) && !empty($facadeDir))
    ? "{$baseUrl}/files/establishments/{$facadeDir}/{$facadeImg}"
    : $logoUrl;

$title = $establishment['title'] ?? 'Establishment';
$category = $establishment['category'] ?? '';
$mainCategory = $establishment['estmaincategory'] ?? '';
$building = $establishment['building'] ?? 'Araneta City';
$level = $establishment['level'] ?? '';
$fallbackImg = $this->Url->image('no-image.jpg');

// Build a clean location string
$locationParts = array_filter([$level, $building]);
$locationText = implode(', ', $locationParts);

// Get Directions link (Google Maps search for building at Araneta City)
$directionsQuery = urlencode(trim(($building ?: 'Araneta City') . ', Araneta City, Cubao, Quezon City'));
$directionsUrl = "https://www.google.com/maps/search/?api=1&query={$directionsQuery}";
?>

<style>
    .estd-page {
        background: #fff;
        padding-bottom: 60px;
    }

    .estd-wrap {
        width: 92%;
        max-width: 1100px;
        margin: 0 auto;
    }

    /* Split hero: info left, facade image right */
    .estd-hero {
        display: flex;
        gap: 48px;
        align-items: center;
        margin-top: 40px;
        margin-bottom: 20px;
    }

    /* Left: logo, title, tags, schedule, directions, socials */
    .estd-hero-info {
        flex: 1 1 52%;
        min-width: 0;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: flex-start;
        text-align: left;
    }

    /* Right: facade image (slightly landscape) */
    .estd-hero-media {
        flex: 1 1 48%;
        max-width: 48%;
        border-radius: 18px;
        overflow: hidden;
        background: #f0f0f0;
        aspect-ratio: 4 / 3;
    }

    .estd-hero-media img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    .estd-logo {
        width: 84px;
        height: 84px;
        border-radius: 50%;
        overflow: hidden;
        background: #fff;
        border: 1px solid #eee;
        box-shadow: 0 4px 14px rgba(0, 0, 0, 0.1);
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 18px;
    }

    .estd-logo img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .estd-title {
        font-size: 38px;
        font-weight: 800;
        color: #1a1a1a;
        margin: 0 0 18px;
        line-height: 1.1;
        letter-spacing: -0.5px;
    }

    @media (max-width: 768px) {
        .estd-hero {
            flex-direction: column-reverse;
            gap: 24px;
            align-items: stretch;
        }

        .estd-hero-media {
            flex: 1 1 auto;
            max-width: none;
            width: 100%;
        }

        .estd-hero-info {
            flex: 1 1 auto;
        }
    }

    /* Tag pills */
    .estd-tags {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        margin-bottom: 22px;
        flex-direction: column;
        align-items: flex-start;
    }

    .estd-tag {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        font-size: 15px;
        font-weight: 600;
        padding: 9px 20px;
        border-radius: 30px;
        line-height: 1;
    }

    .estd-tag--loc {
        background: transparent;
        color: #00afab;
        border: 1.5px solid #00afab;
        font-size: 16px;
        font-weight: 700;
        padding: 10px 16px;
        margin-bottom: 16px !important;
        order: -1;
        border-radius: 18px;
    }

    .estd-tag--loc i {
        font-size: 16px;
        margin-right: 6px;
    }

    .estd-tag--solid {
        background: #00afab;
        color: #fff;
        font-size: 13px;
        padding: 8px 18px;
    }

    .estd-tag--outline {
        background: #fff;
        color: #1a1a1a;
        border: 1.5px solid #e0e0e0;
        font-size: 13px;
        padding: 8px 18px;
    }

    /* Schedule inside the hero info column */
    .estd-section--sched {
        margin-bottom: 24px;
    }

    .estd-sched-row {
        margin-bottom: 4px;
    }

    .estd-sched-day {
        font-size: 18px;
        font-weight: 700;
        color: #1a1a1a;
        margin: 0 0 2px;
    }

    .estd-sched-time {
        font-size: 16px;
        color: #999;
        margin: 0;
    }

    /* Per-day list (different hours) */
    .estd-sched-list {
        list-style: none;
        margin: 0;
        padding: 0;
        max-width: 100%;
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 12px 20px;
    }

    .estd-sched-line {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        padding: 0;
        border-bottom: none;
    }

    .estd-sched-line:last-child {
        border-bottom: none;
        padding-bottom: 0;
    }

    .estd-sched-line-day {
        font-size: 14px;
        font-weight: 700;
        color: #1a1a1a;
        min-width: 75px;
    }

    .estd-sched-line-time {
        font-size: 13px;
        color: #999;
        text-align: right;
    }

    /* Actions row: Get Directions + socials */
    .estd-actions {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 14px;
        margin-top: 4px;
    }

    .estd-directions-btn {
        display: inline-flex;
        align-items: center;
        gap: 9px;
        background: linear-gradient(135deg, #8e2de2 0%, #a12d8e 100%);
        color: #fff;
        font-size: 15px;
        font-weight: 700;
        padding: 13px 26px;
        border-radius: 30px;
        text-decoration: none;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .estd-directions-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 18px rgba(142, 45, 226, 0.35);
        color: #fff;
    }

    .estd-social {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 44px;
        height: 44px;
        border-radius: 50%;
        color: #fff;
        text-decoration: none;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .estd-social:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 18px rgba(0, 0, 0, 0.18);
        color: #fff;
    }

    .estd-social i {
        font-size: 19px;
    }

    .estd-social--fb {
        background: #1877f2;
    }

    .estd-social--ig {
        background: linear-gradient(45deg, #f09433, #e6683c, #dc2743, #cc2366, #bc1888);
    }

    .estd-social--web {
        background: #00afab;
    }

    /* Related stores ("You Might Also Love") */
    .estd-nearby {
        padding-top: 44px;
        margin-top: 24px;
    }

    .estd-nearby-eyebrow {
        display: block;
        font-size: 13px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: #999;
        margin: 0 0 18px;
        text-align: left;
    }

    .estd-nearby-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 24px;
    }

    .estd-nearby-card {
        display: block;
        text-decoration: none;
        border-radius: 14px;
        overflow: hidden;
        border: 1px solid #eee;
        background: #fff;
        transition: transform 0.25s ease, box-shadow 0.25s ease;
    }

    .estd-nearby-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 26px rgba(0, 0, 0, 0.12);
    }

    .estd-nearby-media {
        width: 100%;
        aspect-ratio: 16 / 11;
        overflow: hidden;
        background: #f5f5f5;
    }

    .estd-nearby-media img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.4s ease;
    }

    .estd-nearby-card:hover .estd-nearby-media img {
        transform: scale(1.05);
    }

    .estd-nearby-info {
        padding: 16px 18px 20px;
        text-align: left;
    }

    .estd-nearby-loc {
        font-size: 13px;
        font-weight: 700;
        color: #00afab;
        margin: 0 0 6px;
    }

    .estd-nearby-name {
        font-size: 17px;
        font-weight: 700;
        color: #1a1a1a;
        margin: 0;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    /* Responsive */
    @media (max-width: 991px) {
        .estd-nearby-grid {
            grid-template-columns: repeat(3, 1fr);
            gap: 18px;
        }
    }

    @media (max-width: 768px) {
        .estd-title {
            font-size: 30px;
        }

        .estd-nearby-grid {
            grid-template-columns: 1fr;
        }

        .estd-nearby-media {
            aspect-ratio: 16 / 9;
        }
    }

    @media (max-width: 480px) {
        .estd-title {
            font-size: 24px;
        }

        .estd-directions-btn {
            font-size: 14px;
            padding: 12px 22px;
        }

        .estd-sched-list {
            grid-template-columns: 1fr !important;
            gap: 8px 16px !important;
        }

        .estd-sched-line-day {
            font-size: 13px;
        }

        .estd-sched-line-time {
            font-size: 12px;
        }

        .estd-actions {
            flex-wrap: wrap;
            gap: 10px;
        }

        .estd-directions-btn {
            flex: 0 0 auto;
            min-width: auto;
            width: auto;
        }

        .estd-social {
            width: 40px;
            height: 40px;
            font-size: 16px;
        }

        .estd-social i {
            font-size: 16px;
        }

        .estd-nearby-grid {
            grid-template-columns: 1fr !important;
        }

        .estd-nearby-media {
            aspect-ratio: 16 / 9;
        }
    }
</style>

<div id="wrapper">
    <div class="content">
        <div class="estd-page">
            <div class="estd-wrap">
                <!-- Split hero: image left, info right -->
                <div class="estd-hero">
                    <!-- Left: facade image (slightly landscape) -->
                    <div class="estd-hero-media">
                        <img src="<?= $facadeUrl ?>" alt="<?= h($title) ?>"
                            onerror="this.onerror=null;this.src='<?= $fallbackImg ?>';">
                    </div>

                    <!-- Right: logo, title, tags, schedule, directions, socials -->
                    <div class="estd-hero-info">
                        <div class="estd-logo">
                            <img src="<?= $logoUrl ?>" alt="<?= h($title) ?> logo"
                                onerror="this.onerror=null;this.src='<?= $fallbackImg ?>';">
                        </div>

                        <h1 class="estd-title"><?= h($title) ?></h1>

                        <div class="estd-tags">
                            <?php if (!empty($locationText)): ?>
                                <span class="estd-tag estd-tag--loc"><i class="fas fa-map-marker-alt"></i> <?= h($locationText) ?></span>
                            <?php endif; ?>
                            
                            <div style="display: flex; flex-wrap: wrap; gap: 12px;">
                                <?php if (!empty($category)): ?>
                                    <span class="estd-tag estd-tag--solid"><?= h($category) ?></span>
                                <?php endif; ?>
                                <?php if (!empty($mainCategory)): ?>
                                    <span class="estd-tag estd-tag--outline"><?= h($mainCategory) ?></span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Store Schedule -->
                        <?php if (!empty($establishment['schedules']) && is_array($establishment['schedules'])): ?>
                            <?php
                            $schedules = $establishment['schedules'];

                            // Helper: format a schedule row's time into a readable string
                            $fmtTime = function ($s) {
                                if (!empty($s['open_at']) && !empty($s['close_at'])) {
                                    return $s['open_at'] . ' - ' . $s['close_at'];
                                }
                                return 'Closed';
                            };

                            // Detect if every day shares the exact same time
                            $allSame = true;
                            $firstTime = $fmtTime($schedules[0]);
                            foreach ($schedules as $s) {
                                if ($fmtTime($s) !== $firstTime) {
                                    $allSame = false;
                                    break;
                                }
                            }
                            $firstDay = $schedules[0]['day'] ?? '';
                            $lastDay = $schedules[count($schedules) - 1]['day'] ?? '';
                            ?>
                            <div class="estd-section--sched">
                                <?php if ($allSame && count($schedules) > 1): ?>
                                    <div class="estd-sched-row">
                                        <p class="estd-sched-day"><?= h($firstDay) ?> &ndash; <?= h($lastDay) ?></p>
                                        <p class="estd-sched-time"><?= h($firstTime) ?></p>
                                    </div>
                                <?php else: ?>
                                    <ul class="estd-sched-list">
                                        <?php foreach ($schedules as $schedule): ?>
                                            <li class="estd-sched-line">
                                                <span class="estd-sched-line-day"><?= h($schedule['day'] ?? '') ?></span>
                                                <span class="estd-sched-line-time"><?= h($fmtTime($schedule)) ?></span>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>

                        <!-- Actions: Get Directions + socials -->
                        <div class="estd-actions">
                            <a href="<?= $directionsUrl ?>" target="_blank" rel="noopener" class="estd-directions-btn">
                                <i class="fas fa-location-arrow"></i> Get Directions
                            </a>
                            <?php if (!empty($establishment['facebook'])): ?>
                                <a href="<?= h($establishment['facebook']) ?>" target="_blank" rel="noopener"
                                    class="estd-social estd-social--fb" aria-label="Facebook">
                                    <i class="fab fa-facebook-f"></i>
                                </a>
                            <?php endif; ?>
                            <?php if (!empty($establishment['instagram'])): ?>
                                <a href="<?= h($establishment['instagram']) ?>" target="_blank" rel="noopener"
                                    class="estd-social estd-social--ig" aria-label="Instagram">
                                    <i class="fab fa-instagram"></i>
                                </a>
                            <?php endif; ?>
                            <?php if (!empty($establishment['website'])): ?>
                                <a href="<?= h($establishment['website']) ?>" target="_blank" rel="noopener"
                                    class="estd-social estd-social--web" aria-label="Website">
                                    <i class="fas fa-globe"></i>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- More to Discover: related stores in same building & category -->
                <?php if (!empty($nearbyStores)): ?>
                    <div class="estd-nearby">
                        <span class="estd-nearby-eyebrow">More to Discover</span>
                        <div class="estd-nearby-grid">
                            <?php foreach ($nearbyStores as $store): ?>
                                <?php
                                $sImg = $store['image'] ?? '';
                                $sDir = $store['img_dir'] ?? '';
                                $sFacadeImg = $store['facade_img'] ?? '';
                                $sFacadeDir = $store['facade_dir'] ?? '';
                                $sLogoUrl = (!empty($sImg) && !empty($sDir))
                                    ? "{$baseUrl}/files/establishments/{$sDir}/{$sImg}"
                                    : $fallbackImg;
                                $sMediaUrl = (!empty($sFacadeImg) && !empty($sFacadeDir))
                                    ? "{$baseUrl}/files/establishments/{$sFacadeDir}/{$sFacadeImg}"
                                    : $sLogoUrl;
                                $sBuilding = $store['building'] ?? 'Araneta City';
                                $sLevel = $store['level'] ?? '';
                                $sLoc = implode(', ', array_filter([$sLevel, $sBuilding]));
                                $storeUrl = $this->Url->build([
                                    'controller' => 'Mainpages',
                                    'action' => 'establishmentDetail',
                                    'id' => $store['id']
                                ]);
                                ?>
                                <a href="<?= $storeUrl ?>" class="estd-nearby-card">
                                    <div class="estd-nearby-media">
                                        <img src="<?= $sMediaUrl ?>" alt="<?= h($store['title']) ?>"
                                            onerror="this.onerror=null;this.src='<?= $fallbackImg ?>';">
                                    </div>
                                    <div class="estd-nearby-info">
                                        <?php if (!empty($sLoc)): ?>
                                            <p class="estd-nearby-loc"><?= h($sLoc) ?></p>
                                        <?php endif; ?>
                                        <p class="estd-nearby-name"><?= h($store['title']) ?></p>
                                    </div>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="limit-box fl-wrap"></div>
    </div>
    <!--content end-->
</div>

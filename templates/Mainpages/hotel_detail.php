<?php
/**
 * Hotel Detail Page
 * Modern, clean two-column detail layout.
 */

$baseUrl = env('API_BASE_URL', 'https://aci-cms.aranetacity.com');

$hotelName = $hotel['hotelname'] ?? 'Hotel';
$location = $hotel['location'] ?? '';
$image = $hotel['img'] ?? '';
$imageDir = $hotel['img_dir'] ?? '';
$imageUrl = (!empty($image) && !empty($imageDir))
    ? "{$baseUrl}/files/hotels/{$imageDir}/{$image}"
    : $this->Url->image('no-image.jpg');
$fallbackImg = $this->Url->image('no-image.jpg');

// Description HTML
$descHtml = !empty($hotel['hotel_fulltext'])
    ? $hotel['hotel_fulltext']
    : (!empty($hotel['hotel_introtext']) ? '<p>' . h($hotel['hotel_introtext']) . '</p>' : '');

// Amenities (static defaults — the API doesn't provide these yet)
$amenities = ['Free Wi-Fi', '24-Hour Front Desk', 'All-Day Dining', 'Air Conditioning', 'Family Rooms'];

$createdStr = !empty($hotel['created']) ? date('M Y', strtotime($hotel['created'])) : '';
$updatedStr = !empty($hotel['modified']) ? date('M d, Y', strtotime($hotel['modified'])) : '';

// Booking URL per hotel. Matched against the hotel name so it works regardless
// of slug/id. Unknown hotels get no button (rather than a dead "#" link).
$bookingUrls = [
    'novotel' => 'https://www.novotelmanilaaranetacity.com/',
    'ibis' => 'https://www.ibisstyles-manilaaranetacity.com/',
];
$bookingUrl = '';
$hotelNameLower = strtolower($hotelName);
foreach ($bookingUrls as $keyword => $url) {
    if (str_contains($hotelNameLower, $keyword)) {
        $bookingUrl = $url;
        break;
    }
}

// Gallery images. Each API item usually has a ready-to-use relative "url"
// (e.g. /files/hotels/{dir}/{file}); prefix it with the CMS base. Fall back to
// building the URL from image/image_dir when "url" is absent.
$galleryImages = [];
if (!empty($hotel['gallery']) && is_array($hotel['gallery'])) {
    foreach ($hotel['gallery'] as $g) {
        $gUrl = '';
        if (!empty($g['url'])) {
            $gUrl = str_starts_with($g['url'], 'http')
                ? $g['url']
                : $baseUrl . '/' . ltrim($g['url'], '/');
        } elseif (!empty($g['image']) && !empty($g['image_dir'])) {
            $gUrl = "{$baseUrl}/files/hotels/{$g['image_dir']}/" . rawurlencode($g['image']);
        }
        if ($gUrl !== '') {
            $galleryImages[] = $gUrl;
        }
    }
}
?>

<style>
    .hd-page {
        background: #fff;
        padding: 34px 0 70px;
        text-align: left;
    }

    .hd-page * {
        text-align: left;
    }

    .hd-wrap {
        width: 92%;
        max-width: 1200px;
        margin: 0 auto;
    }

    /* Breadcrumb */
    .hd-crumb {
        font-size: 13px;
        color: #999;
        margin-bottom: 18px;
    }

    .hd-crumb a {
        color: #999;
        text-decoration: none;
    }

    .hd-crumb a:hover {
        color: #00afab;
    }

    .hd-crumb .current {
        color: #6b2d7b;
        font-weight: 600;
    }

    /* Header: logo + title */
    .hd-head {
        display: flex;
        align-items: center;
        gap: 18px;
        margin-bottom: 30px;
    }

    .hd-logo {
        width: 60px;
        height: 60px;
        border-radius: 12px;
        overflow: hidden;
        flex-shrink: 0;
        background: #fff;
        border: 1px solid #eee;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 6px;
    }

    .hd-logo img {
        max-width: 100%;
        max-height: 100%;
        object-fit: contain;
    }

    .hd-title {
        font-size: 38px;
        font-weight: 800;
        color: #1a1a1a;
        margin: 0;
        letter-spacing: -0.5px;
        line-height: 1.1;
        text-transform: none;
    }

    /* Two-column layout */
    .hd-layout {
        display: grid;
        grid-template-columns: 1fr 320px;
        gap: 26px;
        align-items: start;
    }

    /* Card */
    .hd-card {
        border: 1px solid #eee;
        border-radius: 14px;
        padding: 24px 26px;
        margin-bottom: 22px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.03);
    }

    .hd-card-title {
        font-size: 20px;
        font-weight: 800;
        color: #1a1a1a;
        margin: 0 0 16px;
        padding-bottom: 14px;
        border-bottom: 1px solid #f0f0f0;
        letter-spacing: -0.2px;
    }

    /* Description content */
    .hd-desc {
        font-size: 15px;
        line-height: 1.7;
        color: #555;
    }

    .hd-desc ul {
        margin: 0 0 16px;
        padding-left: 20px;
    }

    .hd-desc li {
        margin-bottom: 6px;
        color: #1a1a1a;
    }

    .hd-desc p {
        margin: 0 0 14px;
    }

    .hd-book {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: #6b2d7b;
        color: #fff;
        font-size: 14px;
        font-weight: 700;
        padding: 12px 24px;
        border-radius: 30px;
        text-decoration: none;
        margin-top: 6px;
        transition: background 0.25s ease;
    }

    .hd-book:hover {
        background: #582465;
        color: #fff;
    }

    /* Amenity pills */
    .hd-amenities {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }

    .hd-amenity {
        font-size: 13px;
        font-weight: 600;
        color: #444;
        border: 1px solid #e2e2e2;
        border-radius: 20px;
        padding: 8px 16px;
    }

    /* Gallery grid */
    .hd-gallery {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 16px;
    }

    .hd-gallery-item {
        aspect-ratio: 4 / 3;
        border-radius: 10px;
        overflow: hidden;
        background: linear-gradient(135deg, #eef6f5 0%, #f3eef6 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #9a9a9a;
        font-size: 13px;
    }

    .hd-gallery-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    /* Sidebar */
    .hd-side-text {
        font-size: 14px;
        line-height: 1.6;
        color: #888;
        margin: 0;
    }

    .hd-side-row {
        display: flex;
        justify-content: space-between;
        gap: 12px;
        padding: 8px 0;
        font-size: 14px;
    }

    .hd-side-row .lbl {
        font-weight: 700;
        color: #1a1a1a;
    }

    .hd-side-row .val {
        color: #b06a2c;
    }

    .hd-address .lbl {
        font-weight: 700;
        color: #1a1a1a;
        margin-right: 6px;
    }

    .hd-address .val {
        color: #666;
    }

    .hd-socials {
        display: flex;
        gap: 10px;
        margin-top: 16px;
    }

    .hd-social {
        width: 34px;
        height: 34px;
        border-radius: 50%;
        background: #00afab;
        color: #fff;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        text-decoration: none;
        transition: transform 0.2s ease, background 0.2s ease;
    }

    .hd-social:hover {
        transform: translateY(-2px);
        background: #008b88;
        color: #fff;
    }

    /* Nearby */
    .hd-nearby-item {
        display: flex;
        align-items: center;
        gap: 12px;
        text-decoration: none;
    }

    .hd-nearby-img {
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

    .hd-nearby-img img {
        max-width: 100%;
        max-height: 100%;
        object-fit: contain;
    }

    .hd-nearby-name {
        font-size: 15px;
        font-weight: 700;
        color: #1a1a1a;
        margin: 0 0 2px;
    }

    .hd-nearby-loc {
        font-size: 13px;
        color: #6b2d7b;
        margin: 0;
    }

    /* Responsive */
    @media (max-width: 900px) {
        .hd-layout {
            grid-template-columns: 1fr;
        }

        .hd-title {
            font-size: 30px;
        }
    }

    @media (max-width: 560px) {
        .hd-gallery {
            grid-template-columns: repeat(2, 1fr);
        }

        .hd-title {
            font-size: 26px;
        }
    }
</style>

<div id="wrapper">
    <div class="content">
        <div class="hd-page">
            <div class="hd-wrap">


                <!-- Header -->
                <div class="hd-head">
                    <div class="hd-logo">
                        <img src="<?= $imageUrl ?>" alt="<?= h($hotelName) ?>"
                            onerror="this.onerror=null;this.src='<?= $fallbackImg ?>';">
                    </div>
                    <h1 class="hd-title"><?= h($hotelName) ?></h1>
                </div>

                <!-- Two-column -->
                <div class="hd-layout">
                    <!-- Left column -->
                    <div class="hd-main">
                        <!-- Description -->
                        <div class="hd-card">
                            <h2 class="hd-card-title">Description</h2>
                            <div class="hd-desc">
                                <?php if (!empty($descHtml)): ?>
                                    <?= $descHtml ?>
                                <?php else: ?>
                                    <p>No detailed information available for this hotel.</p>
                                <?php endif; ?>
                            </div>
                            <?php if (!empty($bookingUrl)): ?>
                                <a href="<?= h($bookingUrl) ?>" class="hd-book" target="_blank" rel="noopener">Book Now &rarr;</a>
                            <?php endif; ?>
                        </div>

                        <!-- Amenities -->
                        <div class="hd-card">
                            <h2 class="hd-card-title">Hotel Amenities</h2>
                            <div class="hd-amenities">
                                <?php foreach ($amenities as $am): ?>
                                    <span class="hd-amenity"><?= h($am) ?></span>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <!-- Gallery -->
                        <?php if (!empty($galleryImages)): ?>
                            <div class="hd-card">
                                <h2 class="hd-card-title">Gallery</h2>
                                <div class="hd-gallery">
                                    <?php foreach ($galleryImages as $gImg): ?>
                                        <a class="hd-gallery-item" href="<?= h($gImg) ?>" target="_blank" rel="noopener">
                                            <img src="<?= h($gImg) ?>" alt="<?= h($hotelName) ?> photo" loading="lazy"
                                                onerror="this.onerror=null;this.closest('.hd-gallery-item').style.display='none';">
                                        </a>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>

                    </div>

                    <!-- Right sidebar -->
                    <div class="hd-side">
                        <!-- Hotel Information -->
                        <div class="hd-card">
                            <h2 class="hd-card-title">Hotel Information</h2>
                            <p class="hd-side-text">For check-in, check-out, and room service hours, please contact the
                                hotel directly.</p>
                        </div>

                        <!-- Location / Contacts -->
                        <div class="hd-card">
                            <h2 class="hd-card-title">Location / Contacts</h2>
                            <?php if (!empty($location)): ?>
                                <p class="hd-address"><span class="lbl">Address</span><span
                                        class="val"><?= h($location) ?></span></p>
                            <?php endif; ?>
                            
                        </div>



                        <!-- Nearby -->
                        <?php if (!empty($relatedHotels)): ?>
                            <div class="hd-card">
                                <h2 class="hd-card-title">Nearby</h2>
                                <?php foreach (array_slice($relatedHotels, 0, 3) as $rel): ?>
                                    <?php
                                    $relImg = $rel['img'] ?? '';
                                    $relDir = $rel['img_dir'] ?? '';
                                    $relName = $rel['hotelname'] ?? 'Hotel';
                                    $relId = $rel['id'] ?? '';
                                    $relLoc = $rel['location'] ?? 'Araneta City';
                                    $relUrl = (!empty($relImg) && !empty($relDir))
                                        ? "{$baseUrl}/files/hotels/{$relDir}/{$relImg}"
                                        : $fallbackImg;
                                    ?>
                                    <a href="/hotel/<?= h($relId) ?>" class="hd-nearby-item">
                                        <div class="hd-nearby-img">
                                            <img src="<?= $relUrl ?>" alt="<?= h($relName) ?>"
                                                onerror="this.onerror=null;this.src='<?= $fallbackImg ?>';">
                                        </div>
                                        <div>
                                            <p class="hd-nearby-name"><?= h($relName) ?></p>
                                            <p class="hd-nearby-loc"><?= h($relLoc) ?></p>
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
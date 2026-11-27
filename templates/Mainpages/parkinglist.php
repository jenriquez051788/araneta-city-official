<?php
/**
 * Parking Guide Page
 * Modern, clean layout: location cards, map + live availability, and tips.
 */

$baseUrl = env('API_BASE_URL', 'https://aci-cms.aranetacity.com');
$parkingsList = !empty($parkings) && is_array($parkings) ? $parkings : [];
$fallbackImg = $this->Url->image('no-image.jpg');
?>

<style>
    .pk-page {
        background: #fff;
        padding: 46px 0 70px;
        text-align: left;
    }

    .pk-page * {
        text-align: left;
    }

    .pk-wrap {
        width: 92%;
        max-width: 1200px;
        margin: 0 auto;
    }

    /* Header */
    .pk-eyebrow {
        display: inline-block;
        color: #6b2d7b;
        font-size: 13px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: 8px;
    }

    .pk-title {
        font-size: 44px;
        font-weight: 800;
        color: #1a1a1a;
        margin: 0 0 10px;
        letter-spacing: -0.5px;
        line-height: 1.05;
    }

    .pk-subtitle {
        font-size: 16px;
        color: #777;
        margin: 0 0 40px;
    }

    /* Locations grid */
    .pk-section-label {
        font-size: 13px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: #999;
        margin: 0 0 18px;
    }

    .pk-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 22px;
        margin-bottom: 50px;
    }

    .pk-card {
        position: relative;
        aspect-ratio: 4 / 3;
        border-radius: 12px;
        overflow: hidden;
        background: linear-gradient(160deg, #8a8a90 0%, #2b2b30 100%);
        box-shadow: 0 4px 14px rgba(0, 0, 0, 0.1);
        transition: transform 0.25s ease, box-shadow 0.25s ease;
    }

    .pk-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 24px rgba(0, 0, 0, 0.16);
    }

    .pk-card img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s ease;
    }

    .pk-card:hover img {
        transform: scale(1.05);
    }

    .pk-card-title {
        position: absolute;
        left: 0;
        right: 0;
        bottom: 0;
        padding: 40px 16px 16px;
        background: linear-gradient(to top, rgba(0, 0, 0, 0.85), transparent);
        color: #fff;
        font-size: 15px;
        font-weight: 700;
    }

    /* Two-column: map + availability */
    .pk-split {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 26px;
        align-items: start;
        margin-bottom: 30px;
    }

    .pk-panel {
        border: 1px solid #eee;
        border-radius: 14px;
        padding: 24px 26px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.03);
    }

    .pk-panel-title {
        font-size: 18px;
        font-weight: 800;
        color: #1a1a1a;
        margin: 0 0 16px;
    }

    .pk-panel-title .count {
        color: #00afab;
        font-weight: 700;
        font-size: 15px;
    }

    .pk-map-text {
        font-size: 14px;
        line-height: 1.6;
        color: #777;
        margin: 0 0 18px;
    }

    .pk-map-img {
        width: 100%;
        height: auto;
        border-radius: 10px;
        display: block;
        background: linear-gradient(135deg, #eef6f5 0%, #f3eef6 100%);
    }

    /* Availability list */
    .pk-avail {
        list-style: none;
        margin: 0;
        padding: 0;
    }

    .pk-avail li {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
        padding: 14px 0;
        border-bottom: 1px solid #f0f0f0;
    }

    .pk-avail li:last-child {
        border-bottom: none;
    }

    .pk-avail-name {
        font-size: 15px;
        font-weight: 700;
        color: #1a1a1a;
    }

    .pk-badge {
        flex: none;
        color: #fff;
        padding: 5px 15px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 700;
        white-space: nowrap;
    }

    .pk-badge.available {
        background: #00afab;
    }

    .pk-badge.full {
        background: #c8102e;
    }

    .pk-note {
        font-size: 13px;
        line-height: 1.6;
        color: #999;
        margin: 18px 0 0;
    }

    /* Tips */
    .pk-tips {
        border: 1px solid #eee;
        border-radius: 14px;
        padding: 26px 28px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.03);
    }

    .pk-tips-title {
        font-size: 18px;
        font-weight: 800;
        color: #1a1a1a;
        margin: 0 0 14px;
    }

    .pk-tips p {
        font-size: 14px;
        line-height: 1.7;
        color: #777;
        margin: 0 0 14px;
    }

    .pk-btn {
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

    .pk-btn:hover {
        background: #582465;
        color: #fff;
    }

    /* Responsive */
    @media (max-width: 991px) {
        .pk-grid {
            grid-template-columns: repeat(3, 1fr);
        }

        .pk-title {
            font-size: 36px;
        }
    }

    @media (max-width: 768px) {
        .pk-split {
            grid-template-columns: 1fr;
        }

        .pk-grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 16px;
        }

        .pk-title {
            font-size: 30px;
        }
    }

    @media (max-width: 420px) {
        .pk-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div id="wrapper">
    <div class="content">
        <div class="pk-page">
            <div class="pk-wrap">
                <!-- Header -->
                <span class="pk-eyebrow">Getting Around</span>
                <h1 class="pk-title">Parking Guide</h1>
                <p class="pk-subtitle">Find a bay, pick your entrance, and get in quickly.</p>

                <!-- Location cards -->
                <?php if (!empty($parkingsList)): ?>
                    <p class="pk-section-label">Parking Locations &middot; <?= count($parkingsList) ?></p>
                    <div class="pk-grid">
                        <?php foreach ($parkingsList as $parking): ?>
                            <?php
                            $parkingName = $parking['parking_name'] ?? '';
                            $imageUrl = $parking['image_url'] ?? '';
                            $fullImageUrl = !empty($imageUrl) ? $baseUrl . $imageUrl : '';
                            ?>
                            <div class="pk-card">
                                <?php if (!empty($fullImageUrl)): ?>
                                    <img src="<?= $fullImageUrl ?>" alt="<?= h($parkingName) ?>"
                                        onerror="this.style.display='none';">
                                <?php endif; ?>
                                <div class="pk-card-title"><?= h($parkingName) ?></div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <!-- Map + Availability -->
                <div class="pk-split">
                    <!-- Map -->
                    <div class="pk-panel">
                        <h2 class="pk-panel-title">Parking Map</h2>
                        <p class="pk-map-text">Araneta City is bounded by EDSA, Aurora Blvd., and P. Tuazon Blvd.
                            Entrances are numbered P1 to P19 &mdash; quote the number when someone asks where you
                            parked.</p>
                        <img class="pk-map-img" src="/img/gallery/parking-guide/parking-map.jpg"
                            alt="Araneta City parking map showing entrances P1 to P19"
                            onerror="this.style.display='none';">
                    </div>

                    <!-- Availability -->
                    <div class="pk-panel">
                        <h2 class="pk-panel-title">Parking Availability
                            <span class="count"><?= count($parkingsList) ?> Locations</span></h2>
                        <ul class="pk-avail">
                            <?php foreach ($parkingsList as $parking): ?>
                                <?php
                                $parkingName = $parking['parking_name'] ?? '';
                                $availableSlots = $parking['available_slots'] ?? '';
                                $isFull = ($availableSlots === '' || (int) $availableSlots === 0);
                                ?>
                                <li>
                                    <span class="pk-avail-name"><?= h($parkingName) ?></span>
                                    <span class="pk-badge <?= $isFull ? 'full' : 'available' ?>">
                                        <?= $isFull ? 'Full' : h($availableSlots) . ' available' ?>
                                    </span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                        <p class="pk-note">Slot counts are a snapshot and change through the day. Weekends, paydays, and
                            event nights at the Coliseum fill up fastest.</p>
                    </div>
                </div>

                <!-- Tips -->
                <div class="pk-tips">
                    <h2 class="pk-tips-title">Before You Drive In</h2>
                    <p>Mall hours run Sunday to Thursday, 10am to 9pm, and Friday to Saturday, 10am to 10pm. Parking
                        Garage South and Gateway Mall 2 carry the most bays, so head there first on busy days. The malls
                        are interconnected &mdash; park once and walk between Gateway Mall, Gateway Mall 2, Ali Mall, and
                        Farmers Plaza rather than moving the car.</p>
                    <p>Coming by train or bus instead? The estate connects to MRT-3 Araneta Center&ndash;Cubao, LRT-2
                        Araneta Center&ndash;Cubao, and the Araneta City Bus Port.</p>
                    <a href="/contact" class="pk-btn">Contact Us &rarr;</a>
                </div>
            </div>
        </div>

        <div class="limit-box fl-wrap"></div>
    </div>
</div>

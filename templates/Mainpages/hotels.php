<?php
/**
 * Hotels Page
 * Displays all hotels across Araneta City
 */

$baseUrl = env('API_BASE_URL', 'https://aci-cms.aranetacity.com');
?>

<style>
    .listpage {
        background: #fff;
        padding: 46px 0 70px;
    }

    .listpage-wrap {
        width: 92%;
        max-width: 1200px;
        margin: 0 auto;
    }

    .listpage-head {
        margin-bottom: 34px;
    }

    .listpage-title {
        font-size: 44px;
        font-weight: 800;
        color: #1a1a1a;
        margin: 0 0 10px;
        letter-spacing: -0.5px;
        line-height: 1.05;
        text-align: left;
    }

    .listpage-subtitle {
        font-size: 16px;
        color: #777;
        margin: 0;
        max-width: 460px;
        line-height: 1.5;
        text-align: left;
    }

    .listcard-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 26px;
    }

    .listcard {
        display: flex;
        flex-direction: column;
        border: 1px solid #eee;
        border-radius: 16px;
        overflow: hidden;
        background: #fff;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.05);
        transition: transform 0.25s ease, box-shadow 0.25s ease;
    }

    .listcard:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 26px rgba(0, 0, 0, 0.1);
    }

    .listcard-media {
        width: 100%;
        aspect-ratio: 16 / 10;
        overflow: hidden;
        background: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 24px;
    }

    .listcard-media img {
        max-width: 100%;
        max-height: 100%;
        width: auto;
        height: auto;
        object-fit: contain;
        transition: transform 0.5s ease;
    }

    .listcard:hover .listcard-media img {
        transform: scale(1.04);
    }

    .listcard-body {
        padding: 22px 24px 26px;
        text-align: left;
    }

    .listcard-title {
        font-size: 22px;
        font-weight: 800;
        color: #1a1a1a;
        margin: 0 0 6px;
        letter-spacing: -0.3px;
    }

    .listcard-loc {
        font-size: 14px;
        color: #888;
        margin: 0 0 16px;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .listcard-loc i {
        color: #00afab;
        font-size: 13px;
    }

    .listcard-tags {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin-bottom: 20px;
    }

    .listcard-tag {
        font-size: 12px;
        font-weight: 600;
        color: #555;
        border: 1px solid #e2e2e2;
        border-radius: 20px;
        padding: 6px 14px;
    }

    .listcard-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: #1a1a1a;
        color: #fff;
        font-size: 14px;
        font-weight: 700;
        padding: 11px 22px;
        border-radius: 30px;
        text-decoration: none;
        transition: background 0.25s ease;
    }

    .listcard-btn:hover {
        background: #333;
        color: #fff;
    }

    .listpage-empty {
        text-align: left;
        padding: 60px 20px;
        color: #999;
        font-size: 16px;
    }

    @media (max-width: 768px) {
        .listcard-grid {
            grid-template-columns: 1fr;
        }

        .listpage-title {
            font-size: 32px;
        }

        .listcard-title {
            font-size: 20px;
        }
    }
</style>

<div id="wrapper">
    <div class="content">
        <div class="listpage">
            <div class="listpage-wrap">
                <!-- Header -->
                <div class="listpage-head">
                    <h1 class="listpage-title">Hotels &amp; Accommodations</h1>
                    <p class="listpage-subtitle">Two hotels, right in the district &mdash; steps from every mall,
                        restaurant, and show.</p>
                </div>

                <?php if (empty($hotels)): ?>
                    <div class="listpage-empty">
                        <p>No hotels found.</p>
                    </div>
                <?php else: ?>
                    <div class="listcard-grid">
                        <?php foreach ($hotels as $hotel): ?>
                            <?php
                            $image = $hotel['img'] ?? '';
                            $imageDir = $hotel['img_dir'] ?? '';
                            $hotelname = $hotel['hotelname'] ?? ($hotel['title'] ?? 'Hotel');
                            $id = $hotel['id'] ?? '';
                            $location = !empty($hotel['location']) ? $hotel['location'] : 'Araneta City, Cubao';

                            // Feature tags: use API-provided amenities if present, else none.
                            $tags = [];
                            if (!empty($hotel['amenities']) && is_array($hotel['amenities'])) {
                                $tags = $hotel['amenities'];
                            } elseif (!empty($hotel['features']) && is_array($hotel['features'])) {
                                $tags = $hotel['features'];
                            }

                            $imageUrl = (!empty($image) && !empty($imageDir))
                                ? "{$baseUrl}/files/hotels/{$imageDir}/{$image}"
                                : $this->Url->image('no-image.jpg');
                            ?>
                            <div class="listcard">
                                <a href="/hotel/<?= h($id) ?>" class="listcard-media">
                                    <img src="<?= $imageUrl ?>" alt="<?= h($hotelname) ?>"
                                        onerror="this.onerror=null;this.src='<?= $this->Url->image('no-image.jpg') ?>';">
                                </a>
                                <div class="listcard-body">
                                    <h3 class="listcard-title"><?= h($hotelname) ?></h3>
                                    <p class="listcard-loc"><i class="fas fa-map-marker-alt"></i> <?= h($location) ?></p>
                                    <?php if (!empty($tags)): ?>
                                        <div class="listcard-tags">
                                            <?php foreach (array_slice($tags, 0, 3) as $tag): ?>
                                                <span class="listcard-tag"><?= h(is_array($tag) ? ($tag['name'] ?? '') : $tag) ?></span>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>
                                    <a href="/hotel/<?= h($id) ?>" class="listcard-btn">View Hotel</a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="limit-box fl-wrap"></div>
    </div>
</div>

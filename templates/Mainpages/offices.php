<?php
/**
 * Offices Page
 * Displays all office buildings across Araneta City
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
        max-width: 480px;
        line-height: 1.5;
        text-align: left;
    }

    .listcard-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
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
        aspect-ratio: 4 / 5;
        overflow: hidden;
        background: #f7f7f7;
        display: block;
    }

    .listcard-media img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        object-position: center top;
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
        align-items: flex-start;
        gap: 6px;
        line-height: 1.4;
    }

    .listcard-loc i {
        color: #00afab;
        font-size: 13px;
        margin-top: 3px;
        flex-shrink: 0;
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

    @media (max-width: 991px) {
        .listcard-grid {
            grid-template-columns: repeat(2, 1fr);
        }
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
                    <h1 class="listpage-title">Office Buildings</h1>
                    <p class="listpage-subtitle">
                       Modern office spaces in the heart of Araneta City, connected to dining, transit, and everything in between.
                    </p>
                </div>

                <?php if (empty($offices)): ?>
                    <div class="listpage-empty">
                        <p>No office buildings found.</p>
                    </div>
                <?php else: ?>
                    <div class="listcard-grid">
                        <?php foreach ($offices as $office): ?>
                            <?php
                            $image = $office['img'] ?? '';
                            $imageDir = $office['img_dir'] ?? '';
                            $title = $office['title'] ?? 'Office';
                            $slug = $office['slug'] ?? '';
                            // Address comes from the API's "subtitle" field.
                            $location = !empty($office['subtitle'])
                                ? $office['subtitle']
                                : 'Araneta City, Cubao';

                            // Feature tags: use API-provided features if present
                            $tags = [];
                            if (!empty($office['amenities']) && is_array($office['amenities'])) {
                                $tags = $office['amenities'];
                            } elseif (!empty($office['features']) && is_array($office['features'])) {
                                $tags = $office['features'];
                            }

                            // Office images are served from the CMS under
                            // /files/offices/{img_dir}/{img} — same pattern as
                            // hotels/establishments/events. img_dir is the
                            // per-record folder (usually a UUID).
                            $imageUrl = $this->Url->image('no-image.jpg');
                            if (!empty($image) && !empty($imageDir)) {
                                $imageUrl = $baseUrl . '/files/offices/' . rawurlencode($imageDir) . '/' . rawurlencode($image);
                            }
                            ?>
                            <div class="listcard">
                                <a href="/office/<?= h($slug) ?>" class="listcard-media">
                                    <img 
                                        src="<?= $imageUrl ?>" 
                                        alt="<?= h($title) ?>"
                                        onerror="this.onerror=null;this.src='<?= $this->Url->image('no-image.jpg') ?>';">
                                </a>
                                <div class="listcard-body">
                                    <h3 class="listcard-title"><?= h($title) ?></h3>
                                    <p class="listcard-loc">
                                        <i class="fas fa-map-marker-alt"></i>
                                        <?= h($location) ?>
                                    </p>
                                    <?php if (!empty($tags)): ?>
                                        <div class="listcard-tags">
                                            <?php foreach (array_slice($tags, 0, 3) as $tag): ?>
                                                <span class="listcard-tag">
                                                    <?= h(is_array($tag) ? ($tag['name'] ?? '') : $tag) ?>
                                                </span>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>
                                    <a href="/office/<?= h($slug) ?>" class="listcard-btn">
                                        View Office
                                    </a>
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

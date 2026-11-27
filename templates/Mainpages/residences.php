<?php
/**
 * Residences Page
 * Lists residential projects across Araneta City in a card grid,
 * matching the Offices page layout.
 *
 * @var \App\View\AppView $this
 * @var iterable $residences Residence entities (with gallery + phases)
 */

$fallbackImg = $this->Url->image('no-image.jpg');

/**
 * Resolve a card image for a residence: prefer its image_url, then the first
 * gallery image, otherwise the generic no-image placeholder. Relative paths
 * are served from the app web root.
 */
$baseUrl = env('API_BASE_URL', 'https://aci-cms.aranetacity.com');

$resolveImage = function ($residence) use ($fallbackImg, $baseUrl) {
    $img = $residence['img'] ?? '';
    $dir = $residence['img_dir'] ?? '';
    if (!empty($img) && !empty($dir)) {
        // Same CMS pattern as offices/hotels: /files/residences/{dir}/{img}
        return $baseUrl . '/files/residences/' . rawurlencode($dir) . '/' . rawurlencode($img);
    }
    return $fallbackImg;
};
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
        max-width: 520px;
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
        line-height: 1.5;
    }

    .listcard-loc i {
        color: #00afab;
        font-size: 13px;
        margin-top: 3px;
        flex-shrink: 0;
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
                    <h1 class="listpage-title">Residences</h1>
                    <p class="listpage-subtitle">
                        Transit-oriented residential communities in the heart of Araneta City, steps away from shopping, dining, and mass transit.
                    </p>
                </div>

                <?php if (empty($residences) || count($residences) === 0): ?>
                    <div class="listpage-empty">
                        <p>No residences found.</p>
                    </div>
                <?php else: ?>
                    <div class="listcard-grid">
                        <?php foreach ($residences as $residence): ?>
                            <?php
                            $title = $residence['title'] ?? 'Residence';
                            // The API has no address field; the address is stored
                            // in "subtitle". Use it when it looks like an address
                            // (short + has a comma), otherwise fall back to a
                            // clean default (some subtitles are long paragraphs).
                            $subtitle = $residence['subtitle'] ?? '';
                            $location = !empty($residence['location'])
                                ? $residence['location']
                                : 'Araneta City, Quezon City';
                            if (empty($residence['location']) && !empty($subtitle)
                                && mb_strlen($subtitle) <= 120 && str_contains($subtitle, ',')) {
                                $location = $subtitle;
                            }
                            $imageUrl = $resolveImage($residence);
                            $slug = $residence['slug'] ?? '';
                            $detailUrl = $slug !== '' ? '/residence/' . rawurlencode($slug) : '';
                            ?>
                            <div class="listcard">
                                <?php if (!empty($detailUrl)): ?>
                                    <a href="<?= h($detailUrl) ?>" class="listcard-media">
                                        <img src="<?= h($imageUrl) ?>" alt="<?= h($title) ?>"
                                            onerror="this.onerror=null;this.src='<?= $fallbackImg ?>';">
                                    </a>
                                <?php else: ?>
                                    <span class="listcard-media">
                                        <img src="<?= h($imageUrl) ?>" alt="<?= h($title) ?>"
                                            onerror="this.onerror=null;this.src='<?= $fallbackImg ?>';">
                                    </span>
                                <?php endif; ?>
                                <div class="listcard-body">
                                    <h3 class="listcard-title"><?= h($title) ?></h3>
                                    <p class="listcard-loc">
                                        <i class="fas fa-map-marker-alt"></i>
                                        <?= h($location) ?>
                                    </p>
                                    <?php if (!empty($detailUrl)): ?>
                                        <a href="<?= h($detailUrl) ?>" class="listcard-btn">
                                            View Residence
                                        </a>
                                    <?php endif; ?>
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

<?php
/**
 * Malls Page
 * Displays the main Araneta City malls. Each card links to that mall's
 * shopping directory (/shopping?building={slug}).
 */

$baseUrl = env('API_BASE_URL', 'https://aci-cms.aranetacity.com');
$mallsList = !empty($malls) && is_array($malls) ? $malls : [];
?>

<style>
    .listpage {
        background: #fff;
        padding: 46px 0 70px;
        text-align: left;
    }

    .listpage * {
        text-align: left;
    }

    .listpage-wrap {
        width: 92%;
        max-width: 1200px;
        margin: 0 auto;
    }

    .listpage-head {
        margin-bottom: 34px;
    }

    .listpage-eyebrow {
        display: inline-block;
        color: #6b2d7b;
        font-size: 13px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: 8px;
    }

    .listpage-title {
        font-size: 44px;
        font-weight: 800;
        color: #1a1a1a;
        margin: 0 0 10px;
        letter-spacing: -0.5px;
        line-height: 1.05;
    }

    .listpage-subtitle {
        font-size: 16px;
        color: #777;
        margin: 0;
        max-width: 480px;
        line-height: 1.5;
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
        text-decoration: none;
        transition: transform 0.25s ease, box-shadow 0.25s ease;
        position: relative;
    }

    .listcard:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 26px rgba(0, 0, 0, 0.1);
    }

    .listcard-media {
        width: 100%;
        aspect-ratio: 16 / 9;
        overflow: hidden;
        background: linear-gradient(135deg, #6b2d7b 0%, #00afab 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        height: 200px;
        position: relative;
    }

    .listcard-media img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .listcard-media i {
        font-size: 48px;
        opacity: 0.85;
    }

    .listcard-body {
        padding: 0;
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        background: linear-gradient(to top, rgba(0,0,0,0.8), transparent);
        padding: 30px 24px 20px;
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .listcard-title {
        font-size: 22px;
        font-weight: 800;
        color: #fff;
        margin: 0;
        letter-spacing: -0.3px;
    }

    .listcard-loc {
        font-size: 14px;
        color: #888;
        margin: 0 0 18px;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .listcard-loc i {
        color: #00afab;
        font-size: 13px;
    }

    .listcard-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: #1a1a1a;
        color: #fff;
        font-size: 12px;
        font-weight: 700;
        padding: 8px 16px;
        border-radius: 30px;
        transition: background 0.25s ease;
        width: fit-content;
    }

    .listcard:hover .listcard-btn {
        background: #6b2d7b;
    }

    .listpage-empty {
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

        .listcard-media {
            height: 150px;
            object-fit: contain;
        }

        .listcard-media img {
            object-fit: contain;
            background: rgba(0, 0, 0, 0.3);
        }
    }
</style>

<div id="wrapper">
    <div class="content">
        <div class="listpage">
            <div class="listpage-wrap">
                <!-- Header -->
                <div class="listpage-head">
                    <span class="listpage-eyebrow">Destinations</span>
                    <h1 class="listpage-title">Our Malls</h1>
                    <p class="listpage-subtitle">Where shopping, dining, and entertainment meet — across four connected malls.</p>
                </div>

                <?php if (empty($mallsList)): ?>
                    <div class="listpage-empty">
                        <p>No malls found.</p>
                    </div>
                <?php else: ?>
                    <div class="listcard-grid">
                        <?php foreach ($mallsList as $mall): ?>
                            <?php
                            $mallName = $mall['building_name'] ?? 'Mall';
                            $mallSlug = $mall['slug'] ?? '';
                            $mallLink = !empty($mallSlug)
                                ? '/shopping?building=' . urlencode($mallSlug)
                                : '/shopping';
                            
                            // Map mall names to images
                            $mallImages = [
                                'Ali Mall' => '/img/mall/alimall.jpg',
                                'Gateway Mall' => '/img/mall/gm1.jpg',
                                'Farmers Plaza' => '/img/mall/farmers-plaza.jpg',
                                'Gateway Mall 2' => '/img/mall/gm2.jpg',
                            ];
                            
                            $mallImage = $mallImages[$mallName] ?? '/img/mall/gm1.jpg';
                            ?>
                            <a href="<?= h($mallLink) ?>" class="listcard">
                                <div class="listcard-media">
                                    <img src="<?= h($mallImage) ?>" alt="<?= h($mallName) ?>" loading="lazy">
                                </div>
                                <div class="listcard-body">
                                    <!-- <h3 class="listcard-title"><?= h($mallName) ?></h3> -->
                                    <span class="listcard-btn">Explore Stores &rarr;</span>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="limit-box fl-wrap"></div>
    </div>
</div>

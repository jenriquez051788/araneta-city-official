<?php
/**
 * Attraction Detail Page
 * Displays detailed information about a single attraction
 */

$baseUrl = env('API_BASE_URL', 'https://aci-cms.aranetacity.com');
?>

<style>
    .attraction-detail-hero {
        width: 100%;
        height: 600px;
        overflow: hidden;
        border-radius: 12px;
        margin-bottom: 40px;
    }

    .attraction-detail-hero img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        object-position: center;
    }

    .attraction-detail-container {
        max-width: 1000px;
        margin: 0 auto;
    }

    .attraction-detail-title {
        font-size: 38px;
        font-weight: 700;
        color: #333;
        margin-bottom: 25px;
        line-height: 1.2;
    }

    .attraction-detail-content {
        font-size: 16px;
        line-height: 1.8;
        color: #555;
        margin-bottom: 40px;
    }

    .attraction-detail-content p {
        margin-bottom: 20px;
    }

    .attraction-detail-content h4 {
        font-size: 18px;
        font-weight: 600;
        color: #333;
        margin-top: 25px;
        margin-bottom: 15px;
    }

    .back-button {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 30px;
        color: #667eea;
        text-decoration: none;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .back-button:hover {
        color: #5568d3;
        transform: translateX(-5px);
    }

    @media (max-width: 768px) {
        .attraction-detail-hero {
            height: 400px;
        }

        .attraction-detail-title {
            font-size: 28px;
        }

        .attraction-detail-content {
            font-size: 15px;
        }
    }
</style>

<div id="wrapper">
    <!-- content-->
    <div class="content">
      
        <!--  section  end-->

        <section class="gray-bg small-padding no-top-padding-sec" id="sec1">
            <div class="container">
                <div class="attraction-detail-container">
                   

                    <!-- Hero Image -->
                    <?php
                    $image = $attraction['img'] ?? '';
                    $imageDir = $attraction['img_dir'] ?? '';
                    $imageUrl = (!empty($image) && !empty($imageDir)) 
                        ? "https://aci-cms.aranetacity.com/webroot/files/attractions/{$imageDir}/{$image}"
                        : $this->Url->image('no-image.jpg');
                    ?>
                    <div class="attraction-detail-hero" style="margin-top: 35px;">
                        <img src="<?= $imageUrl ?>" alt="<?= h($attraction['title']) ?>" />
                    </div>

                    <!-- Title -->
                    <h1 class="attraction-detail-title"><?= h($attraction['title']) ?></h1>

                    <!-- Content -->
                    <div class="attraction-detail-content">
                        <?php if (!empty($attraction['content'])): ?>
                            <?= $attraction['content'] ?>
                        <?php else: ?>
                            <p>No description available for this attraction.</p>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Related Attractions -->
                <div class="list-single-main-item fl-wrap block_box">
                    <div class="list-single-main-item-title">
                        <h3>Explore More Attractions</h3>
                    </div>
                    <div class="list-single-main-item_content fl-wrap">
                        <?php if (!empty($relatedAttractions)): ?>
                            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 20px;">
                                <?php foreach ($relatedAttractions as $related): ?>
                                    <?php
                                    $relImage = $related['img'] ?? '';
                                    $relImageDir = $related['img_dir'] ?? '';
                                    $relTitle = $related['title'] ?? '';
                                    $relSlug = $related['slug'] ?? '';
                                    $relImageUrl = (!empty($relImage) && !empty($relImageDir))
                                        ? "https://aci-cms.aranetacity.com/{$relImageDir}/{$relImage}"
                                        : $this->Url->image('no-image.jpg');
                                    ?>
                                    <a href="/attraction/<?= h($relSlug) ?>" style="text-decoration: none; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.1); transition: all 0.3s ease; display: flex; flex-direction: column; height: 100%;">
                                        <div style="height: 350px; overflow: hidden;">
                                            <img src="<?= $relImageUrl ?>" alt="<?= h($relTitle) ?>" style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.3s ease;" />
                                        </div>
                                        <div style="padding: 15px; background: white; flex-grow: 1; display: flex; align-items: center;">
                                            <h4 style="margin: 0; color: #333; font-size: 15px; font-weight: 600;"><?= h($relTitle) ?></h4>
                                        </div>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                        <a href="/attractions" class="btn color2-bg float-btn">View All Attractions 
                            <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>

            </div>
        </section>

        <div class="limit-box fl-wrap"></div>
    </div>
    <!--content end-->
</div>

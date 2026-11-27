<?php
/**
 * Attractions Page
 * Displays all attractions and landmarks across Araneta City in a simple grid
 */

$baseUrl = env('API_BASE_URL', 'https://aci-cms.aranetacity.com');
?>

<style>
    /* Simple attractions grid - equal-sized cards */
    .attractions-grid-simple {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
        gap: 20px;
        margin-top: 30px;
        margin-bottom: 40px;
    }

    @media (max-width: 1024px) {
        .attractions-grid-simple {
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 15px;
        }
    }

    @media (max-width: 640px) {
        .attractions-grid-simple {
            grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
            gap: 12px;
        }
    }

    .attraction-card-simple {
        position: relative;
        width: 100%;
        padding-bottom: 100%;
        overflow: hidden;
        border-radius: 12px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        cursor: pointer;
    }

    .attraction-card-simple-inner {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        display: flex;
        align-items: flex-end;
        overflow: hidden;
    }

    .attraction-card-simple img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.4s ease;
        position: absolute;
        top: 0;
        left: 0;
    }

    .attraction-card-simple:hover img {
        transform: scale(1.05);
    }

    .attraction-card-simple-title {
        position: relative;
        z-index: 2;
        background: linear-gradient(to top, rgba(0, 0, 0, 0.8), transparent);
        width: 100%;
        padding: 20px 16px 16px 16px;
        color: #fff;
        font-size: 16px;
        font-weight: 600;
        text-align: left;
        line-height: 1.3;
        word-wrap: break-word;
        display: block;
        text-decoration: none;
        transition: color 0.3s ease;
    }

    .attraction-card-simple-title:hover {
        color: #00afab;
    }

    .attractions-empty {
        text-align: center;
        padding: 80px 20px;
        grid-column: 1 / -1;
    }

    .attractions-empty-icon {
        font-size: 64px;
        margin-bottom: 20px;
        color: #ddd;
    }

    .attractions-empty p {
        font-size: 18px;
        color: #999;
        margin: 0;
    }
</style>

<div id="wrapper">
    <!-- content-->
    <div class="content">
        <!--  section  -->
        <section class="parallax-section single-par" data-scrollax-parent="true">
            <div class="bg par-elem" data-bg="<?= $this->Url->image('bg/1.jpg') ?>"
                data-scrollax="properties: { translateY: '30%' }"></div>
            <div class="overlay op7"></div>
            <div class="container">
                <div class="section-title center-align big-title">
                    <h2><span>Attractions & Landmarks</span></h2>
                    <span class="section-separator"></span>
                </div>
            </div>
        </section>
        <!--  section  end-->

        <section class="gray-bg small-padding no-top-padding-sec" id="sec1">
            <div class="container">

                <!-- Attractions Simple Grid -->
                <div class="attractions-grid-simple fl-wrap">
                    <?php if (empty($attractions)): ?>
                        <div class="attractions-empty">
                            <div class="attractions-empty-icon">
                                <i class="fas fa-map-marker-alt"></i>
                            </div>
                            <p>No attractions found.</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($attractions as $attraction): ?>
                            <?php
                            $image = $attraction['img'] ?? '';
                            $imageDir = $attraction['img_dir'] ?? '';
                            $title = $attraction['title'] ?? '';
                            $slug = $attraction['slug'] ?? '';

                            // Construct image URL - correct format from API
                            $imageUrl = (!empty($image) && !empty($imageDir))
                                ? "https://aci-cms.aranetacity.com/{$imageDir}/{$image}"
                                : $this->Url->image('no-image.jpg');
                            ?>

                            <div class="attraction-card-simple">
                                <div class="attraction-card-simple-inner">
                                    <img src="<?= $imageUrl ?>" alt="<?= h($title) ?>" />
                                    <a href="/attraction/<?= h($slug) ?>" class="attraction-card-simple-title"><?= h($title) ?></a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                

            </div>
        </section>

        <div class="limit-box fl-wrap"></div>
    </div>
    <!--content end-->
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Attraction card click handlers
        const cards = document.querySelectorAll('.attraction-card-simple');
        cards.forEach(card => {
            card.addEventListener('click', function() {
                // Placeholder for future functionality (modal, detail page, etc)
                console.log('Attraction card clicked');
            });
        });
    });
</script>

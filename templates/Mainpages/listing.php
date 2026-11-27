<?php
/**
 * Listing Page
 * Displays category tiles: Shop, Dine, Services, Hotels
 */

$baseUrl = env('API_BASE_URL', 'https://aci-cms.aranetacity.com');
?>

<style>
    /* Category grid */
    .categories-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
        gap: 30px;
        margin-top: 50px;
        margin-bottom: 60px;
    }

    @media (max-width: 768px) {
        .categories-grid {
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
        }
    }

    @media (max-width: 480px) {
        .categories-grid {
            grid-template-columns: 1fr;
        }
    }

    .category-card {
        position: relative;
        background: white;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
        text-decoration: none;
        display: flex;
        flex-direction: column;
        padding: 40px 30px;
        text-align: center;
        min-height: 300px;
        justify-content: center;
    }

    .category-card:hover {
        transform: translateY(-12px);
        box-shadow: 0 12px 35px rgba(0, 0, 0, 0.15);
    }

    .category-icon {
        font-size: 64px;
        margin-bottom: 20px;
        display: block;
    }

    .category-card-title {
        font-size: 28px;
        font-weight: 700;
        color: #333;
        margin-bottom: 15px;
    }

    .category-card-description {
        font-size: 14px;
        color: #666;
        line-height: 1.6;
        margin-bottom: 25px;
    }

    .category-card-link {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        color: #00b4cc;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.3s ease;
    }

    .category-card:hover .category-card-link {
        gap: 12px;
        color: #0099aa;
    }
</style>

<div id="wrapper">
    <!-- content-->
    <div class="content">
        <section class="gray-bg small-padding" id="sec1">
            <div class="container">

                <!-- Category Cards Grid -->
                <div class="categories-grid fl-wrap">
                    <?php foreach ($categories as $category): ?>
                        <a href="<?= $category['link'] ?>" class="category-card">
                            <span class="category-icon"><?= $category['icon'] ?></span>
                            <h3 class="category-card-title"><?= $category['title'] ?></h3>
                            <p class="category-card-description"><?= $category['description'] ?></p>
                            <span class="category-card-link">
                                Explore <i class="fas fa-arrow-right"></i>
                            </span>
                        </a>
                    <?php endforeach; ?>
                </div>

            </div>
        </section>

        <div class="limit-box fl-wrap"></div>
    </div>
    <!--content end-->
</div>

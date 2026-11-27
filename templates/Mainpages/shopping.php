<?php
/**
 * Shopping Directory Page
 * Displays all shopping establishments across Araneta City malls
 */

$baseUrl = env('API_BASE_URL', 'https://aci-cms.aranetacity.com');
?>

<style>
    section {
        float: left;
        position: relative;
        padding: 30px 0!important;
        background: #fff;
        z-index: 1;
        width: 100%;
    }

    /* ===== Shopping Hero Banner ===== */
    .shop-hero {
        position: relative;
        min-height: 180px;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        text-align: center;
        background: linear-gradient(135deg, #0a1f44 0%, #00afab 100%);
    }

    .shop-hero-inner {
        position: relative;
        z-index: 2;
        padding: 32px 20px;
        max-width: 720px;
    }

    .shop-hero-breadcrumb {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        font-size: 12px;
        font-weight: 500;
        margin-bottom: 10px;
        color: rgba(255, 255, 255, 0.85);
    }

    .shop-hero-breadcrumb a {
        color: rgba(255, 255, 255, 0.85);
        text-decoration: none;
        transition: color 0.2s ease;
    }

    .shop-hero-breadcrumb a:hover {
        color: #fff;
    }

    .shop-hero-breadcrumb .current {
        color: #fff;
        font-weight: 600;
    }

    .shop-hero-title {
        color: #fff;
        font-size: 32px;
        font-weight: 800;
        margin: 0 0 8px;
        letter-spacing: -0.5px;
        line-height: 1.1;
    }

    .shop-hero-title::after {
        content: "";
        display: block;
        width: 50px;
        height: 3px;
        background: #00afab;
        border-radius: 4px;
        margin: 10px auto 0;
    }

    .shop-hero-subtitle {
        color: rgba(255, 255, 255, 0.92);
        font-size: 14px;
        line-height: 1.5;
        margin: 0;
        font-weight: 400;
    }

    @media (max-width: 768px) {
        .shop-hero {
            min-height: 150px;
        }

        .shop-hero-inner {
            padding: 26px 20px;
        }

        .shop-hero-title {
            font-size: 26px;
        }

        .shop-hero-subtitle {
            font-size: 13px;
        }
    }

    /* ===== Directory category switcher ===== */
    .dir-switcher {
        display: block;
        width: 100%;
        float: none;
        clear: both;
        background: #fff;
    }

    .dir-switcher .container {
        width: 92%;
        max-width: 1424px;
        margin: 0 auto;
    }

    .dir-switcher-inner {
        display: flex;
        justify-content: center;
        gap: 18px;
        padding: 26px 0;
        flex-wrap: wrap;
        width: 100%;
    }

    .dir-switcher-item {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 10px;
        width: 150px;
        padding: 22px 12px;
        border-radius: 14px;
        font-size: 15px;
        font-weight: 600;
        color: #333;
        text-decoration: none;
        border: 1px solid #eee;
        background: #fff;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        transition: transform 0.25s ease, box-shadow 0.25s ease, border-color 0.25s ease;
    }

    .dir-switcher-item img {
        width: 80px;
        height: 80px;
        object-fit: contain;
        transition: transform 0.25s ease;
    }

    .dir-switcher-item:hover img {
        transform: scale(1.08);
    }

    .dir-switcher-item:hover {
        transform: translateY(-4px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        border-color: #b8e6e4;
    }

    .dir-switcher-item.is-active {
        border-color: #00afab;
        box-shadow: 0 6px 16px rgba(0, 175, 171, 0.2);
    }

    @media (max-width: 480px) {
        .dir-switcher-inner {
            gap: 12px;
            padding: 20px 0;
        }

        .dir-switcher-item {
            width: 100px;
            padding: 16px 8px;
            font-size: 13px;
        }

        .dir-switcher-item img {
            width: 38px;
            height: 38px;
        }
    }

    /* Building carousel - native scrollable strip */
    .building-carousel-wrap {
        position: relative;
        height: auto;
    }

    .building-carousel {
        position: relative;
    }

    .building-swiper-container {
        display: flex;
        overflow-x: auto;
        overflow-y: hidden;
        -webkit-overflow-scrolling: touch;
        scroll-snap-type: x proximity;
        scrollbar-width: thin;
        scroll-behavior: smooth;
    }

    .building-swiper-container::-webkit-scrollbar {
        height: 6px;
    }

    .building-swiper-container::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }

    .building-swiper-container::-webkit-scrollbar-thumb {
        background: #6B2D7B;
        border-radius: 10px;
    }

    .building-swiper-container::-webkit-scrollbar-thumb:hover {
        background: #00afab;
    }

    .building-swiper-container .building-track {
        display: flex !important;
        flex-wrap: nowrap !important;
        gap: 15px !important;
        padding: 10px 4px;
    }

    .building-swiper-container .building-item {
        width: 140px !important;
        height: 140px !important;
        display: flex !important;
        flex-shrink: 0;
        scroll-snap-align: start;
    }

    .building-carousel-nav {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        background: #fff;
        color: #6B2D7B;
        border: none;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 10;
        transition: all 0.2s ease;
        font-size: 16px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.25);
    }

    .building-carousel-nav:hover {
        background: #6B2D7B;
        color: #fff;
        transform: translateY(-50%) scale(1.1);
    }

    .building-carousel-nav.prev {
        left: 8px;
    }

    .building-carousel-nav.next {
        right: 8px;
    }

    @media (max-width: 768px) {
        .building-carousel-nav {
            display: none;
        }
    }

    /* Modern Establishment Card Styles */
    .est-card-wrapper {
        width: 100%;
    }

    .est-card {
        display: flex;
        flex-direction: column;
        height: 100%;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 4px 14px rgba(0, 0, 0, 0.08);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        background: #fff;
    }

    .est-card:hover {
        box-shadow: 0 10px 24px rgba(0, 0, 0, 0.14);
        transform: translateY(-4px);
    }

    /* Large facade image on top */
    .est-card-image {
        position: relative;
        overflow: hidden;
        aspect-ratio: 4 / 3;
        flex-shrink: 0;
        background: #f5f5f5;
    }

    .est-card-image-link {
        display: block;
        width: 100%;
        height: 100%;
    }

    .est-card-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s ease;
    }

    .est-card:hover .est-card-image img {
        transform: scale(1.06);
    }

    /* Content: logo + title row, then location */
    .est-card-content {
        flex: 1;
        display: flex;
        flex-direction: column;
        padding: 16px 18px 18px;
        gap: 10px;
        align-items: flex-start;
        text-align: left;
    }

    .est-card-header {
        display: flex;
        flex-direction: row;
        align-items: center;
        gap: 12px;
        width: 100%;
    }

    .est-card-logo {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        overflow: hidden;
        flex-shrink: 0;
        background: #fff;
        border: 1px solid #eee;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .est-card-logo img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .est-card-title {
        margin: 0;
        font-size: 18px;
        font-weight: 600;
        line-height: 1.25;
        color: #1a1a1a;
        overflow: hidden;
        display: -webkit-box;
        -webkit-line-clamp: 1;
        -webkit-box-orient: vertical;
    }

    .est-card-title a {
        color: #1a1a1a;
        text-decoration: none;
        transition: color 0.2s ease;
    }

    .est-card-title a:hover {
        color: #00afab;
    }

    .est-card-location {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 15px;
        color: #6b7280;
        margin: 0;
        font-weight: 400;
    }

    .est-card-location i {
        color: #00afab;
        font-size: 14px;
        flex-shrink: 0;
    }

    /* 5-column grid layout - custom wrapper */
    .est-grid-container {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 20px;
        width: 100%;
    }

    @media (max-width: 1400px) {
        .est-grid-container {
            grid-template-columns: repeat(4, 1fr);
        }
    }

    @media (max-width: 1200px) {
        .est-grid-container {
            grid-template-columns: repeat(3, 1fr);
        }
    }

    @media (max-width: 768px) {
        .est-grid-container {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 480px) {
        .est-grid-container {
            grid-template-columns: 1fr;
        }
    }
</style>

<div id="wrapper">
    <!-- content-->
    <div class="content">
        <!-- Directory category switcher -->
        <div class="dir-switcher">
            <div class="container">
                <div class="dir-switcher-inner">
                    <a href="<?= $this->Url->build('/shopping') ?>" class="dir-switcher-item is-active">
                        <img src="<?= $this->Url->image('categories/ACI_Website_Shop.png') ?>" alt="Shopping">
                        <span>Shopping</span>
                    </a>
                    <a href="<?= $this->Url->build('/dining') ?>" class="dir-switcher-item">
                        <img src="<?= $this->Url->image('categories/ACI_Website_Dine.png') ?>" alt="Dining">
                        <span>Dining</span>
                    </a>
                    <a href="<?= $this->Url->build('/services') ?>" class="dir-switcher-item">
                        <img src="<?= $this->Url->image('categories/Services.png') ?>" alt="Services">
                        <span>Services</span>
                    </a>
                </div>
            </div>
        </div>
        <section class="small-padding no-top-padding-sec" id="sec1" style="background:#fff;">
            <div class="container">
                <!-- Filter Button for Mobile -->
                <div class="mob-nav-content-btn color2-bg show-list-wrap-search ntm fl-wrap">
                    <i class="fas fa-filter"></i> Filters
                </div>

                <!-- Search and Filter Section -->
                <div class="fl-wrap" style="margin-bottom: 0;">
                    <div class="listsearch-input-wrap lws_mobile fl-wrap tabs-act inline-lsiw" id="lisfw"
                        style="padding: 15px 0; margin-bottom: 0;">
                        <!-- Filters Row -->
                        <div class="fl-wrap" style="padding: 5px 0; margin: 0;">
                            <div class="fl-wrap" style="padding: 0; margin: 0;">
                                <div class="row"
                                    style="margin: 0; display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
                                    <!-- Search Input -->
                                    <div style="flex: 1.5 1 auto; min-width: 150px;" class="search-input-wrapper">
                                        <div class="listsearch-input-item"
                                            style="padding: 0; margin: 0; display: flex; align-items: center; border: 1px solid #e0e0e0; border-radius: 10px; background: #fff; height: 44px;">
                                            <input type="text" id="searchInput"
                                                placeholder="Search…"
                                                style="border: none; outline: none; flex: 1; height: 100%; padding: 0 16px; background: transparent; font-family: 'Poppins', sans-serif; font-size: 14px;"
                                                value="<?= isset($searchQuery) ? h($searchQuery) : '' ?>" />
                                        </div>
                                    </div>

                                    <!-- Category Dropdown -->
                                    <div style="flex: 1 1 auto; min-width: 140px;" class="category-filter-wrapper">
                                        <div class="listsearch-input-item"
                                            style="padding: 0; margin: 0; display: flex; align-items: center; border: 1px solid #e0e0e0; border-radius: 10px; background: #fff; height: 44px;">
                                            <select id="categoryFilter"
                                                style="border: none; outline: none; flex: 1; height: 100%; padding: 0 12px; background: transparent; cursor: pointer; font-size: 14px;">
                                                <option value="">All Categories</option>
                                                <?php if (!empty($categories)): ?>
                                                    <?php foreach ($categories as $category): ?>
                                                        <option value="<?= h($category) ?>" <?= isset($categoryFilter) && $categoryFilter === $category ? 'selected' : '' ?>>
                                                            <?= h($category) ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </select>
                                        </div>
                                    </div>

                                    <!-- Building Dropdown -->
                                    <div style="flex: 1 1 auto; min-width: 140px;" class="building-filter-wrapper">
                                        <div class="listsearch-input-item"
                                            style="padding: 0; margin: 0; display: flex; align-items: center; border: 1px solid #e0e0e0; border-radius: 10px; background: #fff; height: 44px;">
                                            <select id="buildingFilter"
                                                style="border: none; outline: none; flex: 1; height: 100%; padding: 0 12px; background: transparent; cursor: pointer; font-size: 14px;">
                                                <option value="">All Buildings</option>
                                                <?php if (!empty($buildings)): ?>
                                                    <?php foreach ($buildings as $building): ?>
                                                        <option value="<?= h($building['slug']) ?>" <?= isset($selectedBuildingSlug) && strtolower($selectedBuildingSlug) === strtolower($building['slug']) ? 'selected' : '' ?>>
                                                            <?= h($building['building_name']) ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </select>
                                        </div>
                                    </div>

                                    <!-- Search Button -->
                                    <div style="flex: 0 0 auto; min-width: 110px;" class="search-button-wrapper">
                                        <div class="listsearch-input-item" style="margin-bottom: 0;">
                                            <button type="button" class="header-search-button color-bg"
                                                style="width: 100%; height: 44px; border-radius: 10px;">
                                                <i class="fas fa-search"></i><span>Search</span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Listing Items Container -->
                    <div class="est-grid-container">
                        <?php if (empty($establishments)): ?>
                            <div style="width: 100%; padding: 40px; text-align: center;">
                                <p style="font-size: 16px; color: #666;">No shopping establishments found. Please check the
                                    filters or try again later.</p>
                            </div>
                        <?php else: ?>
                            <?php foreach ($establishments as $item): ?>
                                <?php
                                $image = $item['image'] ?? '';
                                $imageDir = $item['img_dir'] ?? '';
                                $facadeImg = $item['facade_img'] ?? '';
                                $facadeDir = $item['facade_dir'] ?? '';
                                $building = $item['building'] ?? 'Araneta City';
                                $level = $item['level'] ?? '';
                                $title = $item['title'] ?? '';

                                // Logo image URL (from image/img_dir)
                                if (!empty($image) && !empty($imageDir)) {
                                    $logoUrl = $baseUrl . '/files/establishments/' . $imageDir . '/' . $image;
                                } else {
                                    $logoUrl = $this->Url->image('no-image.jpg');
                                }

                                // Facade image URL (large image). If missing, fall back to the logo image.
                                if (!empty($facadeImg) && !empty($facadeDir)) {
                                    $facadeUrl = $baseUrl . '/files/establishments/' . $facadeDir . '/' . $facadeImg;
                                } else {
                                    $facadeUrl = $logoUrl;
                                }
                                ?>

                                <!-- listing-item -->
                                <div class="est-card-wrapper">
                                    <article class="est-card">
                                        <!-- Facade / large image -->
                                        <div class="est-card-image">
                                            <a href="/establishment/<?= h($item['id']) ?>" class="est-card-image-link">
                                                <img src="<?= $facadeUrl ?>" alt="<?= h($title) ?>"
                                                    onerror="this.onerror=null;this.src='<?= $this->Url->image('no-image.jpg') ?>';">
                                            </a>
                                        </div>

                                        <!-- Content Section -->
                                        <div class="est-card-content">
                                            <!-- Logo + Title -->
                                            <div class="est-card-header">
                                                <div class="est-card-logo">
                                                    <img src="<?= $logoUrl ?>" alt="<?= h($title) ?> logo"
                                                        onerror="this.onerror=null;this.src='<?= $this->Url->image('no-image.jpg') ?>';">
                                                </div>
                                                <h3 class="est-card-title">
                                                    <a href="/establishment/<?= h($item['id']) ?>"><?= h($title) ?></a>
                                                </h3>
                                            </div>

                                            <!-- Location -->
                                            <div class="est-card-location">
                                                <i class="fas fa-map-marker-alt"></i>
                                                <span>
                                                    <?php if (!empty($level)): ?>
                                                        <?= h($level) ?>, <?= h($building) ?>
                                                    <?php else: ?>
                                                        <?= h($building) ?>
                                                    <?php endif; ?>
                                                </span>
                                            </div>
                                        </div>
                                    </article>
                                </div>
                                <!-- listing-item end -->
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                    <!-- est-grid-container end -->

                    <!-- Pagination -->
                    <?php if (!empty($paging) && $paging['pageCount'] > 1): ?>
                        <?php
                        // Build a page URL that PRESERVES the current filters
                        // (building / search / category) instead of resetting them.
                        $pageUrl = function ($p) {
                            $params = ['page' => $p];
                            if (!empty($_GET['building'])) {
                                $params['building'] = $_GET['building'];
                            }
                            if (!empty($_GET['search'])) {
                                $params['search'] = $_GET['search'];
                            }
                            if (!empty($_GET['category'])) {
                                $params['category'] = $_GET['category'];
                            }
                            return '?' . http_build_query($params);
                        };
                        ?>
                        <div class="pagination fwmpag">
                            <?php if ($paging['page'] > 1): ?>
                                <a href="<?= h($pageUrl($paging['page'] - 1)) ?>" class="prevposts-link"><i class="fas fa-caret-left"></i><span>Prev</span></a>
                            <?php endif; ?>

                            <?php for ($i = 1; $i <= $paging['pageCount']; $i++): ?>
                                <?php if ($i == $paging['page']): ?>
                                    <a href="<?= h($pageUrl($i)) ?>" class="current-page"><?= $i ?></a>
                                <?php else: ?>
                                    <a href="<?= h($pageUrl($i)) ?>"><?= $i ?></a>
                                <?php endif; ?>
                            <?php endfor; ?>

                            <?php if ($paging['page'] < $paging['pageCount']): ?>
                                <a href="<?= h($pageUrl($paging['page'] + 1)) ?>" class="nextposts-link"><span>Next</span><i
                                        class="fas fa-caret-right"></i></a>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
        </section>

        <div class="limit-box fl-wrap"></div>
    </div>
    <!--content end-->
</div>

<script>
    (function () {
        'use strict';

        // Wait for DOM to be ready
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', init);
        } else {
            init();
        }

        function init() {
            initSearch();
        }

        function initSearch() {
            const searchInput = document.getElementById('searchInput');
            const categoryFilter = document.getElementById('categoryFilter');
            const buildingFilter = document.getElementById('buildingFilter');
            // Be more specific: target the button inside the search-button-wrapper, not the header button
            const searchButton = document.querySelector('.search-button-wrapper .header-search-button');

            if (!searchInput || !searchButton) {
                return;
            }

            // Use capture phase since that's what's firing
            searchButton.addEventListener('click', function (e) {
                e.preventDefault();
                e.stopPropagation();
                doSearch();
            }, true);

            // Add Enter key listener to search input
            searchInput.addEventListener('keypress', (e) => {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    doSearch();
                }
            });

            // Apply filter immediately when category changes
            if (categoryFilter) {
                categoryFilter.addEventListener('change', () => {
                    doSearch();
                });
            }

            // Apply filter immediately when building changes
            if (buildingFilter) {
                buildingFilter.addEventListener('change', () => {
                    doSearch();
                });
            }

            function doSearch() {
                const query = (searchInput.value || '').trim();
                const category = categoryFilter ? categoryFilter.value : '';
                const building = buildingFilter ? buildingFilter.value : '';

                const newParams = new URLSearchParams();
                if (building) {
                    newParams.set('building', building);
                }
                if (query) {
                    newParams.set('search', query);
                }
                if (category) {
                    newParams.set('category', category);
                }

                const newUrl = '/shopping' + (newParams.toString() ? '?' + newParams.toString() : '');
                window.location.href = newUrl;
            }
        }
    })();
</script>
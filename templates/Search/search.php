<?php
/**
 * Search Results Page
 * Displays unified search results across establishments, events, news, and happenings
 *
 * @var \App\View\AppView $this
 * @var array $results Search results organized by type
 * @var string $query Search query string
 * @var string $category Selected category filter
 * @var string $building Selected building filter
 * @var array $buildings Available buildings
 * @var array $categories Available categories
 * @var string $baseUrl API base URL
 * @var array $pagination Pagination information
 */

$noImage = $this->Url->image('no-image.jpg');
?>

<style>
    :root {
        --ac-primary: #00afab;
        --ac-secondary: #93358d;
        --ac-ink: #14131A;
        --ac-muted: #6B6A70;
        --ac-line: rgba(20,19,26,0.1);
        --ac-tint: #F3FBFA;
    }

    .search-page {
        background: #fff;
        padding: 56px 0;
        font-family: 'Sora', -apple-system, BlinkMacSystemFont, sans-serif;
        color: var(--ac-ink);
    }

    .search-wrap {
        width: 92%;
        max-width: 1000px;
        margin: 0 auto;
    }

    .search-header {
        margin-bottom: 32px;
    }

    .search-title {
        font-size: 34px;
        font-weight: 800;
        color: var(--ac-ink);
        margin: 0 0 8px;
        letter-spacing: -0.02em;
    }

    .search-meta {
        font-size: 15px;
        color: var(--ac-muted);
        margin: 0;
    }

    .search-meta strong {
        color: var(--ac-secondary);
        font-weight: 700;
    }

    /* Filter bar */
    .search-filter-bar {
        display: flex;
        flex-wrap: wrap;
        align-items: flex-end;
        gap: 14px;
        background: #fff;
        border: 1px solid var(--ac-line);
        border-radius: 14px;
        padding: 22px;
        margin-bottom: 44px;
    }

    .search-filter-field {
        display: flex;
        flex-direction: column;
        flex: 1 1 0px;
    }

    .search-filter-field label {
        font-size: 11.5px;
        font-weight: 700;
        letter-spacing: 0.03em;
        text-transform: uppercase;
        color: var(--ac-muted);
        margin-bottom: 8px;
    }

    .search-filter-field input,
    .search-filter-field select {
        height: 44px;
        border: 1px solid var(--ac-line);
        border-radius: 10px;
        padding: 0 14px;
        font-size: 14px;
        color: var(--ac-ink);
        background: #fff;
        font-family: inherit;
    }

    .search-filter-field input:focus,
    .search-filter-field select:focus {
        outline: none;
        border-color: var(--ac-primary);
    }

    .search-filter-btn {
        height: 44px;
        padding: 0 26px;
        border: 0;
        border-radius: 999px;
        background: var(--ac-secondary);
        color: #fff;
        font-weight: 600;
        font-size: 14px;
        cursor: pointer;
        transition: background 0.2s ease;
        flex-shrink: 0;
    }

    .search-filter-btn:hover {
        background: #7c2b76;
    }

    /* Result section */
    .search-section {
        margin-bottom: 48px;
    }

    .search-pagination {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
        margin: 12px 0 0;
    }

    .search-pagination a,
    .search-pagination .disabled {
        padding: 10px 16px;
        border: 1px solid var(--ac-line);
        border-radius: 999px;
        color: var(--ac-primary);
        font-size: 14px;
        font-weight: 600;
        text-decoration: none;
    }

    .search-pagination a:hover {
        border-color: var(--ac-primary);
        background: var(--ac-tint);
    }

    .search-pagination .current {
        padding: 10px 16px;
        border: 1px solid var(--ac-primary);
        border-radius: 999px;
        background: var(--ac-primary);
        color: #fff;
        font-size: 14px;
        font-weight: 600;
    }

    .search-pagination .disabled {
        color: #aaa;
    }

    .search-pagination-info {
        color: var(--ac-muted);
        font-size: 14px;
        margin: 8px 0 40px;
        text-align: center;
    }

    .search-section-title {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 13px;
        font-weight: 700;
        color: var(--ac-muted);
        letter-spacing: 0.03em;
        text-transform: uppercase;
        margin: 0 0 20px;
    }

    .search-section-title i {
        color: var(--ac-primary);
        font-size: 14px;
    }

    .search-section-empty {
        color: var(--ac-muted);
        font-size: 14px;
        padding: 20px;
        background: var(--ac-tint);
        border-radius: 10px;
    }

    /* Results list */
    .search-results {
        display: flex;
        flex-direction: column;
        gap: 14px;
    }

    /* Result card */
    .search-result-card {
        display: flex;
        gap: 18px;
        background: #fff;
        border: 1px solid var(--ac-line);
        border-radius: 14px;
        overflow: hidden;
        transition: border-color 0.2s ease, box-shadow 0.2s ease;
        text-decoration: none;
        color: inherit;
        padding: 14px;
        align-items: center;
    }

    a.search-result-card:hover {
        border-color: var(--ac-primary);
        box-shadow: 0 8px 24px rgba(20,19,26,0.06);
    }

    .search-result-image {
        width: 84px;
        height: 84px;
        border-radius: 10px;
        object-fit: cover;
        flex-shrink: 0;
    }

    .search-result-body {
        flex: 1;
        min-width: 0;
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .search-result-title {
        font-size: 16px;
        font-weight: 700;
        color: var(--ac-ink);
        margin: 0;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        text-align: left;
    }

    .search-result-tags {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 8px;
    }

    .search-result-tag {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        font-size: 12px;
        font-weight: 600;
        color: var(--ac-muted);
        border: 1px solid var(--ac-line);
        padding: 4px 10px;
        border-radius: 999px;
        white-space: nowrap;
    }

    .search-result-tag i {
        color: var(--ac-primary);
        font-size: 11px;
    }

    .search-result-desc {
        font-size: 13.5px;
        color: var(--ac-muted);
        line-height: 1.5;
        margin: 0;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .search-result-badge {
        display: inline-flex;
        align-self: flex-start;
        background: var(--ac-tint);
        color: #0a8b84;
        font-size: 11.5px;
        font-weight: 700;
        padding: 5px 12px;
        border-radius: 999px;
    }

    /* Empty state */
    .search-empty {
        text-align: center;
        padding: 64px 20px;
        background: var(--ac-tint);
        border-radius: 14px;
    }

    .search-empty i {
        font-size: 34px;
        color: var(--ac-primary);
        opacity: 0.5;
        margin-bottom: 14px;
    }

    .search-empty p {
        font-size: 16px;
        color: var(--ac-muted);
        margin: 0;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .search-title {
            font-size: 26px;
        }

        .search-filter-bar {
            flex-direction: column;
            align-items: stretch;
        }

        .search-filter-btn {
            align-self: stretch;
        }

        .search-result-card {
            flex-direction: column;
            align-items: flex-start;
        }

        .search-result-image {
            width: 100%;
            height: 180px;
        }
    }
</style>

<div id="wrapper">
    <div class="content">
        <div class="search-page">
            <div class="search-wrap">

                <!-- Search Header -->
                <div class="search-header">
                    <h1 class="search-title">Search Results</h1>
                    <?php if (!empty($query) || !empty($category) || !empty($building)): ?>
                        <p class="search-meta">
                            <?php if (!empty($query)): ?>
                                Results for <strong>"<?= h($query) ?>"</strong>
                            <?php endif; ?>
                            <?php if (!empty($category)): ?>
                                in <strong><?= h($category) ?></strong>
                            <?php endif; ?>
                            <?php if (!empty($building)): ?>
                                <?php $buildingName = $buildings[$building] ?? ($buildings[(int)$building] ?? $building); ?>
                                at <strong><?= h($buildingName) ?></strong>
                            <?php endif; ?>
                        </p>
                    <?php endif; ?>
                </div>

                <!-- Filter Bar -->
                <form class="search-filter-bar" method="GET">
                    <div class="search-filter-field" style="flex: 1 1 0px;">
                        <label>Search Query</label>
                        <input type="text" name="q" value="<?= h($query) ?>" placeholder="Enter search term..." />
                    </div>
                    <div class="search-filter-field">
                        <label>Category</label>
                        <select name="category">
                            <option value="">All Categories</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?= h($cat) ?>" <?= $category === $cat ? 'selected' : '' ?>><?= h($cat) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="search-filter-field">
                        <label>Building</label>
                        <select name="building">
                            <option value="">All Buildings</option>
                            <?php foreach ($buildings as $bid => $bname): ?>
                                <option value="<?= h($bid) ?>" <?= $building === (string)$bid ? 'selected' : '' ?>><?= h($bname) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" class="search-filter-btn"><i class="fas fa-search"></i> Refine Search</button>
                </form>

                <!-- Results -->
                <?php
                $hasResults = !empty($results['establishments']) || !empty($results['events'])
                    || !empty($results['news']) || !empty($results['happenings'])
                    || !empty($results['shows']) || !empty($results['movies']);
                ?>

                <?php if (empty($query) && empty($category) && empty($building)): ?>
                    <div class="search-empty">
                        <i class="fas fa-search"></i>
                        <p>Enter a search term or select a category or building to get started</p>
                    </div>

                <?php elseif (!$hasResults): ?>
                    <div class="search-empty">
                        <i class="fas fa-search"></i>
                        <?php if (!empty($query)): ?>
                            <p>No results found for "<?= h($query) ?>"</p>
                        <?php else: ?>
                            <p>No results found for the selected filters</p>
                        <?php endif; ?>
                    </div>

                <?php else: ?>
                    <?php
                    $paginationQuery = array_filter([
                        'q' => $query,
                        'category' => $category,
                        'building' => $building,
                    ], static function ($value) {
                        return $value !== '';
                    });
                    ?>

                    <!-- Establishments (Shops, Restaurants, Services) -->
                    <?php if (!empty($results['establishments'])): ?>
                        <div class="search-section">
                            <h2 class="search-section-title"><i class="fas fa-store"></i> Stores &amp; Restaurants</h2>
                            <div class="search-results">
                                <?php foreach ($results['establishments'] as $est): ?>
                                    <?php
                                    $estImg = $est['image'] ?? '';
                                    $estDir = $est['img_dir'] ?? '';
                                    $estImageUrl = (!empty($estImg) && !empty($estDir))
                                        ? "{$baseUrl}/files/establishments/{$estDir}/{$estImg}"
                                        : $noImage;
                                    $estUrl = $this->Url->build([
                                        'controller' => 'Mainpages',
                                        'action' => 'establishmentDetail',
                                        'id' => $est['id'] ?? ''
                                    ]);
                                    ?>
                                    <a href="<?= $estUrl ?>" class="search-result-card">
                                        <img src="<?= h($estImageUrl) ?>" alt="<?= h($est['title'] ?? '') ?>" class="search-result-image" loading="lazy" decoding="async" onerror="this.onerror=null;this.src='<?= $noImage ?>';">
                                        <div class="search-result-body">
                                            <h3 class="search-result-title"><?= h($est['title'] ?? '') ?></h3>
                                            <div class="search-result-tags">
                                                <?php if (!empty($est['building'])): ?>
                                                    <span class="search-result-tag"><i class="fas fa-map-marker-alt"></i> <?= h($est['building']) ?></span>
                                                <?php endif; ?>
                                                <?php if (!empty($est['category'])): ?>
                                                    <span class="search-result-tag"><i class="fas fa-tag"></i> <?= h($est['category']) ?></span>
                                                <?php endif; ?>
                                            </div>
                                            <?php if (!empty($est['estmaincategory'])): ?>
                                                <span class="search-result-badge"><?= h($est['estmaincategory']) ?></span>
                                            <?php endif; ?>
                                        </div>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Events -->
                    <?php if (!empty($results['events'])): ?>
                        <div class="search-section">
                            <h2 class="search-section-title"><i class="fas fa-calendar-days"></i> Events</h2>
                            <div class="search-results">
                                <?php foreach ($results['events'] as $event): ?>
                                    <?php
                                    $eventSlug = $event['slug'] ?? '';
                                    $eventUrl = !empty($eventSlug)
                                        ? $this->Url->build('/event/' . h($eventSlug))
                                        : '#';
                                    
                                    // Build thumbnail-first URL (falls back to original via onerror).
                                    $eventRaw = $event['img_url'] ?? '';
                                    $eventImageUrl = $noImage;
                                    $eventOriginal = '';
                                    if (!empty($eventRaw)) {
                                        $eventPath = str_replace('\\', '/', $eventRaw);
                                        $eventDir = dirname($eventPath);
                                        $eventFile = basename($eventPath);
                                        $eventImageUrl = rtrim($baseUrl, '/') . $eventDir . '/thumbnail-' . $eventFile;
                                        $eventOriginal = rtrim($baseUrl, '/') . $eventDir . '/' . $eventFile;
                                    }
                                    
                                    $eventTitle = $event['event_title'] ?? ($event['title'] ?? 'Untitled Event');
                                    $eventDate = $event['display_date'] ?? $event['date'] ?? '';
                                    $eventVenue = $event['venue'] ?? '';
                                    ?>
                                    <a href="<?= $eventUrl ?>" class="search-result-card">
                                        <img src="<?= h($eventImageUrl) ?>" alt="<?= h($eventTitle) ?>" class="search-result-image" loading="lazy" decoding="async" data-original="<?= h($eventOriginal) ?>" data-fallback="<?= h($noImage) ?>" onerror="if(this.dataset.original && this.src!==this.dataset.original){this.src=this.dataset.original;}else{this.onerror=null;this.src=this.dataset.fallback;}">
                                        <div class="search-result-body">
                                            <h3 class="search-result-title"><?= h($eventTitle) ?></h3>
                                            <div class="search-result-tags">
                                                <span class="search-result-tag"><i class="fas fa-calendar"></i> <?= h($eventDate) ?></span>
                                                <?php if (!empty($eventVenue)): ?>
                                                    <span class="search-result-tag"><i class="fas fa-map-marker-alt"></i> <?= h($eventVenue) ?></span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- News -->
                    <?php if (!empty($results['news'])): ?>
                        <div class="search-section">
                            <h2 class="search-section-title"><i class="fas fa-newspaper"></i> News</h2>
                            <div class="search-results">
                                <?php foreach ($results['news'] as $news): ?>
                                    <?php
                                    $newsSlug = $news['slug'] ?? $news['alias'] ?? '';
                                    $newsUrl = !empty($newsSlug) 
                                        ? $this->Url->build('/news-article/' . h($newsSlug))
                                        : '#';
                                    
                                    // Build thumbnail-first URL (falls back to original via onerror).
                                    $newsRaw = $news['img_url'] ?? '';
                                    $newsImageUrl = $noImage;
                                    $newsOriginal = '';
                                    if (!empty($newsRaw)) {
                                        $newsPath = str_replace('\\', '/', $newsRaw);
                                        $newsDir = dirname($newsPath);
                                        $newsFile = basename($newsPath);
                                        $newsImageUrl = rtrim($baseUrl, '/') . $newsDir . '/thumbnail-' . $newsFile;
                                        $newsOriginal = rtrim($baseUrl, '/') . $newsDir . '/' . $newsFile;
                                    }
                                    
                                    $newsTitle = $news['news_title'] ?? $news['title'] ?? 'Untitled News';
                                    $newsDesc = strip_tags($news['introtext'] ?? $news['description'] ?? '');
                                    if (strlen($newsDesc) > 100) {
                                        $newsDesc = substr($newsDesc, 0, 100) . '...';
                                    }
                                    ?>
                                    <a href="<?= $newsUrl ?>" class="search-result-card">
                                        <img src="<?= h($newsImageUrl) ?>" alt="<?= h($newsTitle) ?>" class="search-result-image" loading="lazy" decoding="async" data-original="<?= h($newsOriginal) ?>" data-fallback="<?= h($noImage) ?>" onerror="if(this.dataset.original && this.src!==this.dataset.original){this.src=this.dataset.original;}else{this.onerror=null;this.src=this.dataset.fallback;}">
                                        <div class="search-result-body">
                                            <h3 class="search-result-title"><?= h($newsTitle) ?></h3>
                                            <p class="search-result-desc"><?= h($newsDesc) ?></p>
                                        </div>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Happenings -->
                    <?php if (!empty($results['happenings'])): ?>
                        <div class="search-section">
                            <h2 class="search-section-title"><i class="fas fa-star"></i> Happenings</h2>
                            <div class="search-results">
                                <?php foreach ($results['happenings'] as $happening): ?>
                                    <?php
                                    $happSlug = $happening['slug'] ?? '';
                                    $happUrl = !empty($happSlug)
                                        ? $this->Url->build('/happening/' . h($happSlug))
                                        : '#';
                                    
                                    // Build a thumbnail-first URL (falls back to original via onerror).
                                    $happRaw = $happening['img_url'] ?? '';
                                    $happImageUrl = $noImage;
                                    $happOriginal = '';
                                    if (!empty($happRaw)) {
                                        $happPath = str_replace('\\', '/', $happRaw);
                                        $happDir = dirname($happPath);
                                        $happFile = basename($happPath);
                                        $happImageUrl = rtrim($baseUrl, '/') . $happDir . '/thumbnail-' . $happFile;
                                        $happOriginal = rtrim($baseUrl, '/') . $happDir . '/' . $happFile;
                                    }
                                    $happTitle = $happening['event_title'] ?? $happening['title'] ?? 'Untitled Happening';
                                    $happDate = $happening['display_date'] ?? $happening['date'] ?? '';
                                    ?>
                                    <a href="<?= $happUrl ?>" class="search-result-card">
                                        <img src="<?= h($happImageUrl) ?>" alt="<?= h($happTitle) ?>" class="search-result-image" loading="lazy" decoding="async" data-original="<?= h($happOriginal) ?>" data-fallback="<?= h($noImage) ?>" onerror="if(this.dataset.original && this.src!==this.dataset.original){this.src=this.dataset.original;}else{this.onerror=null;this.src=this.dataset.fallback;}">
                                        <div class="search-result-body">
                                            <h3 class="search-result-title"><?= h($happTitle) ?></h3>
                                            <div class="search-result-tags">
                                                <span class="search-result-tag"><i class="fas fa-calendar"></i> <?= h($happDate) ?></span>
                                            </div>
                                        </div>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Shows (Concert & Theater) -->
                    <?php if (!empty($results['shows'])): ?>
                        <div class="search-section">
                            <h2 class="search-section-title"><i class="fas fa-masks-theater"></i> Shows</h2>
                            <div class="search-results">
                                <?php foreach ($results['shows'] as $show): ?>
                                    <?php
                                    $showTitle = $show['title'] ?? $show['event_title'] ?? 'Untitled Show';
                                    $showUrl = h($show['detailPage'] ?? $show['link'] ?? '#');
                                    $showVenue = $show['venue'] ?? '';
                                    $showDate = $show['displaydate'] ?? $show['dateofevent'] ?? '';
                                    
                                    // Build poster URL
                                    $showCdn = 'https://dohyi8q62t82e.cloudfront.net/';
                                    $showImageUrl = $noImage;
                                    if (!empty($show['poster_dir'])) {
                                        $showImageUrl = $showCdn . ltrim(h($show['poster_dir']), '/');
                                    } elseif (!empty($show['poster'])) {
                                        $showImageUrl = h($show['poster']);
                                    }
                                    ?>
                                    <a href="<?= $showUrl ?>" target="_blank" rel="noopener" class="search-result-card">
                                        <img src="<?= $showImageUrl ?>" alt="<?= h($showTitle) ?>" class="search-result-image" loading="lazy" decoding="async" onerror="this.onerror=null;this.src='<?= $noImage ?>';">
                                        <div class="search-result-body">
                                            <h3 class="search-result-title"><?= h($showTitle) ?></h3>
                                            <div class="search-result-tags">
                                                <?php if (!empty($showVenue)): ?>
                                                    <span class="search-result-tag"><i class="fas fa-map-marker-alt"></i> <?= h($showVenue) ?></span>
                                                <?php endif; ?>
                                                <?php if (!empty($showDate)): ?>
                                                    <span class="search-result-tag"><i class="fas fa-calendar"></i> <?= h($showDate) ?></span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Movies -->
                    <?php if (!empty($results['movies'])): ?>
                        <div class="search-section">
                            <h2 class="search-section-title"><i class="fas fa-film"></i> Movies</h2>
                            <div class="search-results">
                                <?php foreach ($results['movies'] as $movie): ?>
                                    <?php
                                    $movieTitle = $movie['title'] ?? 'Untitled Movie';
                                    $movieUrl = h($movie['detailPage'] ?? $movie['link'] ?? '#');
                                    $movieDate = $movie['displaydate'] ?? '';
                                    
                                    // Build poster URL
                                    $movieCdn = 'https://dohyi8q62t82e.cloudfront.net/';
                                    $movieImageUrl = $noImage;
                                    if (!empty($movie['poster_dir'])) {
                                        $movieImageUrl = $movieCdn . ltrim(h($movie['poster_dir']), '/');
                                    } elseif (!empty($movie['poster'])) {
                                        $movieImageUrl = h($movie['poster']);
                                    }
                                    ?>
                                    <a href="<?= $movieUrl ?>" target="_blank" rel="noopener" class="search-result-card">
                                        <img src="<?= $movieImageUrl ?>" alt="<?= h($movieTitle) ?>" class="search-result-image" loading="lazy" decoding="async" onerror="this.onerror=null;this.src='<?= $noImage ?>';">
                                        <div class="search-result-body">
                                            <h3 class="search-result-title"><?= h($movieTitle) ?></h3>
                                            <div class="search-result-tags">
                                                <?php if (!empty($movieDate)): ?>
                                                    <span class="search-result-tag"><i class="fas fa-calendar"></i> <?= h($movieDate) ?></span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if ($pagination['total'] > 0): ?>
                        <nav class="search-pagination" aria-label="Search results pages">
                            <?php if ($pagination['page'] > 1): ?>
                                <a href="<?= $this->Url->build([
                                    'controller' => 'Search',
                                    'action' => 'search',
                                    '?' => array_merge($paginationQuery, ['page' => $pagination['page'] - 1]),
                                ]) ?>">← Previous</a>
                            <?php else: ?>
                                <span class="disabled" aria-disabled="true">← Previous</span>
                            <?php endif; ?>

                            <?php
                            $visiblePageCount = min(5, $pagination['pageCount']);
                            $firstVisiblePage = max(
                                1,
                                min(
                                    $pagination['page'] - 2,
                                    $pagination['pageCount'] - $visiblePageCount + 1
                                )
                            );
                            $lastVisiblePage = $firstVisiblePage + $visiblePageCount - 1;
                            ?>
                            <?php for ($pageNumber = $firstVisiblePage; $pageNumber <= $lastVisiblePage; $pageNumber++): ?>
                                <?php if ($pageNumber === $pagination['page']): ?>
                                    <span class="current" aria-current="page"><?= $pageNumber ?></span>
                                <?php else: ?>
                                    <a href="<?= $this->Url->build([
                                        'controller' => 'Search',
                                        'action' => 'search',
                                        '?' => array_merge($paginationQuery, ['page' => $pageNumber]),
                                    ]) ?>"><?= $pageNumber ?></a>
                                <?php endif; ?>
                            <?php endfor; ?>

                            <?php if ($pagination['page'] < $pagination['pageCount']): ?>
                                <a href="<?= $this->Url->build([
                                    'controller' => 'Search',
                                    'action' => 'search',
                                    '?' => array_merge($paginationQuery, ['page' => $pagination['page'] + 1]),
                                ]) ?>">Next →</a>
                            <?php else: ?>
                                <span class="disabled" aria-disabled="true">Next →</span>
                            <?php endif; ?>
                        </nav>
                        <p class="search-pagination-info">
                            Showing <?= $pagination['start'] ?>–<?= $pagination['end'] ?>
                            of <?= $pagination['total'] ?> results
                            (Page <?= $pagination['page'] ?> of <?= $pagination['pageCount'] ?>)
                        </p>
                    <?php endif; ?>

                <?php endif; ?>

            </div>
        </div>
    </div>
</div>
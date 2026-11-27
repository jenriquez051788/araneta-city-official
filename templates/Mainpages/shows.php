<?php
/**
 * Shows / Events Page
 * Displays upcoming events from Smart Araneta Coliseum + New Frontier Theater
 * (TicketNet JSON feeds), with client-side category filtering.
 *
 * @var \App\View\AppView $this
 * @var array $events
 */

$eventsList = !empty($events) && is_array($events) ? $events : [];

// Collect distinct categories for the category filter pills
$categories = [];
// Collect distinct venues for the venue filter pills
$venues = [];
foreach ($eventsList as $ev) {
    $c = trim($ev['category'] ?? '');
    if ($c !== '' && !in_array($c, $categories, true)) {
        $categories[] = $c;
    }
    $v = trim($ev['venue_name'] ?? '');
    if ($v !== '' && !in_array($v, $venues, true)) {
        $venues[] = $v;
    }
}
?>

<style>
    .shows-page {
        background: #fff;
        padding: 46px 0 70px;
    }

    .shows-wrap {
        width: 92%;
        max-width: 1200px;
        margin: 0 auto;
    }

    /* Header */
    .shows-head {
        margin-bottom: 40px;
    }

    .shows-title {
        font-size: 44px;
        font-weight: 800;
        color: #1a1a1a;
        margin: 0 0 10px;
        letter-spacing: -0.5px;
        line-height: 1.05;
        text-align: left;
    }

    .shows-subtitle {
        font-size: 16px;
        color: #777;
        margin: 0;
        max-width: 460px;
        line-height: 1.5;
        text-align: left;
    }

    /* Filter bar */
    .shows-filter-bar {
        display: flex;
        flex-wrap: wrap;
        align-items: flex-end;
        gap: 14px;
        background: #fff;
        border: 1px solid #eee;
        border-radius: 14px;
        padding: 22px;
        margin-bottom: 40px;
    }

    .shows-filter-field {
        display: flex;
        flex-direction: column;
        flex: 1 1 0px;
    }

    .shows-filter-field label {
        font-size: 11.5px;
        font-weight: 700;
        letter-spacing: 0.03em;
        text-transform: uppercase;
        color: #999;
        margin-bottom: 8px;
        text-align: left;
    }

    .shows-filter-field input,
    .shows-filter-field select {
        height: 44px;
        border: 1px solid #eee;
        border-radius: 10px;
        padding: 0 14px;
        font-size: 14px;
        color: #1a1a1a;
        background: #fff;
        font-family: inherit;
    }

    .shows-filter-field input:focus,
    .shows-filter-field select:focus {
        outline: none;
        border-color: #00afab;
    }

    .shows-filter-btn {
        height: 44px;
        padding: 0 26px;
        border: 0;
        border-radius: 999px;
        background: #93358d;
        color: #fff;
        font-weight: 600;
        font-size: 14px;
        cursor: pointer;
        transition: background 0.2s ease;
        flex-shrink: 0;
    }

    .shows-filter-btn:hover {
        background: #7c2b76;
    }

    .shows-filter-field:nth-child(1) {
        flex: 2 1 0px;
    }

    /* Grid */
    .shows-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 26px;
    }

    .show-card {
        display: flex;
        flex-direction: column;
        text-decoration: none;
    }

    .show-poster {
        position: relative;
        width: 100%;
        aspect-ratio: 3 / 4;
        border-radius: 10px;
        overflow: hidden;
        background: linear-gradient(160deg, #6b2d7b 0%, #2a1030 100%);
        margin-bottom: 14px;
    }

    .show-poster img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s ease;
    }

    .show-card:hover .show-poster img {
        transform: scale(1.05);
    }

    /* Category badge (top-left) */
    .show-badge {
        position: absolute;
        top: 14px;
        left: 14px;
        background: #fff;
        color: #1a1a1a;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 5px 11px;
        border-radius: 20px;
        z-index: 2;
    }

    /* Date chip (bottom-left) */
    .show-date {
        position: absolute;
        bottom: 14px;
        left: 14px;
        background: rgba(15, 15, 20, 0.9);
        color: #fff;
        border-radius: 8px;
        padding: 7px 12px;
        text-align: center;
        line-height: 1;
        z-index: 2;
    }

    .show-date-month {
        display: block;
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
        opacity: 0.85;
        margin-bottom: 3px;
    }

    .show-date-day {
        display: block;
        font-size: 18px;
        font-weight: 800;
    }

    .show-info-title {
        font-size: 16px;
        font-weight: 700;
        color: #1a1a1a;
        margin: 0 0 6px;
        line-height: 1.3;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        text-align: left;
    }

    .show-info-venue {
        font-size: 13px;
        color: #888;
        margin: 0 0 10px;
        line-height: 1.4;
        text-align: left;
    }

    .show-tickets {
        font-size: 14px;
        font-weight: 700;
        color: #00afab;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        margin-top: auto;
        transition: gap 0.2s ease;
    }

    .show-card:hover .show-tickets {
        gap: 10px;
    }

    .shows-empty {
        text-align: center;
        padding: 60px 20px;
        color: #888;
        font-size: 16px;
    }

    .show-card.hidden {
        display: none;
    }

    /* Responsive */
    @media (max-width: 991px) {
        .shows-grid {
            grid-template-columns: repeat(3, 1fr);
        }

        .shows-title {
            font-size: 36px;
        }

        .shows-filter-bar {
            flex-direction: column;
            align-items: stretch;
        }

        .shows-filter-btn {
            align-self: stretch;
        }

        .shows-filter-field:nth-child(1) {
            flex: 1 1 100%;
        }
    }

    @media (max-width: 700px) {
        .shows-grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 18px;
        }

        .shows-title {
            font-size: 30px;
        }
    }

    @media (max-width: 420px) {
        .shows-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div id="wrapper">
    <div class="content">
        <div class="shows-page">
            <div class="shows-wrap">
                <!-- Header -->
                <div class="shows-head">
                    <h1 class="shows-title">Upcoming Events</h1>
                    <p class="shows-subtitle">Concerts, shows, and gatherings happening across Araneta City.</p>
                </div>

                <?php if (empty($eventsList)): ?>
                    <div class="shows-empty">
                        <p>No upcoming events right now. Please check back soon.</p>
                    </div>
                <?php else: ?>
                    <!-- Filter Bar -->
                    <div class="shows-filter-bar">
                        <div class="shows-filter-field">
                            <label>Search Events</label>
                            <input type="text" id="searchInput" placeholder="Search by event name..." />
                        </div>
                        <div class="shows-filter-field">
                            <label>Category</label>
                            <select id="categorySelect">
                                <option value="">All Categories</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?= h(strtolower($cat)) ?>"><?= h($cat) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="shows-filter-field">
                            <label>Venue</label>
                            <select id="venueSelect">
                                <option value="">All Venues</option>
                                <?php foreach ($venues as $ven): ?>
                                    <option value="<?= h(strtolower($ven)) ?>"><?= h($ven) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <button type="button" class="shows-filter-btn" id="resetBtn"><i class="fas fa-redo"></i> Reset</button>
                    </div>

                    <!-- Grid -->
                    <div class="shows-grid" id="showsGrid">
                        <?php foreach ($eventsList as $ev): ?>
                            <?php
                            $evTitle = h($ev['title'] ?? 'Event');
                            $evVenue = h($ev['venue'] ?? '');
                            $evVenueName = trim($ev['venue_name'] ?? '');
                            $evCategory = trim($ev['category'] ?? '');
                            $evLink = !empty($ev['detailPage']) ? h($ev['detailPage']) : '#';

                            // Poster: build from the public CloudFront CDN + poster_dir.
                            // (The raw S3 "poster" URL is access-restricted / 403, but the
                            //  same file is served publicly via the CloudFront CDN.)
                            $evCdn = 'https://dohyi8q62t82e.cloudfront.net/';
                            if (!empty($ev['poster_dir'])) {
                                $evPoster = $evCdn . ltrim(h($ev['poster_dir']), '/');
                            } elseif (!empty($ev['poster'])) {
                                $evPoster = h($ev['poster']);
                            } else {
                                $evPoster = '';
                            }

                            // Parse the event date for the badge (month + day)
                            $rawDate = $ev['dateofevent'] ?? $ev['displaydate'] ?? '';
                            $ts = strtotime($rawDate);
                            $badgeMonth = $ts ? strtoupper(date('M', $ts)) : '';
                            $badgeDay = $ts ? date('d', $ts) : '';
                            ?>
                            <a href="<?= $evLink ?>" target="_blank" rel="noopener" class="show-card"
                                data-title="<?= h($evTitle) ?>"
                                data-category="<?= h(strtolower($evCategory)) ?>"
                                data-venue="<?= h(strtolower($evVenueName)) ?>">
                                <div class="show-poster">
                                    <?php if (!empty($evPoster)): ?>
                                        <img src="<?= $evPoster ?>" alt="<?= $evTitle ?>" loading="lazy"
                                            onerror="this.style.display='none';">
                                    <?php endif; ?>
                                    <?php if (!empty($badgeDay)): ?>
                                        <span class="show-date">
                                            <span class="show-date-month"><?= $badgeMonth ?></span>
                                            <span class="show-date-day"><?= $badgeDay ?></span>
                                        </span>
                                    <?php endif; ?>
                                </div>
                                <h3 class="show-info-title"><?= $evTitle ?></h3>
                                <?php if (!empty($evVenue) || !empty($rawDate)): ?>
                                    <p class="show-info-venue">
                                        <?= $evVenue ?><?php if (!empty($evVenue) && !empty($rawDate)): ?> &middot; <?php endif; ?><?= h($rawDate) ?>
                                    </p>
                                <?php endif; ?>
                                <span class="show-tickets">Get Tickets &rarr;</span>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="limit-box fl-wrap"></div>
    </div>
</div>

<script>
    (function () {
        'use strict';

        function init() {
            const cards = document.querySelectorAll('.show-card');
            if (!cards.length) return;

            const searchInput = document.getElementById('searchInput');
            const categorySelect = document.getElementById('categorySelect');
            const venueSelect = document.getElementById('venueSelect');
            const resetBtn = document.getElementById('resetBtn');

            let searchQuery = '';
            let activeCategory = '';
            let activeVenue = '';

            function applyFilters() {
                const query = searchQuery.toLowerCase();
                let visibleCount = 0;

                cards.forEach(function (card) {
                    const title = (card.getAttribute('data-title') || '').toLowerCase();
                    const category = (card.getAttribute('data-category') || '').toLowerCase();
                    const venue = (card.getAttribute('data-venue') || '').toLowerCase();

                    const matchQuery = query === '' || title.includes(query);
                    const matchCategory = activeCategory === '' || category === activeCategory;
                    const matchVenue = activeVenue === '' || venue === activeVenue;

                    if (matchQuery && matchCategory && matchVenue) {
                        card.classList.remove('hidden');
                        visibleCount++;
                    } else {
                        card.classList.add('hidden');
                    }
                });

                // Show/hide empty message
                const grid = document.getElementById('showsGrid');
                if (visibleCount === 0 && !grid.querySelector('.shows-empty')) {
                    const emptyDiv = document.createElement('div');
                    emptyDiv.className = 'shows-empty';
                    emptyDiv.innerHTML = '<p>No events found matching your filters.</p>';
                    grid.parentElement.insertBefore(emptyDiv, grid);
                } else if (visibleCount > 0 && grid.parentElement.querySelector('.shows-empty')) {
                    const empty = grid.parentElement.querySelector('.shows-empty');
                    if (empty) empty.remove();
                }
            }

            // Search input
            if (searchInput) {
                searchInput.addEventListener('input', function () {
                    searchQuery = this.value;
                    applyFilters();
                });
            }

            // Category select
            if (categorySelect) {
                categorySelect.addEventListener('change', function () {
                    activeCategory = this.value;
                    applyFilters();
                });
            }

            // Venue select
            if (venueSelect) {
                venueSelect.addEventListener('change', function () {
                    activeVenue = this.value;
                    applyFilters();
                });
            }

            // Reset button
            if (resetBtn) {
                resetBtn.addEventListener('click', function () {
                    searchQuery = '';
                    activeCategory = '';
                    activeVenue = '';
                    if (searchInput) searchInput.value = '';
                    if (categorySelect) categorySelect.value = '';
                    if (venueSelect) venueSelect.value = '';
                    applyFilters();
                });
            }
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', init);
        } else {
            init();
        }
    })();
</script>

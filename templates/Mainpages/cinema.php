<?php
/**
 * Cinema Page
 * Movies now showing / upcoming at Gateway Cineplex 18
 * (TicketNet movies JSON feed).
 *
 * @var \App\View\AppView $this
 * @var array $movies
 */

$moviesList = !empty($movies) && is_array($movies) ? $movies : [];
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
        max-width: 480px;
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
        text-align: left;
    }

    .show-poster {
        position: relative;
        width: 100%;
        aspect-ratio: 2 / 3;
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
    }

    .show-info-venue {
        font-size: 13px;
        color: #888;
        margin: 0 0 10px;
        line-height: 1.4;
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
        text-align: left;
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
                    <h1 class="shows-title">Gateway Cineplex 18</h1>
                    <p class="shows-subtitle">Now showing and coming soon at Gateway Cineplex 18.</p>
                </div>

                <?php if (empty($moviesList)): ?>
                    <div class="shows-empty">
                        <p>No movies showing right now. Please check back soon.</p>
                    </div>
                <?php else: ?>
                    <!-- Filter Bar -->
                    <div class="shows-filter-bar">
                        <div class="shows-filter-field">
                            <label>Search Movies</label>
                            <input type="text" id="searchInput" placeholder="Search by movie name..." />
                        </div>
                        <button type="button" class="shows-filter-btn" id="resetBtn"><i class="fas fa-redo"></i> Reset</button>
                    </div>

                    <!-- Grid -->
                    <div class="shows-grid" id="showsGrid">
                        <?php foreach ($moviesList as $mv): ?>
                            <?php
                            $mvTitle = h($mv['title'] ?? 'Movie');
                            $mvVenue = h($mv['venue'] ?? '');
                            $mvLink = !empty($mv['detailPage']) ? h($mv['detailPage']) : '#';

                            // Poster via public CloudFront CDN + poster_dir (raw S3 URL is 403).
                            $mvCdn = 'https://dohyi8q62t82e.cloudfront.net/';
                            if (!empty($mv['poster_dir'])) {
                                $mvPoster = $mvCdn . ltrim(h($mv['poster_dir']), '/');
                            } elseif (!empty($mv['poster'])) {
                                $mvPoster = h($mv['poster']);
                            } else {
                                $mvPoster = '';
                            }

                            // Prefer the precise schedule date for the badge, else the display date.
                            $rawDate = $mv['_matchingData']['Eventschedules']['eventdate']
                                ?? ($mv['dateofevent'] ?? $mv['displaydate'] ?? '');
                            $ts = strtotime($rawDate);
                            $badgeMonth = $ts ? strtoupper(date('M', $ts)) : '';
                            $badgeDay = $ts ? date('d', $ts) : '';
                            $displayDate = $mv['displaydate'] ?? $mv['dateofevent'] ?? '';
                            ?>
                            <a href="<?= $mvLink ?>" target="_blank" rel="noopener" class="show-card"
                                data-title="<?= h($mvTitle) ?>">
                                <div class="show-poster">
                                    <?php if (!empty($mvPoster)): ?>
                                        <img src="<?= $mvPoster ?>" alt="<?= $mvTitle ?>" loading="lazy"
                                            onerror="this.style.display='none';">
                                    <?php endif; ?>
                                    <?php if (!empty($badgeDay)): ?>
                                        <span class="show-date">
                                            <span class="show-date-month"><?= $badgeMonth ?></span>
                                            <span class="show-date-day"><?= $badgeDay ?></span>
                                        </span>
                                    <?php endif; ?>
                                </div>
                                <h3 class="show-info-title"><?= $mvTitle ?></h3>
                                <?php if (!empty($mvVenue) || !empty($displayDate)): ?>
                                    <p class="show-info-venue">
                                        <?= $mvVenue ?><?php if (!empty($mvVenue) && !empty($displayDate)): ?> &middot; <?php endif; ?><?= h($displayDate) ?>
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
            const resetBtn = document.getElementById('resetBtn');

            let searchQuery = '';

            function applyFilters() {
                const query = searchQuery.toLowerCase();
                let visibleCount = 0;

                cards.forEach(function (card) {
                    const title = (card.getAttribute('data-title') || '').toLowerCase();

                    const matchQuery = query === '' || title.includes(query);

                    if (matchQuery) {
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
                    emptyDiv.innerHTML = '<p>No movies found matching your search.</p>';
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

            // Reset button
            if (resetBtn) {
                resetBtn.addEventListener('click', function () {
                    searchQuery = '';
                    if (searchInput) searchInput.value = '';
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

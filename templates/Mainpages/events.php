<?php
/**
 * Events Calendar Page
 * Displays events happening at Araneta City with Grid / List / Calendar views
 * and a client-side filter bar (Event Type, Venue, Start Date, End Date).
 *
 * @var \App\View\AppView $this
 * @var array $events
 * @var array $highlights
 * @var string $filterType
 */

$baseUrl = rtrim((string) env('API_BASE_URL', 'https://aci-cms.aranetacity.com'), '/');

/**
 * Resolve an event image URL from the various shapes the API may return.
 */
$resolveImage = function (array $event) use ($baseUrl) {
    $image = $event['img_url'] ?? '';
    if (empty($image)) {
        $img = $event['img'] ?? '';
        $imgDir = $event['img_dir'] ?? '';
        if (!empty($img) && !empty($imgDir)) {
            $image = "{$baseUrl}/files/events/{$imgDir}/{$img}";
        }
    } elseif (strpos($image, 'http') !== 0) {
        $image = $baseUrl . '/' . ltrim($image, '/');
    }

    return $image;
};

/**
 * Best-effort extraction of a machine-sortable date (Y-m-d) from an event.
 * Falls back to parsing display_date. Returns '' when nothing is parseable.
 */
$isoDate = function (array $event) {
    foreach (['event_date', 'start_date', 'date', 'display_date'] as $key) {
        if (!empty($event[$key])) {
            $ts = strtotime((string) $event[$key]);
            if ($ts !== false) {
                return date('Y-m-d', $ts);
            }
        }
    }

    return '';
};

/**
 * Extract the event's date RANGE (start + end) as Y-m-d strings so the filter
 * can match multi-day and ongoing events by overlap, not just a single date.
 *
 * Prefers the explicit startdate/enddate fields from the API. Falls back to
 * the single parsed date for both ends when a range isn't provided, so a
 * single-day event still behaves correctly.
 */
$isoRange = function (array $event) use ($isoDate) {
    $start = '';
    $end = '';

    if (!empty($event['startdate'])) {
        $ts = strtotime((string) $event['startdate']);
        if ($ts !== false) {
            $start = date('Y-m-d', $ts);
        }
    }
    if (!empty($event['enddate'])) {
        $ts = strtotime((string) $event['enddate']);
        if ($ts !== false) {
            $end = date('Y-m-d', $ts);
        }
    }

    // Fall back to the single date when either end is missing.
    $single = $isoDate($event);
    if ($start === '') {
        $start = $single;
    }
    if ($end === '') {
        // If we have a start but no end, treat it as a single-day event.
        $end = ($start !== '') ? $start : $single;
    }

    // Sanity guard against bad data (e.g. a typo'd end year like 2029 instead
    // of 2026, which would smear the event across years of the calendar).
    if ($start !== '' && $end !== '') {
        $startTs = strtotime($start);
        $endTs = strtotime($end);

        // 1) End before start -> invalid range; collapse to a single day.
        if ($endTs < $startTs) {
            $end = $start;
        } elseif (($endTs - $startTs) > (366 * 86400)) {
            // 2) Range longer than ~1 year is almost certainly a data error.
            // Prefer the end date implied by the display_date text if we can
            // parse a sane one from it; otherwise cap the end to the start.
            $recovered = '';
            if (!empty($event['display_date'])) {
                // Grab the last date-looking chunk from the display text,
                // e.g. "September 14 - 19, 2026" -> "19, 2026".
                $dd = (string) $event['display_date'];
                $ts = strtotime($dd);
                // strtotime on a range string returns the FIRST date; try to
                // build the end from the trailing day + the display year.
                // Use the LAST 4-digit year in the text (the end year for
                // cross-year ranges like "Dec 28, 2026 - Jan 3, 2027").
                if (preg_match_all('/(\d{4})/', $dd, $ym) && $ts !== false) {
                    $year = (int) end($ym[1]);
                    // Re-anchor the parsed end to the display year.
                    $endParts = getdate($endTs);
                    $reTs = mktime(23, 59, 0, $endParts['mon'], $endParts['mday'], $year);
                    if ($reTs !== false && ($reTs - $startTs) <= (366 * 86400) && $reTs >= $startTs) {
                        $recovered = date('Y-m-d', $reTs);
                    }
                }
            }
            $end = $recovered !== '' ? $recovered : $start;
        }
    }

    return ['start' => $start, 'end' => $end];
};

// Normalise events into a predictable shape for all three views.
$items = [];
$eventTypes = [];
$venues = [];

foreach (($events ?? []) as $event) {
    $title = $event['event_title'] ?? ($event['title'] ?? 'Untitled Event');
    $category = $event['category'] ?? '';
    $venue = $event['venue'] ?? '';
    $displayDate = $event['display_date'] ?? '';
    $time = $event['event_time'] ?? ($event['display_time'] ?? ($event['time'] ?? ''));
    $descRaw = $event['event_desc'] ?? ($event['description'] ?? ($event['event_content'] ?? ($event['content'] ?? '')));
    $desc = trim(strip_tags((string) $descRaw));
    if (strlen($desc) > 120) {
        $desc = substr($desc, 0, 120) . '...';
    }

    if ($category !== '') {
        $eventTypes[$category] = true;
    }
    if ($venue !== '') {
        $venues[$venue] = true;
    }

    $slug = $event['slug'] ?? '';
    $eventUrl = !empty($slug) ? $this->Url->build('/event/' . h($slug)) : '#';

    $range = $isoRange($event);

    $items[] = [
        'title' => $title,
        'category' => $category,
        'venue' => $venue,
        'display_date' => $displayDate,
        'time' => $time,
        'desc' => $desc,
        'image' => $resolveImage($event),
        'iso' => $isoDate($event),
        'iso_start' => $range['start'],
        'iso_end' => $range['end'],
        'id' => $event['id'] ?? ($event['event_id'] ?? ''),
        'url' => $eventUrl,
    ];
}

ksort($eventTypes);
ksort($venues);

$noImage = $this->Url->image('no-image.jpg');
?>

<!-- Page Header (plain style, no parallax banner) -->
<section class="aci-page-header">
    <div class="container">
        <h1 class="aci-page-title">Upcoming Events</h1>
        <p class="aci-page-subtitle">Concerts, shows, and gatherings happening across Araneta City.</p>
    </div>
</section>
<!-- Page Header End -->

<section class="aci-eventspage">
    <div class="container">
        <div class="aci-events">

            <!-- View toggle -->
            <div class="aci-events-topbar">
                <div class="aci-view-toggle" role="tablist" aria-label="Event view">
                    <button type="button" class="aci-view-btn" data-view="grid" aria-pressed="false">
                        <i class="fas fa-th-large"></i> Grid
                    </button>
                    <button type="button" class="aci-view-btn is-active" data-view="list" aria-pressed="true">
                        <i class="fas fa-list"></i> List
                    </button>
                    <button type="button" class="aci-view-btn" data-view="calendar" aria-pressed="false">
                        <i class="fas fa-calendar-days"></i> Cal
                    </button>
                </div>
            </div>

            <!-- Filter bar -->
            <form class="aci-filter-bar" id="aciEventFilter" onsubmit="return false;">
                <div class="aci-filter-field">
                    <label>Search Events</label>
                    <input type="text" id="aciFilterSearch" placeholder="Search by title...">
                </div>
                <div class="aci-filter-field">
                    <label>Event Type</label>
                    <select id="aciFilterType">
                        <option value="">All Event Types</option>
                        <?php foreach (array_keys($eventTypes) as $type): ?>
                            <option value="<?= h($type) ?>"><?= h($type) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="aci-filter-field">
                    <label>Venue</label>
                    <select id="aciFilterVenue">
                        <option value="">All Venues</option>
                        <?php foreach (array_keys($venues) as $venue): ?>
                            <option value="<?= h($venue) ?>"><?= h($venue) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="aci-filter-field">
                    <label>Start Date</label>
                    <input type="date" id="aciFilterStart">
                </div>
                <div class="aci-filter-field">
                    <label>End Date</label>
                    <input type="date" id="aciFilterEnd">
                </div>
                <div class="aci-filter-actions">
                    <button type="button" class="aci-btn-filter" id="aciApplyFilter">Filter</button>
                    <button type="button" class="aci-btn-reset" id="aciResetFilter">Reset</button>
                </div>
            </form>

            <!-- Empty state (shown when a filter removes everything) -->
            <div class="aci-empty" id="aciEmpty" style="display:none;">
                <i class="fas fa-calendar-xmark"></i>
                <p>No events match your filters.</p>
            </div>

            <?php if (empty($items)): ?>
                <div class="aci-empty">
                    <i class="fas fa-calendar-xmark"></i>
                    <p>No events available at this time.</p>
                </div>
            <?php else: ?>

                <!-- ============ LIST VIEW ============ -->
                <div class="aci-list-view aci-view-panel" data-panel="list">
                    <?php foreach ($items as $item): ?>
                        <a href="<?= h($item['url']) ?>" class="aci-list-row aci-event" data-type="<?= h($item['category']) ?>"
                            data-venue="<?= h($item['venue']) ?>" data-date="<?= h($item['iso']) ?>"
                            data-start="<?= h($item['iso_start']) ?>" data-end="<?= h($item['iso_end']) ?>"
                            style="text-decoration: none; color: inherit; display: flex;">
                            <div class="aci-list-date">
                                <span class="aci-date-main"><?= h($item['display_date']) ?></span>
                                <?php if (!empty($item['time'])): ?>
                                    <span class="aci-date-time"><?= h($item['time']) ?></span>
                                <?php endif; ?>
                            </div>
                            <div class="aci-list-body">
                                <h3 class="aci-event-title"><?= h($item['title']) ?></h3>
                                <?php if (!empty($item['desc'])): ?>
                                    <p class="aci-event-desc"><?= h($item['desc']) ?></p>
                                <?php endif; ?>
                                <p class="aci-event-meta">
                                    <?php if (!empty($item['venue'])): ?>
                                        <span><i class="fas fa-map-marker-alt"></i> <?= h($item['venue']) ?></span>
                                    <?php endif; ?>
                                    <?php if (!empty($item['category'])): ?>
                                        <span><i class="fas fa-tag"></i> <?= h($item['category']) ?></span>
                                    <?php endif; ?>
                                </p>
                            </div>
                            <div class="aci-list-action">
                                <span class="aci-btn-more">More Info</span>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>

                <!-- ============ GRID VIEW ============ -->
                <div class="aci-grid-view aci-view-panel" data-panel="grid" style="display:none;">
                    <?php foreach ($items as $item): ?>
                        <a href="<?= h($item['url']) ?>" class="aci-grid-card aci-event" data-type="<?= h($item['category']) ?>"
                            data-venue="<?= h($item['venue']) ?>" data-date="<?= h($item['iso']) ?>"
                            data-start="<?= h($item['iso_start']) ?>" data-end="<?= h($item['iso_end']) ?>"
                            style="text-decoration: none; color: inherit;">
                            <div class="aci-grid-media">
                                <img src="<?= h(!empty($item['image']) ? $item['image'] : $noImage) ?>"
                                    alt="<?= h($item['title']) ?>" loading="lazy">
                                <?php if (!empty($item['category'])): ?>
                                    <span class="aci-grid-badge"><?= h($item['category']) ?></span>
                                <?php endif; ?>
                            </div>
                            <div class="aci-grid-body">
                                <span class="aci-grid-date"><i class="fas fa-calendar"></i>
                                    <?= h($item['display_date']) ?>        <?= !empty($item['time']) ? ' &middot; ' . h($item['time']) : '' ?></span>
                                <h3 class="aci-event-title"><?= h($item['title']) ?></h3>
                                <?php if (!empty($item['venue'])): ?>
                                    <p class="aci-event-meta"><i class="fas fa-map-marker-alt"></i> <?= h($item['venue']) ?></p>
                                <?php endif; ?>
                                <span class="aci-btn-more">More Info</span>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>

                <!-- ============ CALENDAR VIEW ============ -->
                <div class="aci-calendar-view aci-view-panel" data-panel="calendar" style="display:none;">
                    <div class="aci-cal-header">
                        <button type="button" class="aci-cal-nav" id="aciCalPrev"><i
                                class="fas fa-chevron-left"></i></button>
                        <h3 id="aciCalMonthLabel"></h3>
                        <button type="button" class="aci-cal-nav" id="aciCalNext"><i
                                class="fas fa-chevron-right"></i></button>
                    </div>
                    <!-- Ongoing / month-long events, listed once here instead of
                         being repeated in every single day cell below. -->
                    <div class="aci-cal-ongoing" id="aciCalOngoing" hidden></div>
                    <div class="aci-cal-weekdays">
                        <span data-short="S">Sun</span><span data-short="M">Mon</span><span data-short="T">Tue</span><span data-short="W">Wed</span><span data-short="T">Thu</span><span data-short="F">Fri</span><span data-short="S">Sat</span>
                    </div>
                    <div class="aci-cal-grid" id="aciCalGrid"></div>
                    <!-- Tapping a day with events lists them here (esp. useful
                         on mobile where the in-cell chips are hidden). -->
                    <div class="aci-cal-details" id="aciCalDetails" hidden></div>
                    <!-- Mobile agenda: shows only the days that have events,
                         as a scrollable list grouped by date. -->
                    <div class="aci-cal-agenda" id="aciCalAgenda"></div>
                </div>

            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Event data for the calendar view -->
<script>
    window.ACI_EVENTS = <?= json_encode(array_map(function ($i) {
        return [
            'title' => $i['title'],
            'venue' => $i['venue'],
            'category' => $i['category'],
            'time' => $i['time'],
            'display_date' => $i['display_date'],
            'iso' => $i['iso'],
            'iso_start' => $i['iso_start'],
            'iso_end' => $i['iso_end'],
            'url' => $i['url'],
        ];
    }, $items), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;
</script>

<style>
    section.aci-eventspage {
        padding-top: 0px;
    }

    /* ===== Araneta City Events - scoped styles ===== */
    .aci-events {
        padding: 10px 0 40px;
    }

    /* Plain page header (replaces parallax banner) */
    .aci-page-header {
        padding: 140px 0 22px;
        border-bottom: 1px solid #ececf1;
        margin-bottom: 30px;
        background: #fff;
    }

    .aci-page-title {
        font-size: 36px;
        font-weight: 800;
        color: #1c1c1c;
        margin: 0 0 8px;
    }

    .aci-page-subtitle {
        font-size: 15px;
        color: #777;
        margin: 0;
    }

    /* Topbar (now just holds the view toggle) */
    .aci-events-topbar {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        flex-wrap: wrap;
        gap: 16px;
        margin-bottom: 24px;
    }

    /* View toggle */
    .aci-view-toggle {
        display: inline-flex;
        border: 1px solid #e2e2e2;
        border-radius: 30px;
        overflow: hidden;
        background: #fff;
    }

    .aci-view-btn {
        border: 0;
        background: transparent;
        padding: 9px 18px;
        font-size: 13px;
        font-weight: 700;
        color: #555;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 7px;
        transition: all .2s ease;
    }

    .aci-view-btn i {
        font-size: 13px;
    }

    .aci-view-btn:hover {
        color: #00afab;
    }

    .aci-view-btn.is-active {
        background: #6B2D7B;
        color: #fff;
    }

    /* Filter bar */
    .aci-filter-bar {
        display: flex;
        flex-wrap: wrap;
        align-items: flex-end;
        gap: 16px;
        background: #f7f7f9;
        border: 1px solid #ececf1;
        border-radius: 12px;
        padding: 18px 20px;
        margin-bottom: 30px;
    }

    .aci-filter-field {
        display: flex;
        flex-direction: column;
        flex: 1 1 180px;
        min-width: 150px;
    }

    .aci-filter-field label {
        font-size: 11px;
        font-weight: 700;
        letter-spacing: .05em;
        text-transform: uppercase;
        color: #8a8a94;
        margin-bottom: 6px;
    }

    .aci-filter-field select,
    .aci-filter-field input {
        height: 44px;
        border: 1px solid #dcdce3;
        border-radius: 8px;
        padding: 0 12px;
        font-size: 14px;
        color: #333;
        background: #fff;
        width: 100%;
    }

    .aci-filter-field select:focus,
    .aci-filter-field input:focus {
        outline: none;
        border-color: #00afab;
        box-shadow: 0 0 0 3px rgba(0, 175, 171, .12);
    }

    .aci-filter-actions {
        display: flex;
        gap: 10px;
    }

    .aci-btn-filter {
        height: 44px;
        padding: 0 26px;
        border: 0;
        border-radius: 8px;
        cursor: pointer;
        background: #00afab;
        color: #fff;
        font-weight: 700;
        font-size: 14px;
        transition: background .2s ease;
    }

    .aci-btn-filter:hover {
        background: #009591;
    }

    .aci-btn-reset {
        height: 44px;
        padding: 0 18px;
        border: 1px solid #dcdce3;
        border-radius: 8px;
        cursor: pointer;
        background: #fff;
        color: #666;
        font-weight: 700;
        font-size: 14px;
        transition: all .2s ease;
    }

    .aci-btn-reset:hover {
        border-color: #6B2D7B;
        color: #6B2D7B;
    }

    /* Shared button */
    .aci-btn-more {
        display: inline-block;
        padding: 10px 20px;
        border: 1px solid #6B2D7B;
        border-radius: 30px;
        color: #6B2D7B;
        font-size: 12px;
        font-weight: 700;
        letter-spacing: .04em;
        text-transform: uppercase;
        text-decoration: none;
        white-space: nowrap;
        transition: all .2s ease;
    }

    .aci-btn-more:hover {
        background: #6B2D7B;
        color: #fff;
    }

    /* Event title */
    .aci-event-title {
        font-size: 19px;
        font-weight: 800;
        color: #1c1c1c;
        margin: 0 0 6px;
        text-align: left;
    }

    /* ===== List view ===== */
    .aci-list-view {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    .aci-list-row {
        display: flex;
        align-items: center;
        gap: 22px;
        background: #fff;
        border: 1px solid #ececf1;
        border-radius: 12px;
        padding: 22px 26px;
        transition: box-shadow .2s ease, transform .2s ease;
    }

    .aci-list-row:hover {
        box-shadow: 0 8px 24px rgba(0, 0, 0, .07);
        transform: translateY(-2px);
    }

    .aci-list-date {
        flex: 0 0 120px;
        display: flex;
        flex-direction: column;
        gap: 2px;
        border-right: 1px solid #eee;
        padding-right: 18px;
    }

    .aci-date-main {
        font-size: 13px;
        font-weight: 800;
        color: #00afab;
        text-transform: uppercase;
        line-height: 1.35;
    }

    .aci-date-time {
        font-size: 12px;
        color: #999;
    }

    .aci-list-body {
        flex: 1 1 auto;
    }

    .aci-event-desc {
        font-size: 14px;
        color: #6B2D7B;
        margin: 0 0 8px;
        text-align: left;
    }

    .aci-event-meta {
        font-size: 13px;
        color: #777;
        margin: 0;
        display: flex;
        flex-wrap: wrap;
        gap: 5px;
        text-align: left;
    }

    .aci-event-meta i {
        color: #00afab;
        margin-right: 4px;
        margin-top: 4px;
    }

    .aci-list-action {
        flex: 0 0 auto;
    }

    /* ===== Grid view ===== */
    .aci-grid-view {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 24px;
    }

    .aci-grid-card {
        background: #fff;
        border: 1px solid #ececf1;
        border-radius: 14px;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        transition: box-shadow .2s ease, transform .2s ease;
    }

    .aci-grid-card:hover {
        box-shadow: 0 10px 28px rgba(0, 0, 0, .09);
        transform: translateY(-3px);
    }

    .aci-grid-media {
        position: relative;
        height: 240px;
        overflow: hidden;
    }

    .aci-grid-media img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .aci-grid-badge {
        position: absolute;
        top: 12px;
        left: 12px;
        background: #6B2D7B;
        color: #fff;
        font-size: 11px;
        font-weight: 700;
        padding: 5px 12px;
        border-radius: 20px;
    }

    .aci-grid-body {
        padding: 18px 20px;
        display: flex;
        flex-direction: column;
        gap: 8px;
        flex: 1;
    }

    .aci-grid-date {
        font-size: 12px;
        font-weight: 700;
        color: #00afab;
        text-transform: uppercase;
        text-align: left;
    }

    .aci-grid-body .aci-btn-more {
        align-self: flex-start;
        margin-top: auto;
    }

    /* ===== Calendar view ===== */
    .aci-calendar-view {
        background: #fff;
        border: 1px solid #ececf1;
        border-radius: 14px;
        padding: 22px;
    }

    .aci-cal-header {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 24px;
        margin-bottom: 18px;
    }

    .aci-cal-header h3 {
        margin: 0;
        font-size: 20px;
        font-weight: 800;
        color: #1c1c1c;
        min-width: 220px;
        text-align: center;
    }

    .aci-cal-nav {
        flex: 0 0 auto;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        border: 1px solid #e2e2e2;
        background: #fff;
        color: #00afab;
        cursor: pointer;
        transition: all .2s ease;
    }

    .aci-cal-nav:hover {
        background: #00afab;
        color: #fff;
        border-color: #00afab;
    }

    .aci-cal-weekdays {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        gap: 6px;
        margin-bottom: 6px;
    }

    .aci-cal-weekdays span {
        text-align: center;
        font-size: 12px;
        font-weight: 700;
        color: #999;
        text-transform: uppercase;
        padding: 6px 0;
    }

    .aci-cal-grid {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        gap: 6px;
    }

    .aci-cal-cell {
        /* Fixed height keeps every box identical whether or not it has events.
           Overflow is handled inside the events list below. */
        height: 120px;
        border: 1px solid #f0f0f3;
        border-radius: 8px;
        padding: 6px;
        background: #fbfbfd;
        display: flex;
        flex-direction: column;
        gap: 4px;
        overflow: hidden;
    }

    .aci-cal-cell.is-empty {
        background: transparent;
        border-color: transparent;
    }

    .aci-cal-cell.is-today {
        border-color: #00afab;
        box-shadow: inset 0 0 0 1px #00afab;
    }

    .aci-cal-daynum {
        font-size: 12px;
        font-weight: 700;
        color: #666;
        flex-shrink: 0;
    }

    .aci-cal-cell.is-today .aci-cal-daynum {
        color: #00afab;
    }

    /* Scrollable list of event chips so overflowing events don't stretch the
       cell. The cell height stays fixed; extra events scroll within. */
    .aci-cal-events {
        display: flex;
        flex-direction: column;
        gap: 4px;
        overflow-y: auto;
        flex: 1 1 auto;
        min-height: 0;
    }

    .aci-cal-events::-webkit-scrollbar {
        width: 4px;
    }

    .aci-cal-events::-webkit-scrollbar-thumb {
        background: #d9d9e0;
        border-radius: 4px;
    }

    .aci-cal-event {
        background: #6B2D7B;
        color: #fff;
        font-size: 11px;
        font-weight: 600;
        line-height: 1.25;
        padding: 3px 6px;
        border-radius: 5px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        flex-shrink: 0;
    }

    .aci-cal-event:nth-child(3n) {
        background: #00afab;
    }

    .aci-cal-more {
        font-size: 10px;
        color: #999;
        font-weight: 700;
    }

    /* ===== "Ongoing this month" strip ===== */
    /* Month-long / recurring events are shown once here rather than repeated
       in every day cell, keeping the grid clean. */
    .aci-cal-ongoing {
        margin: 0 0 16px;
        padding: 14px 16px;
        background: #f2fbfa;
        border: 1px solid #d8f0ee;
        border-radius: 10px;
    }

    .aci-cal-ongoing-label {
        display: flex;
        align-items: center;
        gap: 7px;
        font-size: 12px;
        font-weight: 800;
        letter-spacing: 0.04em;
        text-transform: uppercase;
        color: #007e7b;
        margin: 0 0 10px;
    }

    .aci-cal-ongoing-label i {
        font-size: 12px;
    }

    .aci-cal-ongoing-list {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }

    .aci-cal-ongoing-item {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        text-decoration: none;
        background: #fff;
        border: 1px solid #cdeae7;
        border-radius: 999px;
        padding: 6px 14px 6px 12px;
        font-size: 13px;
        font-weight: 600;
        color: #1a1a1a;
        transition: border-color 0.2s ease, box-shadow 0.2s ease;
    }

    .aci-cal-ongoing-item:hover {
        border-color: #00afab;
        box-shadow: 0 2px 8px rgba(0, 175, 171, 0.15);
    }

    .aci-cal-ongoing-item .dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: #00afab;
        flex-shrink: 0;
    }

    .aci-cal-ongoing-item .aci-cal-ongoing-date {
        color: #888;
        font-weight: 500;
        font-size: 12px;
    }

    /* Tappable day cells (cells that have events). */
    .aci-cal-cell.has-events {
        cursor: pointer;
    }

    .aci-cal-cell.has-events:hover {
        border-color: #6B2D7B;
    }

    .aci-cal-cell.is-selected {
        border-color: #6B2D7B;
        box-shadow: inset 0 0 0 1px #6B2D7B;
    }

    /* Day-details panel: shows the event titles for the tapped day. */
    .aci-cal-details {
        margin-top: 14px;
        border: 1px solid #ece6f0;
        border-radius: 10px;
        background: #faf7fc;
        overflow: hidden;
    }

    .aci-cal-details-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 12px 16px;
        background: #6B2D7B;
        color: #fff;
        font-weight: 700;
        font-size: 14px;
    }

    .aci-cal-details-close {
        background: transparent;
        border: 0;
        color: #fff;
        font-size: 22px;
        line-height: 1;
        cursor: pointer;
        padding: 0 4px;
    }

    .aci-cal-details-list {
        list-style: none;
        margin: 0;
        padding: 6px;
    }

    .aci-cal-details-list li {
        border-radius: 8px;
    }

    .aci-cal-details-list li + li {
        margin-top: 4px;
    }

    .aci-cal-details-list a,
    .aci-cal-details-list li > span {
        display: flex;
        flex-direction: column;
        gap: 3px;
        padding: 10px 12px;
        text-decoration: none;
        color: inherit;
        border-radius: 8px;
    }

    .aci-cal-details-list a:hover {
        background: #f0e8f5;
    }

    .aci-cal-details-title {
        font-size: 14px;
        font-weight: 700;
        color: #1a1a1a;
    }

    .aci-cal-details-venue,
    .aci-cal-details-date {
        font-size: 12px;
        color: #777;
    }

    .aci-cal-details-venue i,
    .aci-cal-details-date i {
        color: #6B2D7B;
        margin-right: 4px;
    }

    /* ===== Mobile agenda list (days with events only) ===== */
    /* Hidden on desktop; the mobile media query flips it on. */
    .aci-cal-agenda {
        display: none;
        margin-top: 8px;
    }

    /* Each day is a row: date badge on the LEFT, event cards on the RIGHT. */
    .aci-agenda-day {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        margin-bottom: 18px;
    }

    .aci-agenda-daynum {
        flex: 0 0 auto;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 40px;
        height: 40px;
        background: #00afab;
        color: #fff;
        font-size: 16px;
        font-weight: 800;
        border-radius: 10px;
        margin-top: 2px;
    }

    .aci-agenda-daynum.is-today {
        background: #007e7b;
    }

    /* Right column holds the day's event cards, stacked. */
    .aci-agenda-events {
        flex: 1 1 auto;
        min-width: 0;
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .aci-agenda-card {
        display: block;
        text-decoration: none;
        color: inherit;
        background: #f2fbfa;
        border: 1px solid #d8f0ee;
        border-left: 4px solid #00afab;
        border-radius: 10px;
        padding: 14px 16px;
    }

    .aci-agenda-title {
        font-size: 15px;
        font-weight: 700;
        color: #1a1a1a;
        line-height: 1.3;
        margin: 0 0 6px;
        text-align: left;
    }

    .aci-agenda-meta {
        display: flex;
        flex-direction: column;
        gap: 3px;
        font-size: 12.5px;
        color: #777;
        text-align: left;
    }

    .aci-agenda-meta span i {
        color: #00afab;
        margin-right: 5px;
    }

    .aci-agenda-empty {
        text-align: center;
        padding: 40px 20px;
        color: #999;
        font-size: 15px;
    }

    /* Empty state */
    .aci-empty {
        text-align: center;
        padding: 60px 20px;
        color: #999;
    }

    .aci-empty i {
        font-size: 42px;
        color: #d9d9e0;
        margin-bottom: 14px;
        display: block;
    }

    .aci-empty p {
        font-size: 17px;
        margin: 0;
    }

    /* Responsive */
    @media (max-width: 992px) {
        .aci-grid-view {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 767px) {
        .aci-page-header {
            padding: 110px 0 18px;
        }

        .aci-page-title {
            font-size: 27px;
        }

        .aci-grid-view {
            grid-template-columns: 1fr;
        }

        .aci-list-row {
            flex-direction: column;
            align-items: flex-start;
            gap: 12px;
        }

        .aci-list-date {
            flex-basis: auto;
            border-right: 0;
            border-bottom: 1px solid #eee;
            padding: 0 0 10px;
            width: 100%;
            flex-direction: row;
            gap: 10px;
        }

        .aci-list-action {
            width: 100%;
        }

        .aci-list-action .aci-btn-more {
            display: block;
            text-align: center;
        }

        .aci-filter-field {
            flex-basis: 100%;
        }

        /* Calendar on mobile: replace the 7-column grid with an "agenda" list
           that only shows the days that actually have events. */
        .aci-cal-weekdays,
        .aci-cal-grid,
        .aci-cal-details {
            display: none !important;
        }

        .aci-cal-agenda {
            display: block;
        }

        /* Ongoing strip stays visible on mobile, just a touch tighter. */
        .aci-cal-ongoing {
            padding: 12px 14px;
        }

        .aci-cal-ongoing-item {
            font-size: 12.5px;
        }

        /* Header: keep arrows fully visible and spaced from the edges. */
        .aci-cal-header {
            justify-content: space-between;
            gap: 12px;
            padding: 0 4px;
        }

        .aci-cal-header h3 {
            min-width: 0;
            flex: 1 1 auto;
            font-size: 18px;
        }
    }
</style>

<script>
    (function () {
        var root = document.querySelector('.aci-events');
        if (!root) return;

        /* ---------- View switching ---------- */
        var viewBtns = root.querySelectorAll('.aci-view-btn');
        var panels = root.querySelectorAll('.aci-view-panel');

        function showView(view) {
            viewBtns.forEach(function (b) {
                var on = b.getAttribute('data-view') === view;
                b.classList.toggle('is-active', on);
                b.setAttribute('aria-pressed', on ? 'true' : 'false');
            });
            panels.forEach(function (p) {
                p.style.display = (p.getAttribute('data-panel') === view) ? '' : 'none';
            });
            if (view === 'calendar') renderCalendar();
        }

        viewBtns.forEach(function (b) {
            b.addEventListener('click', function () { showView(b.getAttribute('data-view')); });
        });

        /* ---------- Filtering (list + grid) ---------- */
        var fSearch = document.getElementById('aciFilterSearch');
        var fType = document.getElementById('aciFilterType');
        var fVenue = document.getElementById('aciFilterVenue');
        var fStart = document.getElementById('aciFilterStart');
        var fEnd = document.getElementById('aciFilterEnd');
        var emptyEl = document.getElementById('aciEmpty');

        function applyFilter() {
            var search = (fSearch.value || '').toLowerCase();
            var type = fType.value;
            var venue = fVenue.value;
            var start = fStart.value; // yyyy-mm-dd or ''
            var end = fEnd.value;
            var anyVisible = false;

            root.querySelectorAll('.aci-event').forEach(function (el) {
                var elType = el.getAttribute('data-type') || '';
                var elVenue = el.getAttribute('data-venue') || '';
                var elDate = el.getAttribute('data-date') || '';
                // Event date RANGE. Fall back to the single date when start/end
                // aren't present so older markup still filters sensibly.
                var elStart = el.getAttribute('data-start') || elDate;
                var elEnd = el.getAttribute('data-end') || elStart;
                var elTitle = el.querySelector('.aci-event-title')?.textContent || '';
                var elTitle2 = el.querySelector('.aci-list-body h3')?.textContent || '';
                var elTitle3 = el.querySelector('.aci-grid-body h3')?.textContent || '';
                var combinedTitle = (elTitle || elTitle2 || elTitle3).toLowerCase();

                var ok = true;

                // Search filter
                if (search && !combinedTitle.includes(search)) ok = false;

                // Type filter
                if (type && elType !== type) ok = false;

                // Venue filter
                if (venue && elVenue !== venue) ok = false;

                // Date RANGE OVERLAP filter.
                // An event matches the chosen window if the event's own date
                // range overlaps it. Overlap rule: eventStart <= filterEnd AND
                // eventEnd >= filterStart. This correctly includes multi-day
                // and ongoing events (e.g. Sep 1 - Oct 31 shows up when
                // filtering October), not just events that START in the window.
                if (elStart && elEnd) {
                    if (start && elEnd < start) ok = false;   // event ended before the window
                    if (end && elStart > end) ok = false;     // event starts after the window
                } else {
                    // No parseable date at all: only exclude if a date filter
                    // is active (undated events can't satisfy a date range).
                    if (start || end) ok = false;
                }

                el.style.display = ok ? '' : 'none';
                if (ok) anyVisible = true;
            });

            // Only surface the empty message when a card/list panel is active.
            var listVisible = root.querySelector('.aci-list-view').style.display !== 'none';
            var gridVisible = root.querySelector('.aci-grid-view').style.display !== 'none';
            emptyEl.style.display = (!anyVisible && (listVisible || gridVisible)) ? 'block' : 'none';
        }

        // Apply filter on button click
        document.getElementById('aciApplyFilter').addEventListener('click', applyFilter);

        // Apply filter on search input change (real-time)
        if (fSearch) {
            fSearch.addEventListener('keyup', applyFilter);
            fSearch.addEventListener('change', applyFilter);
        }

        // Reset filters
        document.getElementById('aciResetFilter').addEventListener('click', function () {
            fSearch.value = '';
            fType.value = '';
            fVenue.value = '';
            fStart.value = '';
            fEnd.value = '';
            applyFilter();
        });

        /* ---------- Calendar ---------- */
        var events = window.ACI_EVENTS || [];

        // An event counts as "ongoing" for a given month when it (a) actually
        // overlaps that month AND (b) is a long/month-spanning event (so
        // repeating it in every day cell would just be noise).
        function isOngoingInMonth(e, year, month0) {
            var s = e.iso_start || e.iso;
            var en = e.iso_end || e.iso_start || e.iso;
            if (!s || !en) return false;

            var startTs = Date.parse(s + 'T00:00:00');
            var endTs = Date.parse(en + 'T00:00:00');
            if (isNaN(startTs) || isNaN(endTs)) return false;

            var monthStart = new Date(year, month0, 1).getTime();
            var monthEnd = new Date(year, month0 + 1, 0).getTime();

            // (a) Must overlap the visible month. Without this, a long event
            // that ended in a previous month (e.g. an Oct 31 promo) would
            // wrongly appear in later months like November.
            var overlapsMonth = startTs <= monthEnd && endTs >= monthStart;
            if (!overlapsMonth) return false;

            // (b) Long span (>= 20 days) OR it brackets the whole month.
            var spanDays = (endTs - startTs) / 86400000;
            if (spanDays >= 20) return true;
            return startTs <= monthStart && endTs >= monthEnd;
        }

        // Events active on a given ISO day, EXCLUDING ongoing ones (those are
        // surfaced separately in the ongoing strip). Falls back to the single
        // `iso` date when a range isn't available.
        function eventsOnDay(iso, year, month0) {
            return events.filter(function (e) {
                if (isOngoingInMonth(e, year, month0)) return false;
                var s = e.iso_start || e.iso;
                var en = e.iso_end || e.iso_start || e.iso;
                if (!s && !en) return false;
                if (s && en) return iso >= s && iso <= en;
                return iso === (s || en);
            });
        }

        // The set of ongoing events for a given month (deduped).
        function ongoingEventsForMonth(year, month0) {
            return events.filter(function (e) {
                return isOngoingInMonth(e, year, month0);
            });
        }

        var today = new Date();
        var calYear = today.getFullYear();
        var calMonth = today.getMonth(); // 0-11
        var monthNames = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];

        function pad(n) { return (n < 10 ? '0' : '') + n; }

        function renderCalendar() {
            var grid = document.getElementById('aciCalGrid');
            var label = document.getElementById('aciCalMonthLabel');
            if (!grid || !label) return;

            label.textContent = monthNames[calMonth] + ' ' + calYear;
            grid.innerHTML = '';

            var firstDay = new Date(calYear, calMonth, 1).getDay(); // 0 Sun
            var daysInMonth = new Date(calYear, calMonth + 1, 0).getDate();

            // Leading blanks
            for (var b = 0; b < firstDay; b++) {
                var blank = document.createElement('div');
                blank.className = 'aci-cal-cell is-empty';
                grid.appendChild(blank);
            }

            var todayIso = today.getFullYear() + '-' + pad(today.getMonth() + 1) + '-' + pad(today.getDate());

            for (var d = 1; d <= daysInMonth; d++) {
                var iso = calYear + '-' + pad(calMonth + 1) + '-' + pad(d);
                var cell = document.createElement('div');
                cell.className = 'aci-cal-cell';
                if (iso === todayIso) cell.classList.add('is-today');

                var dayEvents = eventsOnDay(iso, calYear, calMonth);
                cell.setAttribute('data-count', dayEvents.length);

                var num = document.createElement('span');
                num.className = 'aci-cal-daynum';
                num.textContent = d;
                cell.appendChild(num);

                // Wrap chips in a scrollable container so overflowing events
                // never stretch the fixed-height cell.
                if (dayEvents.length) {
                    // Make the day tappable to reveal its event titles below.
                    cell.classList.add('has-events');
                    cell.setAttribute('role', 'button');
                    cell.setAttribute('tabindex', '0');
                    cell.dataset.iso = iso;
                    cell.dataset.dayLabel = monthNames[calMonth] + ' ' + d + ', ' + calYear;

                    var eventsWrap = document.createElement('div');
                    eventsWrap.className = 'aci-cal-events';

                    dayEvents.slice(0, 3).forEach(function (e) {
                        var chip = document.createElement('div');
                        chip.className = 'aci-cal-event';
                        chip.title = e.title + (e.venue ? ' - ' + e.venue : '');
                        chip.textContent = e.title;
                        eventsWrap.appendChild(chip);
                    });
                    if (dayEvents.length > 3) {
                        var more = document.createElement('span');
                        more.className = 'aci-cal-more';
                        more.textContent = '+' + (dayEvents.length - 3) + ' more';
                        eventsWrap.appendChild(more);
                    }
                    cell.appendChild(eventsWrap);
                }
                grid.appendChild(cell);
            }

            // Ongoing/month-long events strip (shown once, above the grid).
            renderOngoing();

            // Build the mobile agenda list for the same month.
            renderAgenda(daysInMonth, todayIso);
        }

        // Render the "Ongoing this month" strip. Lists each month-spanning
        // event once instead of repeating it across every day cell.
        function renderOngoing() {
            var box = document.getElementById('aciCalOngoing');
            if (!box) return;

            var ongoing = ongoingEventsForMonth(calYear, calMonth);
            if (!ongoing.length) {
                box.hidden = true;
                box.innerHTML = '';
                return;
            }

            var html = '<div class="aci-cal-ongoing-label">'
                + '<i class="fas fa-infinity"></i> Ongoing this month</div>'
                + '<div class="aci-cal-ongoing-list">';

            ongoing.forEach(function (e) {
                var href = (e.url && e.url !== '#') ? e.url : null;
                var inner = '<span class="dot"></span>'
                    + '<span>' + escHtml(e.title) + '</span>'
                    + (e.display_date ? '<span class="aci-cal-ongoing-date">' + escHtml(e.display_date) + '</span>' : '');
                html += href
                    ? '<a class="aci-cal-ongoing-item" href="' + href + '">' + inner + '</a>'
                    : '<span class="aci-cal-ongoing-item">' + inner + '</span>';
            });

            html += '</div>';
            box.innerHTML = html;
            box.hidden = false;
        }

        // Mobile agenda: list only the days in the current month that have
        // events, each with its event cards. Escapes text to avoid injection.
        function escHtml(s) {
            return String(s == null ? '' : s)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;');
        }

        function renderAgenda(daysInMonth, todayIso) {
            var agenda = document.getElementById('aciCalAgenda');
            if (!agenda) return;

            var html = '';
            for (var d = 1; d <= daysInMonth; d++) {
                var iso = calYear + '-' + pad(calMonth + 1) + '-' + pad(d);
                var dayEvents = eventsOnDay(iso, calYear, calMonth);
                if (!dayEvents.length) continue;

                var isToday = (iso === todayIso) ? ' is-today' : '';
                html += '<div class="aci-agenda-day">';
                html += '<div class="aci-agenda-daynum' + isToday + '">' + d + '</div>';
                html += '<div class="aci-agenda-events">';

                dayEvents.forEach(function (e) {
                    var href = (e.url && e.url !== '#') ? e.url : null;
                    var inner = '<h4 class="aci-agenda-title">' + escHtml(e.title) + '</h4>'
                        + '<div class="aci-agenda-meta">'
                        + (e.venue ? '<span><i class="fas fa-map-marker-alt"></i>' + escHtml(e.venue) + '</span>' : '')
                        + (e.display_date ? '<span><i class="fas fa-calendar"></i>' + escHtml(e.display_date) + '</span>' : '')
                        + (e.time ? '<span><i class="fas fa-clock"></i>' + escHtml(e.time) + '</span>' : '')
                        + '</div>';
                    html += href
                        ? '<a class="aci-agenda-card" href="' + href + '">' + inner + '</a>'
                        : '<div class="aci-agenda-card">' + inner + '</div>';
                });

                html += '</div>'; // .aci-agenda-events
                html += '</div>'; // .aci-agenda-day
            }

            agenda.innerHTML = html
                || '<div class="aci-agenda-empty">No events this month.</div>';
        }

        // Render the list of events for a tapped day into the details panel.
        function showDayDetails(iso, dayLabel) {
            var details = document.getElementById('aciCalDetails');
            if (!details) return;

            var dayEvents = eventsOnDay(iso, calYear, calMonth);
            if (!dayEvents.length) {
                details.hidden = true;
                details.innerHTML = '';
                return;
            }

            var html = '<div class="aci-cal-details-head">'
                + '<span>' + dayLabel + '</span>'
                + '<button type="button" class="aci-cal-details-close" aria-label="Close">&times;</button>'
                + '</div><ul class="aci-cal-details-list">';

            dayEvents.forEach(function (e) {
                var href = e.url && e.url !== '#' ? e.url : null;
                var inner = '<span class="aci-cal-details-title">' + e.title + '</span>'
                    + (e.venue ? '<span class="aci-cal-details-venue"><i class="fas fa-map-marker-alt"></i> ' + e.venue + '</span>' : '')
                    + (e.display_date ? '<span class="aci-cal-details-date"><i class="fas fa-calendar"></i> ' + e.display_date + '</span>' : '');
                html += '<li>' + (href
                    ? '<a href="' + href + '">' + inner + '</a>'
                    : inner) + '</li>';
            });

            html += '</ul>';
            details.innerHTML = html;
            details.hidden = false;

            var closeBtn = details.querySelector('.aci-cal-details-close');
            if (closeBtn) {
                closeBtn.addEventListener('click', function () {
                    details.hidden = true;
                    details.innerHTML = '';
                    var active = document.querySelector('.aci-cal-cell.is-selected');
                    if (active) active.classList.remove('is-selected');
                });
            }
        }

        // Tap/click a day with events to reveal its titles (event delegation).
        var calGridEl = document.getElementById('aciCalGrid');
        if (calGridEl) {
            calGridEl.addEventListener('click', function (ev) {
                var cell = ev.target.closest('.aci-cal-cell.has-events');
                if (!cell) return;
                var prev = calGridEl.querySelector('.aci-cal-cell.is-selected');
                if (prev) prev.classList.remove('is-selected');
                cell.classList.add('is-selected');
                showDayDetails(cell.dataset.iso, cell.dataset.dayLabel);
                document.getElementById('aciCalDetails')
                    .scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            });
            // Keyboard support (Enter / Space) for accessibility.
            calGridEl.addEventListener('keydown', function (ev) {
                if (ev.key !== 'Enter' && ev.key !== ' ') return;
                var cell = ev.target.closest('.aci-cal-cell.has-events');
                if (!cell) return;
                ev.preventDefault();
                cell.click();
            });
        }

        document.getElementById('aciCalPrev') && document.getElementById('aciCalPrev').addEventListener('click', function () {
            calMonth--; if (calMonth < 0) { calMonth = 11; calYear--; } renderCalendar();
            var det = document.getElementById('aciCalDetails');
            if (det) { det.hidden = true; det.innerHTML = ''; }
        });
        document.getElementById('aciCalNext') && document.getElementById('aciCalNext').addEventListener('click', function () {
            calMonth++; if (calMonth > 11) { calMonth = 0; calYear++; } renderCalendar();
            var det = document.getElementById('aciCalDetails');
            if (det) { det.hidden = true; det.innerHTML = ''; }
        });

        // Default view = list (matches the reference screenshot)
        showView('list');
    })();
</script>
<?php

declare(strict_types=1);

namespace App\Controller;

/**
 * Search Controller
 * Unified search across establishments, events, news, and happenings.
 *
 * NOTE: The upstream API endpoints do not support a server-side `search`
 * parameter, so this controller fetches the full list from each endpoint
 * and filters the results in PHP by matching the query against each record's
 * title / description fields. News is fetched fresh; other APIs are cached.
 */
class SearchController extends AppController
{
    /**
     * How long API responses are cached (seconds). Keeps the search fast by
     * avoiding repeated calls to the slow upstream API on every keystroke/submit.
     */
    private const CACHE_TTL = 600;
    private const RESULTS_PER_PAGE = 20;

    /**
     * Search action - queries multiple API endpoints and filters in PHP.
     */
    public function search(): void
    {
        $query = trim((string)$this->request->getQuery('q', ''));
        $category = trim((string)$this->request->getQuery('category', ''));
        $building = trim((string)$this->request->getQuery('building', ''));

        $results = [
            'establishments' => [],
            'events' => [],
            'shows' => [],
            'movies' => [],
            'news' => [],
            'happenings' => [],
        ];

        $baseUrl = env('API_BASE_URL', 'https://aci-cms.aranetacity.com');

        // Fetch all establishments once (cached) and reuse for both the
        // establishment search and the categories dropdown.
        $allEstablishments = $this->fetchAllEstablishments($category, $building);

        if ($query !== '') {
            $needle = mb_strtolower($query);

            // When a specific category or building is selected, the user is
            // narrowing the search. For establishments, we skip other types.
            // For content categories (Events, Shows, etc.), we search only that type.
            $establishmentCategories = ['Dining', 'Entertainment', 'Services', 'Shopping'];
            $isEstablishmentCategory = in_array($category, $establishmentCategories);
            $establishmentsOnly = ($isEstablishmentCategory || $building !== '');

            // ---- Establishments (shops, restaurants, services) ----
            if ($category === '' || $isEstablishmentCategory) {
                $results['establishments'] = $this->filterByKeyword(
                    $allEstablishments,
                    $needle,
                    ['title', 'category', 'estmaincategory', 'building', 'est_introtext']
                );
            }

            if (!$establishmentsOnly) {
                // ---- Events ----
                if ($category === '' || $category === 'Events') {
                    $eventsResponse = $this->callApi('/api/events_upcoming', [], self::CACHE_TTL);
                    $events = (!empty($eventsResponse['success']) && !empty($eventsResponse['data']))
                        ? $eventsResponse['data']
                        : [];
                    $results['events'] = $this->filterByKeyword(
                        $events,
                        $needle,
                        ['event_title', 'title', 'venue', 'category', 'event_desc', 'description']
                    );
                }

                // ---- News ----
                if ($category === '' || $category === 'News') {
                    $newsResponse = $this->callApi('/api/newslistall');
                    $news = (!empty($newsResponse['success']) && !empty($newsResponse['data']))
                        ? $newsResponse['data']
                        : [];
                    $results['news'] = $this->filterByKeyword(
                        $news,
                        $needle,
                        ['news_title', 'title', 'introtext', 'description', 'fulltext']
                    );
                }

                // ---- Happenings ----
                if ($category === '' || $category === 'Happenings') {
                    $happeningsResponse = $this->callApi('/api/happeningslist', [], self::CACHE_TTL);
                    $happenings = (!empty($happeningsResponse['success']) && !empty($happeningsResponse['data']))
                        ? $happeningsResponse['data']
                        : [];
                    $results['happenings'] = $this->filterByKeyword(
                        $happenings,
                        $needle,
                        ['event_title', 'title', 'description']
                    );
                }

                // ---- Shows (from TicketNet: Smart Araneta Coliseum + New Frontier Theater) ----
                if ($category === '' || $category === 'Shows') {
                    $shows = $this->fetchTicketNetShows();
                    $results['shows'] = $this->filterByKeyword(
                        $shows,
                        $needle,
                        ['title', 'venue', 'event_title', 'description']
                    );
                }

                // ---- Movies (from TicketNet: Gateway Cineplex 18) ----
                if ($category === '' || $category === 'Movies') {
                    $movies = $this->fetchTicketNetMovies();
                    $results['movies'] = $this->filterByKeyword(
                        $movies,
                        $needle,
                        ['title', 'description']
                    );
                }
            }
        } elseif ($category !== '' || $building !== '') {
            $establishmentCategories = ['Dining', 'Entertainment', 'Services', 'Shopping'];
            $isEstablishmentCategory = in_array($category, $establishmentCategories);
            $establishmentsOnly = ($isEstablishmentCategory || $building !== '');

            if (($category === '' || $isEstablishmentCategory) && $establishmentsOnly) {
                $results['establishments'] = $allEstablishments;
            }

            if (!$establishmentsOnly) {
                if ($category === 'Events') {
                    $eventsResponse = $this->callApi('/api/events_upcoming', [], self::CACHE_TTL);
                    $results['events'] = !empty($eventsResponse['success']) && !empty($eventsResponse['data'])
                        ? $eventsResponse['data']
                        : [];
                } elseif ($category === 'News') {
                    $newsResponse = $this->callApi('/api/newslistall');
                    $results['news'] = !empty($newsResponse['success']) && !empty($newsResponse['data'])
                        ? $newsResponse['data']
                        : [];
                } elseif ($category === 'Happenings') {
                    $happeningsResponse = $this->callApi('/api/happeningslist', [], self::CACHE_TTL);
                    $results['happenings'] = !empty($happeningsResponse['success']) && !empty($happeningsResponse['data'])
                        ? $happeningsResponse['data']
                        : [];
                } elseif ($category === 'Shows') {
                    $results['shows'] = $this->fetchTicketNetShows();
                } elseif ($category === 'Movies') {
                    $results['movies'] = $this->fetchTicketNetMovies();
                }
            }
        }

        $allResults = [];
        foreach ($results as $type => $rows) {
            foreach ($rows as $row) {
                $allResults[] = ['type' => $type, 'row' => $row];
            }
        }

        $totalResults = count($allResults);
        $pageCount = max(1, (int)ceil($totalResults / self::RESULTS_PER_PAGE));
        $pageQuery = $this->request->getQuery('page', 1);
        $requestedPage = is_scalar($pageQuery)
            ? filter_var((string)$pageQuery, FILTER_VALIDATE_INT)
            : false;
        $page = min($pageCount, max(1, $requestedPage === false ? 1 : $requestedPage));
        $pageResults = array_slice(
            $allResults,
            ($page - 1) * self::RESULTS_PER_PAGE,
            self::RESULTS_PER_PAGE
        );

        foreach ($results as $type => $rows) {
            $results[$type] = [];
        }
        foreach ($pageResults as $result) {
            $results[$result['type']][] = $result['row'];
        }

        $pagination = [
            'page' => $page,
            'pageCount' => $pageCount,
            'total' => $totalResults,
            'perPage' => self::RESULTS_PER_PAGE,
            'start' => $totalResults > 0 ? (($page - 1) * self::RESULTS_PER_PAGE) + 1 : 0,
            'end' => min($page * self::RESULTS_PER_PAGE, $totalResults),
        ];

        // Fetch buildings for the filter dropdown (cached).
        $buildings = [];
        $buildingsResponse = $this->callApi('/api/buildingslist', [], self::CACHE_TTL);
        if (!empty($buildingsResponse['success']) && !empty($buildingsResponse['data'])) {
            foreach ($buildingsResponse['data'] as $b) {
                $bid = $b['id'] ?? '';
                if ($bid !== '') {
                    $buildings[$bid] = $b['building_name'] ?? ($b['title'] ?? ($b['name'] ?? ''));
                }
            }
        }

        // Build the categories dropdown from ALL establishments across ALL buildings,
        // regardless of search results. This ensures categories are always available.
        $categories = [];
        $allEstablishmentsForCategories = $this->fetchAllEstablishments('', '');
        foreach ($allEstablishmentsForCategories as $est) {
            if (!empty($est['category'])) {
                $categories[$est['category']] = $est['category'];
            }
        }

        // Add non-establishment categories
        $categories['Events'] = 'Events';
        $categories['Shows'] = 'Shows';
        $categories['Movies'] = 'Movies';
        $categories['News'] = 'News';
        $categories['Happenings'] = 'Happenings';

        ksort($categories);

        $this->set(compact('results', 'query', 'category', 'building', 'buildings', 'categories', 'baseUrl', 'pagination'));
    }

    /**
     * Fetch all establishments across buildings, optionally filtered by
     * category and/or a specific building. Each per-building call is cached.
     *
     * @param string $category Category filter (case-insensitive)
     * @param string $building Building id filter
     * @return array<int, array<string, mixed>>
     */
    private function fetchAllEstablishments(string $category, string $building): array
    {
        $establishments = [];

        // Which buildings to query. If a specific building is chosen, only that
        // one; otherwise query the FULL set of buildings (matching the listing
        // pages). The old [1,2,3,4,5] set silently excluded establishments in
        // buildings 6/10/11/12/15 (e.g. some Jollibee branches), so they never
        // appeared in search regardless of API health.
        $buildingIds = $building !== '' ? [$building] : [1, 4, 10, 11, 12, 6, 5, 15, 2, 3];

        foreach ($buildingIds as $buildingId) {
            $params = ['building_id' => $buildingId];
            if ($category !== '') {
                $params['category'] = mb_strtolower($category);
            }

            $response = $this->callApi('/api/establishments_by_building', $params, self::CACHE_TTL);
            if (!empty($response['success']) && !empty($response['data'])) {
                foreach ($response['data'] as $row) {
                    $establishments[] = $row;
                }
            }
        }

        return $establishments;
    }

    /**
     * Filter a list of associative arrays, keeping rows whose given fields
     * contain the (already lower-cased) needle.
     *
     * @param array<int, array<string, mixed>> $rows Rows to filter
     * @param string $needle Lower-cased search term
     * @param array<int, string> $fields Field names to search within
     * @return array<int, array<string, mixed>>
     */
    private function filterByKeyword(array $rows, string $needle, array $fields): array
    {
        if ($needle === '') {
            return [];
        }

        $matched = [];
        foreach ($rows as $row) {
            foreach ($fields as $field) {
                if (empty($row[$field])) {
                    continue;
                }
                $haystack = mb_strtolower(strip_tags((string)$row[$field]));
                if (str_contains($haystack, $needle)) {
                    $matched[] = $row;
                    break; // Matched on one field; move to next row.
                }
            }
        }

        return $matched;
    }

    /**
     * Fetch shows from TicketNet JSON feeds.
     *
     * @return array<int, array<string, mixed>>
     */
    private function fetchTicketNetShows(): array
    {
        $shows = [];
        $feeds = [
            'https://ticketnet.com.ph/events/sac-api.json' => 'Smart Araneta Coliseum',
            'https://ticketnet.com.ph/events/nft-api.json' => 'New Frontier Theater',
        ];

        try {
            $http = new \Cake\Http\Client([
                'connectTimeout' => 5,
            ]);

            foreach ($feeds as $feedUrl => $venue) {
                try {
                    $response = $http->get($feedUrl);
                    if ($response->isOk()) {
                        $data = $response->getJson();
                        if (!empty($data['events']) && is_array($data['events'])) {
                            foreach ($data['events'] as $event) {
                                $event['venue'] = $venue;
                                $shows[] = $event;
                            }
                        }
                    }
                } catch (\Throwable $e) {
                    // Continue if one feed fails
                }
            }
        } catch (\Throwable $e) {
            // Shows are non-critical; ignore API failures
        }

        return $shows;
    }

    /**
     * Fetch movies from TicketNet JSON feed.
     *
     * @return array<int, array<string, mixed>>
     */
    private function fetchTicketNetMovies(): array
    {
        $movies = [];

        try {
            $http = new \Cake\Http\Client([
                'connectTimeout' => 5,
            ]);
            $response = $http->get('https://ticketnet.com.ph/events/movies-api.json');
            if ($response->isOk()) {
                $data = $response->getJson();
                if (!empty($data['events']) && is_array($data['events'])) {
                    $movies = $data['events'];
                }
            }
        } catch (\Throwable $e) {
            // Movies are non-critical; ignore API failures
        }

        return $movies;
    }
}

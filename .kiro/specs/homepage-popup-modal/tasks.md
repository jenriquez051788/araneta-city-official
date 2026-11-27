# Implementation Plan: Homepage Popup Modal

## Overview

The popup modal is built entirely inside `templates/Mainpages/home.php` using PHP for config and escaping, inline CSS, and an inline JavaScript IIFE. The design specifies PHP and JavaScript, so no language choice is needed. Implementation proceeds in this order: server-side config, helper and markup; styles; client script; then wiring and tests. Tests are optional sub-tasks.

## Tasks

- [x] 1. Implement the popup modal in the homepage template
  - [x] 1.1 Add the PHP config block, URL helper, and modal markup
    - Add the `$popupModal` array (enabled, title, message, image, ctaLabel, ctaUrl) at the top of `templates/Mainpages/home.php`
    - Add `popupModalIsSafeUrl()` (guarded with `function_exists`) that accepts only non-blank URLs starting with `http://`, `https://`, or `/`
    - Emit the overlay and dialog markup only when `enabled` is true, with `role="dialog"`, `aria-modal="true"`, `aria-labelledby` pointing at the title id, a Close button with `aria-label="Close"`, an optional image, and an optional CTA anchor
    - Wrap all values in `h()`, render the CTA only when the URL is safe and the label is non-empty, and use the `hidden` attribute as the default state
    - _Requirements: 1.2, 1.3, 1.5, 1.7, 1.8, 2.1, 4.1, 4.6, 6.1, 6.2, 6.3, 6.4, 6.5, 6.6, 7.3_

  - [x] 1.2 Add the modal styles
    - Add a page-scoped inline `<style>` block, emitted only when enabled
    - Overlay: `position: fixed; inset: 0; z-index: 1000`
    - Dialog: centered, at least 16px side margin, `max-height: calc(100vh - 32px)`, `overflow-y: auto`
    - Close button: at least 44x44px, positioned top-right and kept visible while the dialog scrolls
    - Add `body.popup-modal-open { overflow: hidden; }`
    - Reuse the site's existing fonts and colors, and make the image responsive
    - _Requirements: 1.3, 1.6, 2.1, 4.7, 5.1, 5.2, 5.3, 5.4, 5.5_

  - [x] 1.3 Add the modal client script
    - Add an isolated IIFE, emitted only when enabled, with `safeGet`, `safeSet`, `shouldShowModal`, `getFocusableElements`, `trapTabKey`, `isOverlayClick`, `openModal`, `closeModal`, and `init`
    - Use the storage key `popupModalDismissed` with the value `dismissed`
    - Make `init` return early if any required element is missing, and wrap it in try/catch
    - Show the modal on `DOMContentLoaded` unless it was dismissed
    - Handle Close click, Enter and Space, overlay click, and Escape
    - Guard `closeModal` with an `isOpen` flag
    - On open, record the previously focused element, focus the Close button, and lock body scroll
    - On close, restore the scroll position and focus (fall back to `document.body`)
    - Hide the image on its `error` event
    - Do not touch the carousel or floating widget DOM
    - _Requirements: 1.1, 1.4, 2.2, 2.3, 2.4, 2.5, 2.6, 2.7, 3.1, 3.2, 3.3, 3.4, 3.5, 4.2, 4.3, 4.4, 4.5, 4.7, 4.8, 7.1, 7.2, 7.4, 7.5_

- [x] 2. Checkpoint - Verify the implementation
  - Ensure all tests pass, ask the user if questions arise.

- [x] 3. Server-side tests (PHPUnit)
  - [x] 3.1 Write integration tests for the homepage render
    - Add `tests/TestCase/Controller/MainpagesControllerPopupTest.php`
    - `GET /` returns 200 and contains the modal markup when enabled
    - The ARIA attributes, `aria-label="Close"`, and a CTA without `target="_blank"` are present
    - Another page using `layout/default.php` does not contain the modal
    - _Requirements: 1.7, 2.5, 4.1, 4.6, 7.3_

  - [x] 3.2 Write property test for enabled flag controlling output
    - **Property 7: Enabled flag exactly controls markup and script presence**
    - **Validates: Requirements 1.8, 6.2, 6.3**

  - [x] 3.3 Write property test for HTML escaping
    - **Property 8: HTML-escaping is total over the special-character set**
    - **Validates: Requirements 6.4**

  - [x] 3.4 Write property test for CTA URL validity
    - **Property 9: CTA URL validity is determined solely by allowed prefixes**
    - **Validates: Requirements 6.5, 6.6**

  - [x] 3.5 Write unit tests for `popupModalIsSafeUrl` and the CTA omission rules
    - Cover `https://`, `/path`, `javascript:`, empty, and whitespace-only URLs
    - Cover title and message still rendering when the CTA is omitted
    - _Requirements: 6.5, 6.6_

- [x] 4. Client-side tests (JavaScript)
  - [x] 4.1 Set up the JS test harness
    - Add jest with jsdom and fast-check as dev dependencies and a `test:js` script
    - Add a helper in `tests/js/helpers/` that extracts the popup `<script>` and markup from the rendered homepage, or from a fixture, and evaluates them in jsdom
    - _Requirements: 7.2_

  - [x] 4.2 Write property test for the dismissal decision
    - **Property 1: Dismissal visibility decision is exact-match only**
    - **Validates: Requirements 3.2, 3.3, 3.5**

  - [x] 4.3 Write property test for safe storage
    - **Property 2: Safe storage never throws and always yields a usable result**
    - **Validates: Requirements 3.4**

  - [x] 4.4 Write property test for dismissal persistence
    - **Property 3: Dismissal persists a recognized flag for every dismissal path**
    - **Validates: Requirements 2.7, 3.1**

  - [x] 4.5 Write property test for click-target routing
    - **Property 4: Click-target routing determines close vs. no-op**
    - **Validates: Requirements 2.3, 2.6**

  - [x] 4.6 Write property test for the focus trap
    - **Property 5: Focus trap wraps at both boundaries for any focusable set**
    - **Validates: Requirements 4.3**

  - [x] 4.7 Write property test for focus restoration
    - **Property 6: Focus restoration targets the prior element or falls back to body**
    - **Validates: Requirements 4.4, 4.5**

  - [x] 4.8 Write property test for missing DOM elements
    - **Property 10: Missing required DOM elements never crash init and never show the modal**
    - **Validates: Requirements 7.2**

  - [x] 4.9 Write property test for non-interference with existing widgets
    - **Property 11: Modal lifecycle never mutates unrelated existing widgets**
    - **Validates: Requirements 7.5**

  - [x] 4.10 Write unit tests for dismissal triggers and error paths
    - Close button click, Enter, Space, overlay click, and Escape each hide the modal
    - Clicking inside the dialog keeps it open
    - A thrown initialization error leaves the carousel and widget scripts working
    - _Requirements: 2.2, 2.3, 2.4, 2.6, 7.1, 7.4_

- [x] 5. Final checkpoint - Ensure all tests pass
  - Ensure all tests pass, ask the user if questions arise.

## Notes

- Tasks marked with `*` are optional and can be skipped for a faster MVP
- Each task references specific requirements for traceability
- Visual requirements (1.3, 1.6, 5.1 to 5.5) are verified by manual QA across breakpoints, not by automated tests
- Property tests validate universal correctness properties; unit tests validate specific examples and edge cases

## Task Dependency Graph

```json
{
  "waves": [
    { "id": 0, "tasks": ["1.1"] },
    { "id": 1, "tasks": ["1.2"] },
    { "id": 2, "tasks": ["1.3"] },
    { "id": 3, "tasks": ["3.1", "4.1"] },
    { "id": 4, "tasks": ["3.2", "4.2", "4.3"] },
    { "id": 5, "tasks": ["3.3", "4.4", "4.5"] },
    { "id": 6, "tasks": ["3.4", "4.6", "4.7"] },
    { "id": 7, "tasks": ["3.5", "4.8", "4.9"] },
    { "id": 8, "tasks": ["4.10"] }
  ]
}
```

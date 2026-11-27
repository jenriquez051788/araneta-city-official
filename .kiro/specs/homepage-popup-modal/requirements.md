# Requirements Document

## Introduction

This feature adds a pop-up modal to the public Araneta City homepage. The homepage is served at `/` by `MainpagesController::home()` and rendered from `templates/Mainpages/home.php` inside `templates/layout/default.php`. The modal shows promotional or announcement content (title, optional image, message, optional call-to-action link) above the page content when a visitor lands on the homepage, and the visitor can dismiss it.

Assumptions (to be confirmed during review):
- The first version uses content defined in the homepage template. Admin-managed content is out of scope, but may be added later through the `dynamic-homepage-content` work.
- The modal appears once per browser session after dismissal.
- The modal uses plain HTML, CSS and JavaScript consistent with the existing inline scripts in `home.php`, with no new third-party library.

## Glossary

- **Homepage**: The public page rendered by `templates/Mainpages/home.php` at the site root `/`.
- **Popup_Modal**: The overlay dialog displayed on top of the Homepage content.
- **Overlay**: The semi-transparent backdrop that covers the Homepage behind the Popup_Modal.
- **Close_Control**: The button within the Popup_Modal that dismisses it.
- **CTA_Link**: The optional call-to-action link inside the Popup_Modal.
- **Visitor**: A person viewing the Homepage in a web browser.
- **Session_Storage**: The browser's `sessionStorage`, used to record that the Popup_Modal was dismissed during the current browser session.

## Requirements

### Requirement 1: Display Popup on Homepage Load

**User Story:** As a Visitor, I want to see a pop-up modal when I open the homepage, so that I notice important announcements or promotions.

#### Acceptance Criteria

1. WHEN the Homepage finishes loading, IF the Popup_Modal is active and has not been dismissed in the current browser session, THEN THE Popup_Modal SHALL display within 1 second of the page load event.
2. WHEN the Popup_Modal is displayed, THE Popup_Modal SHALL show a title of 1 to 100 characters and a message of 1 to 500 characters.
3. WHERE an image is configured for the Popup_Modal, THE Popup_Modal SHALL display the image above the title, scaled to fit within the Popup_Modal width without horizontal scrolling.
4. IF an image is configured for the Popup_Modal and the image fails to load, THEN THE Popup_Modal SHALL display the title and message without the image and without a broken-image indicator.
5. WHERE a CTA_Link is configured for the Popup_Modal, THE Popup_Modal SHALL display the CTA_Link with its label text of 1 to 50 characters below the message.
6. WHEN the Popup_Modal is displayed, THE Overlay SHALL cover 100% of the viewport width and height behind the Popup_Modal, and THE Popup_Modal SHALL remain visible and centered when the viewport is resized.
7. THE Popup_Modal SHALL appear only on the Homepage and not on other pages that use `templates/layout/default.php`.
8. IF no Popup_Modal is configured or the Popup_Modal is inactive, THEN THE Homepage SHALL load without displaying the Popup_Modal or the Overlay.

### Requirement 2: Dismiss the Popup

**User Story:** As a Visitor, I want to close the pop-up easily, so that I can continue browsing the homepage.

#### Acceptance Criteria

1. WHILE the Popup_Modal is displayed, THE Popup_Modal SHALL display a Close_Control within the top-right quadrant of the Popup_Modal, with a clickable/tappable area of at least 44 by 44 CSS pixels and an accessible name indicating that it closes the popup.
2. WHEN the Visitor activates the Close_Control by mouse click, touch tap, or by pressing Enter or Space while the Close_Control has keyboard focus, THE Popup_Modal SHALL hide the Popup_Modal and the Overlay within 300 milliseconds and leave the homepage content beneath scrollable and interactive.
3. WHEN the Visitor clicks or taps the Overlay outside the bounds of the Popup_Modal, THE Popup_Modal SHALL hide the Popup_Modal and the Overlay within 300 milliseconds.
4. WHEN the Visitor presses the Escape key while the Popup_Modal is displayed, THE Popup_Modal SHALL hide the Popup_Modal and the Overlay within 300 milliseconds.
5. WHEN the Visitor activates the CTA_Link, THE Popup_Modal SHALL navigate the browser to the CTA_Link destination in the same browser tab.
6. IF the Visitor clicks or taps inside the Popup_Modal on an area that is not the Close_Control or the CTA_Link, THEN THE Popup_Modal SHALL remain displayed and the Overlay SHALL remain displayed.
7. WHEN the Popup_Modal and the Overlay are hidden by any dismissal action in criteria 2 to 4, THE Popup_Modal SHALL NOT redisplay during the same page view, and the Visitor's scroll position on the homepage SHALL remain unchanged.

### Requirement 3: Remember Dismissal for the Session

**User Story:** As a Visitor, I want the pop-up not to reappear after I dismiss it, so that it does not interrupt my browsing.

#### Acceptance Criteria

1. WHEN the Visitor dismisses the Popup_Modal by any supported dismissal action (close control, backdrop click, or Escape key), THE Popup_Modal SHALL store a dismissal flag in Session_Storage within 100 ms of the dismissal action.
2. WHILE the dismissal flag exists in Session_Storage, THE Popup_Modal SHALL remain hidden each time the Homepage loads or reloads within the same browser tab session, and SHALL NOT be rendered visible at any point during page load.
3. WHEN a new browser session starts (a new browser tab or window is opened, or the existing tab is closed and reopened) and no dismissal flag exists in Session_Storage, THE Popup_Modal SHALL display on the Homepage.
4. IF Session_Storage is unavailable in the browser (access is blocked, throws an error, or the storage quota is exceeded), THEN THE Popup_Modal SHALL display on each Homepage load, SHALL close when the Visitor dismisses it, and SHALL NOT raise an uncaught script error in the browser console.
5. IF the stored dismissal flag has an unrecognized or corrupted value, THEN THE Popup_Modal SHALL treat the flag as absent and display on the Homepage load.

### Requirement 4: Accessibility

**User Story:** As a Visitor using assistive technology or a keyboard, I want the pop-up to be accessible, so that I can read and dismiss it.

#### Acceptance Criteria

1. WHILE the Popup_Modal is displayed, THE Popup_Modal SHALL expose the attributes `role="dialog"`, `aria-modal="true"`, and an `aria-labelledby` attribute whose value matches the id of the title element inside the Popup_Modal.
2. WHEN the Popup_Modal is displayed, THE Popup_Modal SHALL move keyboard focus to the Close_Control within 100 milliseconds.
3. WHILE the Popup_Modal is displayed, THE Popup_Modal SHALL keep keyboard focus on focusable elements inside the Popup_Modal, so that pressing Tab on the last focusable element moves focus to the first focusable element and pressing Shift+Tab on the first focusable element moves focus to the last focusable element.
4. WHEN the Popup_Modal is hidden, THE Popup_Modal SHALL return keyboard focus to the element that had focus before the Popup_Modal was displayed.
5. IF the element that had focus before the Popup_Modal was displayed is no longer present in the page when the Popup_Modal is hidden, THEN THE Popup_Modal SHALL move keyboard focus to the document body.
6. THE Close_Control SHALL have an accessible name of "Close".
7. WHILE the Popup_Modal is displayed, THE Homepage SHALL prevent background page scrolling by mouse wheel, touch drag, and keyboard scroll keys (Space, Page Up, Page Down, Arrow Up, Arrow Down).
8. WHEN the Popup_Modal is hidden, THE Homepage SHALL restore background page scrolling and the vertical scroll position the page had before the Popup_Modal was displayed.

### Requirement 5: Responsive Layout and Styling

**User Story:** As a Visitor on any device, I want the pop-up to fit my screen, so that I can read all of it.

#### Acceptance Criteria

1. WHILE the viewport width is between 320 pixels and 3840 pixels inclusive, THE Popup_Modal SHALL render with a width that leaves at least 16 pixels of margin between each of its left and right edges and the viewport edges, and with no horizontal scrollbar within the Popup_Modal.
2. WHILE the Popup_Modal content height exceeds the viewport height minus 32 pixels, THE Popup_Modal SHALL limit its own height to the viewport height minus 32 pixels and SHALL allow vertical scrolling within the Popup_Modal, with the close control remaining visible and the Homepage behind it not scrolling.
3. WHILE the Popup_Modal is displayed, THE Popup_Modal SHALL render above the site header, the floating app widget, and all other Homepage elements, so that no part of those elements overlaps the Popup_Modal.
4. THE Popup_Modal SHALL use the font families and colors that the site stylesheet defines for the Araneta City brand, with no font family or color values that differ from those in the site stylesheet.
5. IF the viewport width is below 320 pixels, THEN THE Popup_Modal SHALL remain fully reachable by vertical scrolling and SHALL keep its close control visible and operable.

### Requirement 6: Content Control and Safety

**User Story:** As a site administrator, I want the pop-up to be easy to turn on or off, so that I can control when it appears.

#### Acceptance Criteria

1. THE Homepage SHALL define the Popup_Modal title, message, CTA_Link label, CTA_Link URL, and a single boolean enabled flag in one location in `templates/Mainpages/home.php`.
2. WHILE the enabled flag is false, THE Homepage SHALL omit all Popup_Modal markup and Popup_Modal script from the rendered HTML.
3. WHILE the enabled flag is true, THE Homepage SHALL include the Popup_Modal markup and script in the rendered HTML.
4. WHEN the Homepage renders a title, message, CTA_Link label, or CTA_Link URL, THE Homepage SHALL escape the values for HTML output so that the characters `<`, `>`, `&`, `"`, and `'` appear as literal text and not as markup.
5. IF the CTA_Link URL does not begin with `http://`, `https://`, or `/`, THEN THE Homepage SHALL omit the CTA_Link from the Popup_Modal and SHALL still render the Popup_Modal title and message.
6. IF the CTA_Link URL is empty or contains only whitespace, THEN THE Homepage SHALL omit the CTA_Link from the Popup_Modal and SHALL still render the Popup_Modal title and message.

### Requirement 7: Preserve Existing Homepage Behavior

**User Story:** As a developer, I want the pop-up to not break existing homepage behavior, so that current features keep working.

#### Acceptance Criteria

1. WHEN the Popup_Modal is displayed or hidden, THE Homepage SHALL continue to run the existing Happenings carousel and floating app widget scripts, so that the browser console records 0 uncaught JavaScript errors originating from those scripts and the carousel still advances and the widget still opens and closes.
2. IF the Popup_Modal script cannot find a required DOM element, THEN THE Popup_Modal SHALL skip initialization, remain hidden, and leave all other Homepage content visible and clickable, so that the browser console records 0 uncaught JavaScript errors from the Popup_Modal script.
3. WHEN a visitor requests `/` and `MainpagesController::home()` handles the request, THE Homepage SHALL return HTTP status 200 and render the full page content with the Popup_Modal markup present.
4. IF the Popup_Modal script throws an error during initialization, THEN THE Homepage SHALL continue to run the Happenings carousel and floating app widget scripts, so that both remain functional after the error.
5. WHILE the Popup_Modal is displayed, THE Homepage SHALL keep the Happenings carousel and floating app widget scripts running and SHALL NOT remove or modify their DOM elements.
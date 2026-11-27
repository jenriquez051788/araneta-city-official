'use strict';

/**
 * Test harness for the homepage popup modal.
 *
 * The popup <script> is extracted verbatim from templates/Mainpages/home.php.
 * The markup is built from a fixture that mirrors the template output (the
 * template itself needs PHP to render). Both are evaluated inside jsdom.
 *
 * The script is an IIFE, so its internals are not reachable. Before evaluation
 * the harness injects a small export hook (`window.__popup`) just before the
 * `document.readyState` bootstrap block. Production code is not modified.
 */

const fs = require('fs');
const path = require('path');
const { JSDOM } = require('jsdom');

const HOME_TEMPLATE = path.resolve(__dirname, '../../../templates/Mainpages/home.php');

const START_MARKER = '<?php if (!empty($popupModal[\'enabled\'])) : ?>';
const BOOTSTRAP_MARKER = "if (document.readyState === 'loading')";

const EXPORT_HOOK = `
window.__popup = {
    safeGet: safeGet,
    safeSet: safeSet,
    shouldShowModal: shouldShowModal,
    getFocusableElements: getFocusableElements,
    trapTabKey: trapTabKey,
    isOverlayClick: isOverlayClick,
    openModal: openModal,
    closeModal: closeModal,
    init: init,
    isOpen: function () { return isOpen; }
};
`;

/** Extract the popup <script> body from the homepage template. */
function extractPopupScript(templatePath = HOME_TEMPLATE) {
    const source = fs.readFileSync(templatePath, 'utf8');
    const start = source.indexOf(START_MARKER);
    if (start === -1) {
        throw new Error('Popup block not found in ' + templatePath);
    }
    const scriptStart = source.indexOf('<script>', start);
    const scriptEnd = source.indexOf('</script>', scriptStart);
    if (scriptStart === -1 || scriptEnd === -1) {
        throw new Error('Popup <script> not found in ' + templatePath);
    }
    return source.slice(scriptStart + '<script>'.length, scriptEnd);
}

/** Insert the export hook into the extracted script (before the bootstrap block). */
function instrumentScript(script) {
    const idx = script.indexOf(BOOTSTRAP_MARKER);
    if (idx === -1) {
        throw new Error('Bootstrap block not found in popup script');
    }
    return script.slice(0, idx) + EXPORT_HOOK + script.slice(idx);
}

function escapeHtml(value) {
    return String(value)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

/**
 * Build popup markup matching the template output.
 * Pass `omit: ['overlay', 'modal', 'close', 'title']` to drop required nodes.
 */
function buildPopupMarkup(options = {}) {
    const {
        title = 'Welcome to Araneta City',
        message = 'Check out our latest happenings and promos.',
        image = null,
        ctaLabel = 'See Happenings',
        ctaUrl = '/happenings',
        omit = [],
    } = options;
    const has = (name) => !omit.includes(name);

    const inner =
        (has('close')
            ? '<button id="popup-modal-close" class="popup-modal-close" type="button" aria-label="Close">&times;</button>'
            : '') +
        (image ? '<img id="popup-modal-image" class="popup-modal-image" src="' + escapeHtml(image) + '" alt="">' : '') +
        (has('title')
            ? '<h2 id="popup-modal-title" class="popup-modal-title">' + escapeHtml(title) + '</h2>'
            : '') +
        '<p class="popup-modal-message">' + escapeHtml(message) + '</p>' +
        (ctaUrl && ctaLabel
            ? '<a id="popup-modal-cta" class="popup-modal-cta" href="' + escapeHtml(ctaUrl) + '">' + escapeHtml(ctaLabel) + '</a>'
            : '');

    const modal = has('modal')
        ? '<div id="popup-modal" class="popup-modal" role="dialog" aria-modal="true" aria-labelledby="popup-modal-title">' + inner + '</div>'
        : inner;

    return has('overlay')
        ? '<div id="popup-modal-overlay" class="popup-modal-overlay" hidden>' + modal + '</div>'
        : modal;
}

/** Markup for the unrelated widgets the popup must not touch (Req 7.5). */
function buildOtherWidgetsMarkup() {
    return (
        '<div class="happenings-swiper"><div class="swiper-wrapper"><div class="swiper-slide">Slide</div></div></div>' +
        '<button class="happenings-carousel-prev2" type="button">Prev</button>' +
        '<button class="happenings-carousel-next2" type="button">Next</button>' +
        '<div id="floating-app-widget"><button id="close-floating-app" type="button">x</button></div>'
    );
}

/**
 * Create a jsdom page containing the popup markup and evaluate the popup script.
 *
 * options:
 *   markup        full body HTML override (default: buildPopupMarkup(markupOptions))
 *   markupOptions options passed to buildPopupMarkup
 *   withOtherWidgets append carousel / floating widget markup
 *   storage       'default' | 'throwing' | custom object with getItem/setItem
 *   preset        object of sessionStorage entries set before the script runs
 *   run           evaluate the script (default true)
 *   script        script source override (e.g. a fixture)
 */
function createPopupEnv(options = {}) {
    const {
        markup,
        markupOptions = {},
        withOtherWidgets = false,
        storage = 'default',
        preset = {},
        run = true,
        script = extractPopupScript(),
    } = options;

    const body =
        (markup !== undefined ? markup : buildPopupMarkup(markupOptions)) +
        (withOtherWidgets ? buildOtherWidgetsMarkup() : '');

    const dom = new JSDOM('<!DOCTYPE html><html><body>' + body + '</body></html>', {
        url: 'https://example.test/',
        runScripts: 'outside-only',
        pretendToBeVisual: true,
    });
    const { window } = dom;

    window.scrollTo = () => {};

    if (storage === 'throwing') {
        const thrower = () => {
            throw new Error('storage unavailable');
        };
        Object.defineProperty(window, 'sessionStorage', {
            configurable: true,
            get: thrower,
        });
    } else if (storage !== 'default') {
        Object.defineProperty(window, 'sessionStorage', {
            configurable: true,
            value: storage,
        });
    } else {
        Object.keys(preset).forEach((k) => window.sessionStorage.setItem(k, preset[k]));
    }

    const env = {
        dom,
        window,
        document: window.document,
        get popup() {
            return window.__popup;
        },
        overlay: () => window.document.getElementById('popup-modal-overlay'),
        modal: () => window.document.getElementById('popup-modal'),
        closeButton: () => window.document.getElementById('popup-modal-close'),
        isVisible: () => {
            const el = window.document.getElementById('popup-modal-overlay');
            return !!el && !el.hasAttribute('hidden');
        },
        close: () => dom.window.close(),
    };

    if (run) {
        window.eval(instrumentScript(script));
    }
    return env;
}

module.exports = {
    HOME_TEMPLATE,
    extractPopupScript,
    instrumentScript,
    buildPopupMarkup,
    buildOtherWidgetsMarkup,
    createPopupEnv,
    escapeHtml,
};

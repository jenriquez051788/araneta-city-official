'use strict';

const fc = require('fast-check');
const { createPopupEnv, extractPopupScript } = require('./popupHarness');

describe('popup harness smoke test', () => {
    test('extracts the popup script from home.php', () => {
        expect(extractPopupScript()).toContain('popupModalDismissed');
    });

    test('opens the modal on load and exposes internals', () => {
        const env = createPopupEnv();
        expect(env.isVisible()).toBe(true);
        expect(env.popup.shouldShowModal('dismissed')).toBe(false);
        env.close();
    });

    test('fast-check is wired up', () => {
        fc.assert(fc.property(fc.string(), (s) => createPopupEnv({ preset: { popupModalDismissed: s } }).isVisible() === (s !== 'dismissed')), { numRuns: 20 });
    });
});

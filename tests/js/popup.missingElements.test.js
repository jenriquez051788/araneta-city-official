'use strict';

const fc = require('fast-check');
const { createPopupEnv } = require('./helpers/popupHarness');

const REQUIRED = ['overlay', 'modal', 'close', 'title'];

describe('missing required DOM elements', () => {
    // Feature: homepage-popup-modal, Property 10: Missing required DOM elements never crash init and never show the modal
    // **Validates: Requirements 7.2**
    test('init never throws, never shows the modal, and attaches no effective listeners', () => {
        fc.assert(
            fc.property(
                fc.subarray(REQUIRED, { minLength: 1 }),
                fc.boolean(),
                (omit, withImage) => {
                    let env;
                    try {
                        expect(() => {
                            env = createPopupEnv({
                                markupOptions: {
                                    omit,
                                    image: withImage ? '/img/popup.png' : null,
                                },
                            });
                        }).not.toThrow();

                        const { popup, document, window } = env;
                        expect(popup).toBeDefined();

                        // Explicit re-run of init must not throw either.
                        expect(() => popup.init()).not.toThrow();

                        expect(env.isVisible()).toBe(false);
                        expect(popup.isOpen()).toBe(false);

                        // Escape does nothing.
                        document.dispatchEvent(
                            new window.KeyboardEvent('keydown', { key: 'Escape', bubbles: true })
                        );
                        expect(env.isVisible()).toBe(false);
                        expect(popup.isOpen()).toBe(false);

                        // Click on whatever elements remain does nothing.
                        ['overlay', 'modal', 'closeButton'].forEach((name) => {
                            const el = env[name]();
                            if (el) {
                                el.dispatchEvent(new window.MouseEvent('click', { bubbles: true }));
                            }
                        });
                        expect(env.isVisible()).toBe(false);
                        expect(popup.isOpen()).toBe(false);

                        // Nothing was persisted as a dismissal.
                        expect(window.sessionStorage.getItem('popupModalDismissed')).toBeNull();
                        if (env.overlay()) {
                            expect(env.overlay().hasAttribute('hidden')).toBe(true);
                        }
                    } finally {
                        if (env) {
                            env.close();
                        }
                    }
                }
            ),
            { numRuns: 100 }
        );
    });
});

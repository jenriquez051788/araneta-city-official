'use strict';

const fc = require('fast-check');
const { createPopupEnv } = require('./helpers/popupHarness');

describe('trapTabKey focus trap', () => {
    // Feature: homepage-popup-modal, Property 5: Focus trap wraps at both boundaries for any focusable set
    // **Validates: Requirements 4.3**
    test('wraps Tab on last -> first and Shift+Tab on first -> last; otherwise default is not prevented', () => {
        fc.assert(
            fc.property(
                fc.boolean(), // image present
                fc.boolean(), // CTA present
                fc.integer({ min: 0, max: 5 }), // extra focusables
                fc.boolean(), // shift key
                fc.nat(), // focus position seed
                (hasImage, hasCta, extras, shift, seed) => {
                    const env = createPopupEnv({
                        markupOptions: {
                            image: hasImage ? '/img/x.png' : null,
                            ctaUrl: hasCta ? '/happenings' : null,
                        },
                    });
                    try {
                        const { document, window, popup } = env;
                        const modal = env.modal();
                        for (let i = 0; i < extras; i++) {
                            const b = document.createElement('button');
                            b.type = 'button';
                            modal.appendChild(b);
                        }

                        const list = popup.getFocusableElements(modal);
                        expect(list.length).toBeGreaterThan(0);
                        const pos = seed % list.length;
                        list[pos].focus();
                        expect(document.activeElement).toBe(list[pos]);

                        const event = new window.KeyboardEvent('keydown', {
                            key: 'Tab',
                            shiftKey: shift,
                            bubbles: true,
                            cancelable: true,
                        });
                        popup.trapTabKey(event, modal);

                        const first = list[0];
                        const last = list[list.length - 1];

                        if (!shift && pos === list.length - 1) {
                            expect(event.defaultPrevented).toBe(true);
                            expect(document.activeElement).toBe(first);
                        } else if (shift && pos === 0) {
                            expect(event.defaultPrevented).toBe(true);
                            expect(document.activeElement).toBe(last);
                        } else {
                            expect(event.defaultPrevented).toBe(false);
                            expect(document.activeElement).toBe(list[pos]);
                        }
                    } finally {
                        env.close();
                    }
                }
            ),
            { numRuns: 100 }
        );
    });
});

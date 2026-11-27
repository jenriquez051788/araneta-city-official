'use strict';

const fc = require('fast-check');
const { createPopupEnv } = require('./helpers/popupHarness');

describe('non-interference with existing widgets', () => {
    let env;

    beforeAll(() => {
        env = createPopupEnv({ withOtherWidgets: true });
    });

    afterAll(() => {
        env.close();
    });

    const snapshot = () => {
        const doc = env.document;
        const swiper = doc.querySelector('.happenings-swiper');
        const widget = doc.getElementById('floating-app-widget');
        return {
            swiper: swiper ? swiper.outerHTML : null,
            widget: widget ? widget.outerHTML : null,
        };
    };

    // Feature: homepage-popup-modal, Property 11: Modal lifecycle never mutates unrelated existing widgets
    // **Validates: Requirements 7.5**
    test('open/close sequences leave carousel and floating widget unchanged', () => {
        fc.assert(
            fc.property(
                fc.array(fc.constantFrom('open', 'close', 'escape', 'overlay', 'closeButton'), {
                    minLength: 0,
                    maxLength: 20,
                }),
                (ops) => {
                    if (env.popup.isOpen()) {
                        env.popup.closeModal();
                    }
                    const before = snapshot();
                    if (before.swiper === null || before.widget === null) {
                        return false;
                    }

                    ops.forEach((op) => {
                        switch (op) {
                            case 'open':
                                env.popup.openModal();
                                break;
                            case 'close':
                                env.popup.closeModal();
                                break;
                            case 'escape':
                                env.document.dispatchEvent(
                                    new env.window.KeyboardEvent('keydown', { key: 'Escape', bubbles: true })
                                );
                                break;
                            case 'overlay':
                                env.overlay().dispatchEvent(
                                    new env.window.MouseEvent('click', { bubbles: true })
                                );
                                break;
                            case 'closeButton':
                                env.closeButton().click();
                                break;
                        }
                    });

                    const during = snapshot();
                    if (env.popup.isOpen()) {
                        env.popup.closeModal();
                    }
                    const after = snapshot();

                    return (
                        during.swiper === before.swiper &&
                        during.widget === before.widget &&
                        after.swiper === before.swiper &&
                        after.widget === before.widget
                    );
                }
            ),
            { numRuns: 150 }
        );
    });

    test('example: open then close leaves widgets untouched', () => {
        const before = snapshot();
        env.popup.openModal();
        env.popup.closeModal();
        expect(snapshot()).toEqual(before);
    });
});

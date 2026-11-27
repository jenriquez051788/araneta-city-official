'use strict';

const fc = require('fast-check');
const { createPopupEnv } = require('./helpers/popupHarness');

describe('focus restoration on close', () => {
    let env;

    beforeAll(() => {
        env = createPopupEnv();
    });

    afterAll(() => {
        env.close();
    });

    function ensureClosed() {
        if (env.popup.isOpen()) {
            env.popup.closeModal();
        }
        env.document.body.focus();
        if (env.document.activeElement && env.document.activeElement !== env.document.body) {
            env.document.activeElement.blur();
        }
    }

    // Feature: homepage-popup-modal, Property 6: Focus restoration targets the prior element or falls back to body
    // **Validates: Requirements 4.4, 4.5**
    test('focus returns to prior element if attached, otherwise document.body', () => {
        fc.assert(
            fc.property(
                fc.integer({ min: 1, max: 8 }),
                fc.nat(100),
                fc.constantFrom('attached', 'detached', 'none'),
                (count, pick, mode) => {
                    const doc = env.document;
                    ensureClosed();

                    const created = [];
                    for (let i = 0; i < count; i++) {
                        const btn = doc.createElement('button');
                        btn.type = 'button';
                        doc.body.appendChild(btn);
                        created.push(btn);
                    }
                    const prior = created[pick % count];

                    try {
                        if (mode !== 'none') {
                            prior.focus();
                        }
                        env.popup.openModal();
                        if (mode === 'detached') {
                            prior.remove();
                        }
                        env.popup.closeModal();

                        const expected = mode === 'attached' ? prior : doc.body;
                        return !env.popup.isOpen() && doc.activeElement === expected;
                    } finally {
                        ensureClosed();
                        created.forEach((el) => el.remove());
                    }
                }
            ),
            { numRuns: 150 }
        );
    });

    test('example: attached prior button regains focus', () => {
        ensureClosed();
        const btn = env.document.createElement('button');
        env.document.body.appendChild(btn);
        btn.focus();
        env.popup.openModal();
        expect(env.document.activeElement).toBe(env.closeButton());
        env.popup.closeModal();
        expect(env.document.activeElement).toBe(btn);
        btn.remove();
    });
});

'use strict';

const fc = require('fast-check');
const { createPopupEnv } = require('./helpers/popupHarness');

// Feature: homepage-popup-modal, Property 3: Dismissal persists a recognized flag for every dismissal path

function makeSpyStorage() {
    const data = new Map();
    const sets = [];
    return {
        sets,
        data,
        getItem: (k) => (data.has(k) ? data.get(k) : null),
        setItem: (k, v) => {
            sets.push([k, String(v)]);
            data.set(k, String(v));
        },
    };
}

const paths = [
    {
        name: 'close click',
        run: (env) => env.closeButton().dispatchEvent(new env.window.MouseEvent('click', { bubbles: true })),
    },
    {
        name: 'close Enter',
        run: (env) =>
            env.closeButton().dispatchEvent(new env.window.KeyboardEvent('keydown', { key: 'Enter', bubbles: true, cancelable: true })),
    },
    {
        name: 'close Space',
        run: (env) =>
            env.closeButton().dispatchEvent(new env.window.KeyboardEvent('keydown', { key: ' ', bubbles: true, cancelable: true })),
    },
    {
        name: 'overlay click',
        run: (env) => env.overlay().dispatchEvent(new env.window.MouseEvent('click', { bubbles: true })),
    },
    {
        name: 'Escape',
        run: (env) =>
            env.document.dispatchEvent(new env.window.KeyboardEvent('keydown', { key: 'Escape', bubbles: true, cancelable: true })),
    },
];

describe('Property 3: dismissal persistence', () => {
    it('every dismissal path stores "dismissed" and shouldShowModal then returns false', () => {
        fc.assert(
            fc.property(
                fc.constantFrom(...paths),
                fc.string({ maxLength: 20 }),
                fc.string({ maxLength: 20 }),
                (dismissal, title, message) => {
                    const storage = makeSpyStorage();
                    const env = createPopupEnv({ storage, markupOptions: { title: title || 't', message: message || 'm' } });
                    try {
                        expect(env.isVisible()).toBe(true);
                        dismissal.run(env);

                        expect(env.isVisible()).toBe(false);
                        expect(storage.sets.length).toBeGreaterThan(0);
                        const last = storage.sets[storage.sets.length - 1];
                        expect(last[1]).toBe('dismissed');
                        expect(env.popup.shouldShowModal(storage.getItem(last[0]))).toBe(false);
                    } finally {
                        env.close();
                    }
                }
            ),
            { numRuns: 100 }
        );
    });
});

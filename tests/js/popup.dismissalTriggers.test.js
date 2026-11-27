'use strict';

const { createPopupEnv } = require('./helpers/popupHarness');

// Task 4.10: dismissal triggers and error paths
// Validates: Requirements 2.2, 2.3, 2.4, 2.6, 7.1, 7.4

function key(env, key) {
    return new env.window.KeyboardEvent('keydown', { key, bubbles: true, cancelable: true });
}

describe('dismissal triggers', () => {
    let env;

    beforeEach(() => {
        env = createPopupEnv({ withOtherWidgets: true });
    });

    afterEach(() => {
        env.close();
    });

    it('starts visible', () => {
        expect(env.isVisible()).toBe(true);
    });

    it('close button click hides the modal', () => {
        env.closeButton().dispatchEvent(new env.window.MouseEvent('click', { bubbles: true }));
        expect(env.isVisible()).toBe(false);
    });

    it('Enter on the close button hides the modal', () => {
        env.closeButton().dispatchEvent(key(env, 'Enter'));
        expect(env.isVisible()).toBe(false);
    });

    it('Space on the close button hides the modal', () => {
        env.closeButton().dispatchEvent(key(env, ' '));
        expect(env.isVisible()).toBe(false);
    });

    it('overlay click hides the modal', () => {
        env.overlay().dispatchEvent(new env.window.MouseEvent('click', { bubbles: true }));
        expect(env.isVisible()).toBe(false);
    });

    it('Escape hides the modal', () => {
        env.document.dispatchEvent(key(env, 'Escape'));
        expect(env.isVisible()).toBe(false);
    });

    it('clicking inside the dialog keeps it open', () => {
        const click = () => new env.window.MouseEvent('click', { bubbles: true });
        env.modal().dispatchEvent(click());
        expect(env.isVisible()).toBe(true);
        env.document.querySelector('.popup-modal-message').dispatchEvent(click());
        expect(env.isVisible()).toBe(true);
        env.document.getElementById('popup-modal-title').dispatchEvent(click());
        expect(env.isVisible()).toBe(true);
    });

    it('unrelated keys do not close the modal', () => {
        env.document.dispatchEvent(key(env, 'a'));
        env.closeButton().dispatchEvent(key(env, 'a'));
        expect(env.isVisible()).toBe(true);
    });
});

describe('initialization error paths', () => {
    it('throwing sessionStorage does not throw out of the script and the modal still shows', () => {
        let env;
        expect(() => {
            env = createPopupEnv({ storage: 'throwing', withOtherWidgets: true });
        }).not.toThrow();
        try {
            expect(env.isVisible()).toBe(true);
            expect(() => env.closeButton().click()).not.toThrow();
            expect(env.isVisible()).toBe(false);
        } finally {
            env.close();
        }
    });

    it('an init failure is swallowed and other DOMContentLoaded listeners still run', () => {
        const env = createPopupEnv({ run: false, withOtherWidgets: true });
        const { window, document } = env;
        const calls = [];
        const warn = window.console.warn;
        window.console.warn = () => {};
        try {
            document.addEventListener('DOMContentLoaded', () => calls.push('before'));

            // Force a failure inside init() by making DOM lookups throw.
            const realGetById = document.getElementById;
            document.getElementById = () => {
                throw new Error('boom');
            };

            expect(() => window.eval(require('./helpers/popupHarness').instrumentScript(require('./helpers/popupHarness').extractPopupScript()))).not.toThrow();

            document.addEventListener('DOMContentLoaded', () => calls.push('after'));
            expect(() => document.dispatchEvent(new window.Event('DOMContentLoaded'))).not.toThrow();

            document.getElementById = realGetById;

            expect(calls).toEqual(['before', 'after']);
            // Modal never opened and carousel/widget markup is intact.
            expect(env.isVisible()).toBe(false);
            expect(document.querySelector('.happenings-swiper')).not.toBeNull();
            expect(document.getElementById('floating-app-widget')).not.toBeNull();

            // Widget listener still works.
            let closed = false;
            document.getElementById('close-floating-app').addEventListener('click', () => (closed = true));
            document.getElementById('close-floating-app').click();
            expect(closed).toBe(true);
        } finally {
            window.console.warn = warn;
            env.close();
        }
    });
});

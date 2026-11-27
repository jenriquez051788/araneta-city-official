'use strict';

const fc = require('fast-check');
const { createPopupEnv } = require('./helpers/popupHarness');

/**
 * Storage implementations driven by the generated "mode".
 *  - working: Map-backed, behaves like sessionStorage
 *  - throwing: every getItem/setItem call throws
 *  - blocked: accessing window.sessionStorage itself throws (harness 'throwing')
 */
function makeWorkingStorage() {
    const data = new Map();
    return {
        getItem: (k) => (data.has(k) ? data.get(k) : null),
        setItem: (k, v) => {
            data.set(k, String(v));
        },
    };
}

function makeThrowingStorage() {
    return {
        getItem: () => {
            throw new Error('getItem failed');
        },
        setItem: () => {
            throw new Error('setItem failed');
        },
    };
}

function buildEnv(mode) {
    if (mode === 'working') {
        return createPopupEnv({ storage: makeWorkingStorage() });
    }
    if (mode === 'throwing') {
        return createPopupEnv({ storage: makeThrowingStorage() });
    }
    return createPopupEnv({ storage: 'throwing' });
}

describe('popup safe storage', () => {
    const envs = {};

    beforeAll(() => {
        ['working', 'throwing', 'blocked'].forEach((mode) => {
            envs[mode] = buildEnv(mode);
        });
    });

    afterAll(() => {
        Object.keys(envs).forEach((mode) => envs[mode].close());
    });

    // Feature: homepage-popup-modal, Property 2: Safe storage never throws and always yields a usable result
    // **Validates: Requirements 3.4**
    test('safeGet/safeSet never throw and return usable results for working or failing storage', () => {
        fc.assert(
            fc.property(
                fc.constantFrom('working', 'throwing', 'blocked'),
                fc.string(),
                fc.string(),
                (mode, key, value) => {
                    const { popup } = envs[mode];

                    let setResult;
                    expect(() => {
                        setResult = popup.safeSet(key, value);
                    }).not.toThrow();

                    let getResult;
                    expect(() => {
                        getResult = popup.safeGet(key);
                    }).not.toThrow();

                    expect(typeof setResult).toBe('boolean');

                    if (mode === 'working') {
                        expect(setResult).toBe(true);
                        expect(getResult).toBe(value);
                    } else {
                        expect(setResult).toBe(false);
                        expect(getResult).toBeNull();
                    }
                }
            ),
            { numRuns: 100 }
        );
    });

    test('safeGet returns null for a missing key on working storage', () => {
        expect(envs.working.popup.safeGet('missing-key-xyz')).toBeNull();
    });
});

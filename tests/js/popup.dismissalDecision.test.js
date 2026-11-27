'use strict';

const fc = require('fast-check');
const { createPopupEnv } = require('./helpers/popupHarness');

describe('shouldShowModal dismissal decision', () => {
    let env;
    let shouldShowModal;

    beforeAll(() => {
        env = createPopupEnv();
        shouldShowModal = env.popup.shouldShowModal;
    });

    afterAll(() => {
        env.close();
    });

    // Feature: homepage-popup-modal, Property 1: Dismissal visibility decision is exact-match only
    // **Validates: Requirements 3.2, 3.3, 3.5**
    test('returns false iff the value is exactly "dismissed"', () => {
        const nearMisses = fc.constantFrom(
            'Dismissed', 'DISMISSED', ' dismissed', 'dismissed ', 'dismissed\n',
            'dismiss', 'dismissedx', 'true', '1', ''
        );
        const flagValues = fc.oneof(
            fc.string(),
            fc.constant('dismissed'),
            fc.constant(null),
            fc.constant(undefined),
            nearMisses
        );

        fc.assert(
            fc.property(flagValues, (value) => {
                const result = shouldShowModal(value);
                return result === (value !== 'dismissed');
            }),
            { numRuns: 200 }
        );
    });

    test('examples: marker hides, absent/empty/unexpected values show', () => {
        expect(shouldShowModal('dismissed')).toBe(false);
        expect(shouldShowModal(null)).toBe(true);
        expect(shouldShowModal(undefined)).toBe(true);
        expect(shouldShowModal('')).toBe(true);
        expect(shouldShowModal('Dismissed')).toBe(true);
    });
});

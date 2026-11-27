'use strict';

const fc = require('fast-check');
const { createPopupEnv } = require('./helpers/popupHarness');

describe('isOverlayClick click-target routing', () => {
    let env;
    let isOverlayClick;

    beforeAll(() => {
        env = createPopupEnv();
        isOverlayClick = env.popup.isOverlayClick;
    });

    afterAll(() => {
        env.close();
    });

    /**
     * Build a random subtree under the modal and a random set of elements
     * outside the modal (siblings of the modal inside the overlay, and
     * elements elsewhere in the body). `parents[i]` is the index of the
     * parent among [modal, ...inside nodes created so far].
     */
    function buildTree(parentPicks, outsidePicks) {
        const doc = env.document;
        const overlay = env.overlay();
        const modal = env.modal();

        const created = [];
        const inside = [modal];
        parentPicks.forEach((pick) => {
            const parent = inside[pick % inside.length];
            const el = doc.createElement('span');
            parent.appendChild(el);
            inside.push(el);
            created.push(el);
        });

        const outside = [overlay];
        outsidePicks.forEach((pick) => {
            const parent = pick % 2 === 0 ? overlay : doc.body;
            const el = doc.createElement('div');
            parent.appendChild(el);
            outside.push(el);
            created.push(el);
        });
        outside.push(doc.body);

        return {
            overlay,
            modal,
            inside,
            outside,
            cleanup: () => created.forEach((el) => el.remove()),
        };
    }

    // Feature: homepage-popup-modal, Property 4: Click-target routing determines close vs. no-op
    // **Validates: Requirements 2.3, 2.6**
    test('true iff target is overlay or outside the modal subtree; false for modal and its descendants', () => {
        fc.assert(
            fc.property(
                fc.array(fc.nat(50), { maxLength: 15 }),
                fc.array(fc.nat(50), { maxLength: 5 }),
                (parentPicks, outsidePicks) => {
                    const tree = buildTree(parentPicks, outsidePicks);
                    try {
                        const insideOk = tree.inside.every(
                            (t) => isOverlayClick(t, tree.overlay, tree.modal) === false
                        );
                        const outsideOk = tree.outside.every(
                            (t) => isOverlayClick(t, tree.overlay, tree.modal) === true
                        );
                        return insideOk && outsideOk;
                    } finally {
                        tree.cleanup();
                    }
                }
            ),
            { numRuns: 200 }
        );
    });

    test('examples: overlay closes, modal and its children do not', () => {
        const overlay = env.overlay();
        const modal = env.modal();
        expect(isOverlayClick(overlay, overlay, modal)).toBe(true);
        expect(isOverlayClick(modal, overlay, modal)).toBe(false);
        expect(isOverlayClick(env.closeButton(), overlay, modal)).toBe(false);
    });
});

import { test } from 'node:test';
import assert from 'node:assert/strict';

import renderActions from '../../resources/js/components/pg-render-actions.js';

function setupWindow() {
    const target = new EventTarget();

    global.window = {
        addEventListener: target.addEventListener.bind(target),
        removeEventListener: target.removeEventListener.bind(target),
        dispatchEvent: target.dispatchEvent.bind(target),
    };
}

function makeGridAction({ wireId, parentId = null, rowId = '1' }) {
    const component = renderActions({ rowId, parentId });
    component.$wire = { id: wireId };

    return component;
}

function dispatchActionsUpdated(id) {
    window.dispatchEvent(new CustomEvent('pg:actions-updated', { detail: { id } }));
}

test('re-renders only the grid whose actions were updated', () => {
    setupWindow();

    const gridA = makeGridAction({ wireId: 'grid-a' });
    const gridB = makeGridAction({ wireId: 'grid-b' });

    gridA.init();
    gridB.init();

    assert.equal(gridA.renderTick, 0);
    assert.equal(gridB.renderTick, 0);

    dispatchActionsUpdated('grid-a');

    assert.equal(gridA.renderTick, 1, 'grid A should re-render its actions');
    assert.equal(gridB.renderTick, 0, 'grid B must not re-render when grid A updates');

    gridA.destroy();
    gridB.destroy();
});

test('matches the parent grid id for nested row actions', () => {
    setupWindow();

    const nested = makeGridAction({ wireId: 'child-wire', parentId: 'parent-grid' });
    const other = makeGridAction({ wireId: 'other-grid' });

    nested.init();
    other.init();

    dispatchActionsUpdated('parent-grid');

    assert.equal(nested.renderTick, 1, 'nested action should follow its parentId');
    assert.equal(other.renderTick, 0, 'unrelated grid must stay untouched');

    nested.destroy();
    other.destroy();
});

test('stops listening after destroy', () => {
    setupWindow();

    const grid = makeGridAction({ wireId: 'grid-a' });

    grid.init();
    grid.destroy();

    dispatchActionsUpdated('grid-a');

    assert.equal(grid.renderTick, 0, 'destroyed component must not react to events');
});

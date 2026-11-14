const test = require('node:test');
const assert = require('node:assert/strict');
const fs = require('node:fs');
const path = require('node:path');
const vm = require('node:vm');

const projectRoot = path.resolve(__dirname, '../..');
const source = fs.readFileSync(path.join(projectRoot, 'public/js/sari-theme.js'), 'utf8');

function environment({ saved = null, dark = false, blocked = false } = {}) {
    const documentEvents = new Map();
    const windowEvents = new Map();
    const values = new Map(saved ? [['sari:dashboard-theme', saved]] : []);
    const buttons = [];
    const root = { dataset: {} };
    class Element {
        constructor() { this.attributes = {}; this.disabled = false; }
        setAttribute(name, value) { this.attributes[name] = value; }
        closest() { return this; }
    }
    const subscribe = map => (name, callback) => {
        const callbacks = map.get(name) || [];
        callbacks.push(callback); map.set(name, callbacks);
    };
    const dispatch = (map, name, event = {}) => (map.get(name) || []).forEach(callback => callback(event));
    const system = {
        matches: dark,
        addEventListener(name, callback) { this.onChange = callback; }
    };
    const storage = {
        getItem(key) { if (blocked) throw new Error('Storage unavailable'); return values.get(key) ?? null; },
        setItem(key, value) { if (blocked) throw new Error('Storage unavailable'); values.set(key, value); }
    };
    const document = {
        documentElement: root,
        querySelectorAll() { return buttons; },
        addEventListener: subscribe(documentEvents),
        dispatchEvent(event) { dispatch(documentEvents, event.type, event); }
    };
    const window = { localStorage: storage, matchMedia() { return system; }, addEventListener: subscribe(windowEvents) };
    class CustomEvent { constructor(type, options) { this.type = type; this.detail = options.detail; } }
    const context = vm.createContext({ window, document, Element, CustomEvent });
    const run = () => vm.runInContext(source, context);
    const addButton = () => { const button = new Element(); buttons.push(button); return button; };
    const button = addButton();
    run();
    return {
        window, document, root, values, button, buttons, addButton, run, system,
        event: name => dispatch(documentEvents, name),
        click: (target = button) => dispatch(documentEvents, 'click', { target }),
        storageEvent: key => dispatch(windowEvents, 'storage', { key }),
        systemChange: value => { system.matches = value; system.onChange(); }
    };
}

test('initial theme uses saved preference, otherwise the system preference', () => {
    assert.equal(environment().root.dataset.sariTheme, 'light');
    assert.equal(environment({ dark: true }).root.dataset.sariTheme, 'dark');
    assert.equal(environment({ saved: 'light', dark: true }).root.dataset.sariTheme, 'light');
    assert.equal(environment({ saved: 'invalid' }).root.dataset.sariTheme, 'light');
});

test('toggle persists the choice, synchronizes controls, and ignores disabled buttons', () => {
    const app = environment();
    app.click();
    assert.equal(app.root.dataset.sariTheme, 'dark');
    assert.equal(app.values.get('sari:dashboard-theme'), 'dark');
    assert.equal(app.button.attributes['aria-pressed'], 'true');
    assert.equal(app.button.title, 'Switch to light mode');
    app.button.disabled = true; app.click();
    assert.equal(app.root.dataset.sariTheme, 'dark');
});

test('Livewire navigation and repeated script evaluation keep exactly one click handler', () => {
    const app = environment(); app.click();
    const next = app.addButton(); app.event('livewire:navigated');
    assert.equal(next.attributes['aria-pressed'], 'true');
    app.run(); app.run(); app.click(next);
    assert.equal(app.root.dataset.sariTheme, 'light');
});

test('storage notifications synchronize another tab and cleared preferences', () => {
    const app = environment();
    app.values.set('sari:dashboard-theme', 'dark'); app.storageEvent('sari:dashboard-theme');
    assert.equal(app.root.dataset.sariTheme, 'dark');
    app.values.clear(); app.storageEvent(null);
    assert.equal(app.root.dataset.sariTheme, 'light');
});

test('blocked storage still supports the switch and in-memory navigation', () => {
    const app = environment({ blocked: true }); app.click();
    app.event('livewire:navigating'); app.event('livewire:navigated');
    assert.equal(app.root.dataset.sariTheme, 'dark');
});

test('system changes apply until the user explicitly chooses a theme', () => {
    const app = environment(); app.systemChange(true);
    assert.equal(app.root.dataset.sariTheme, 'dark');
    app.click(); app.systemChange(true);
    assert.equal(app.root.dataset.sariTheme, 'light');
    app.window.SariTheme.set('invalid');
    assert.equal(app.window.SariTheme.get(), 'light');
});

test('all theme color references resolve and print keeps original fallbacks', () => {
    const css = fs.readFileSync(path.join(projectRoot, 'public/css/sari-theme.css'), 'utf8');
    const definitions = new Set([...css.matchAll(/(--sari-dark-[a-f\d]+)\s*:/g)].map(match => match[1]));
    function check(file) {
        const text = fs.readFileSync(file, 'utf8');
        for (const match of text.matchAll(/var\((--sari-dark-[a-f\d]+),/g)) {
            assert.ok(definitions.has(match[1]), `${file}: ${match[1]}`);
        }
    }
    function walk(directory) {
        for (const entry of fs.readdirSync(directory, { withFileTypes: true })) {
            const file = path.join(directory, entry.name);
            if (entry.isDirectory()) walk(file);
            else if (/\.(css|js|php)$/.test(file)) check(file);
        }
    }
    walk(path.join(projectRoot, 'resources/views'));
    walk(path.join(projectRoot, 'public'));
    assert.ok(css.includes('@media screen'));
    assert.ok(css.includes('@media print'));
    assert.ok(css.includes('.sari-theme-toggle { display: none !important; }'));
    assert.ok(!/filter\s*:\s*(?:invert|brightness)/.test(css));
});

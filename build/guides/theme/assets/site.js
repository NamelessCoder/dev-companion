// What the theme adds to a rendered page: the colour mode, the folds, the
// search, and the drawings. Bundled by `assets/build.mjs` and served as one
// file, so 48 pages share one download instead of carrying it each —
// `D-DOC-019`.
//
// The design system's custom elements. They frame a fenced block, colour it,
// and carry the copy button — all of which this file used to do by hand, with
// a highlighter of its own and a stylesheet mapping its classes onto the
// system's three syntax colours.
//
// The built drop-in, not the source: `src/` reaches a generated file that a
// git install does not carry, and the drop-in brings its own Lit, so nothing
// here has to depend on a version of it.
import { setIconSprite } from '@typo3/soul-design-system/dist/soul.js';

// How deep the page being read sits, which is what every path this script
// writes is relative to. It comes off the document rather than out of an
// inline script, because an inline script is the one thing an external file
// was supposed to remove.
var up = document.documentElement.getAttribute('data-up') || '';

// Where the icons resolve. An element left to itself looks beside its own
// module, and this one has been bundled into a file that sits somewhere else
// — every glyph would come back blank. Published flat with the rest by
// `assets/build.mjs`, so the depth of the page being read is the whole path.
setIconSprite(up + 'assets/actions.svg');

// Light or dark, as two segments with the chosen one filled — the design
// system's mode switch, which is the same treatment as an active navigation
// item because it is one. A reader who has pressed neither follows their
// machine, and the head has already read the stored choice, or the page would
// paint in the other mode for a frame.
(function () {
    var modes = document.getElementById('modes'),
        root = document.documentElement;

    var machine = matchMedia('(prefers-color-scheme: dark)');

    function show() {
        Array.prototype.forEach.call(modes.querySelectorAll('button'), function (button) {
            button.setAttribute('aria-pressed', root.dataset.theme === button.dataset.mode ? 'true' : 'false');
        });
        // What else follows the mode says so itself.
        document.dispatchEvent(new Event('mode'));
    }

    modes.hidden = false;
    show();
    machine.addEventListener('change', show);
    modes.addEventListener('click', function (event) {
        var button = event.target.closest('button');
        if (!button) { return; }
        root.dataset.theme = button.dataset.mode;
        localStorage.setItem('theme', button.dataset.mode);
        show();
    });
})();

// The list of pages: where the one being read is, and the button that folds
// the list away where the screen is too narrow to carry it.
(function () {
    var menu = document.getElementById('menu'),
        side = document.querySelector('nav.side');

    // The tool reference is 26 entries, so the page being read can sit below
    // the fold of a sidebar that has already scrolled past it. Only the
    // sidebar moves: scrollIntoView would take the page with it.
    var current = document.querySelector('nav.side a.page.current');
    if (current && current.offsetTop > side.clientHeight) {
        side.scrollTop = current.offsetTop - side.clientHeight / 2;
    }

    // Only now is there something that can open the list again.
    document.documentElement.className += ' js';
    menu.hidden = false;
    menu.addEventListener('click', function () {
        var open = side.className.indexOf('open') === -1;
        side.className = open ? 'side open' : 'side';
        menu.setAttribute('aria-expanded', open ? 'true' : 'false');
    });
})();

// The search: a dialog opened with Ctrl-K, the key every documentation site a
// reader arrives from has taught them. The index behind it is fetched when the
// dialog is first opened and never before, so a reader who does not search
// pays nothing for one that is 213 KB.
(function () {
    var dialog = document.getElementById('search'),
        find = document.getElementById('find'),
        box = document.getElementById('q'),
        hits = document.getElementById('hits'),
        miss = document.getElementById('miss'),
        rest = document.getElementById('rest'),
        index = null, pending = false, at = -1;

    // What the shortcut is called on the machine reading the page. The key
    // itself is the same one; only the name of the modifier differs.
    if (/Mac|iPhone|iPad/.test(navigator.platform || '')) {
        document.getElementById('key').textContent = '⌘ K';
        find.setAttribute('aria-keyshortcuts', 'Meta+K');
    }

    function terms(value) {
        return value.toLowerCase().split(/[^a-z0-9_:.-]+/).filter(function (t) { return t.length > 1; });
    }

    // A title carries what a page is; a heading what a section is; the prose
    // only that the word occurs. Scored in that order, so the page named after
    // the question outranks the one that mentions it.
    //
    // The prose is scored by how often, capped: a word nobody put in a heading
    // is otherwise worth the same on the page about it and on the one that
    // names it once in passing. The cap keeps a long page from winning on
    // length alone.
    function score(page, words) {
        var title = page.title.toLowerCase(),
            heads = page.headings.join(' ').toLowerCase(),
            text = page.text.toLowerCase(),
            total = 0;
        for (var i = 0; i < words.length; i++) {
            var word = words[i], had = 0, times = text.split(word).length - 1;
            if (title.indexOf(word) !== -1) { total += 10; had = 1; }
            if (heads.indexOf(word) !== -1) { total += 4; had = 1; }
            if (times) { total += Math.min(times, 6); had = 1; }
            if (!had) { return 0; }
        }
        return total;
    }

    function snippet(page, word) {
        var found = page.text.toLowerCase().indexOf(word);
        if (found === -1) { return page.headings.slice(0, 2).join(' · '); }
        var from = Math.max(0, found - 40);
        return (from ? '…' : '') + page.text.slice(from, from + 130).trim() + '…';
    }

    // The word that was searched for, marked wherever it stands in what is
    // shown. A snippet is 130 characters out of the middle of a page, and
    // without the mark a reader has to find their own word in it again.
    function marked(into, text, word) {
        var lower = text.toLowerCase(), from = 0, found;
        while ((found = lower.indexOf(word, from)) !== -1) {
            into.appendChild(document.createTextNode(text.slice(from, found)));
            var hit = document.createElement('mark');
            hit.textContent = text.slice(found, found + word.length);
            into.appendChild(hit);
            from = found + word.length;
        }
        into.appendChild(document.createTextNode(text.slice(from)));
    }

    // Which hit the return key would open. It moves with the arrows and with
    // the pointer, and the list scrolls to it rather than the page.
    function choose(which) {
        var all = hits.children;
        if (!all.length) { at = -1; return; }
        at = (which + all.length) % all.length;
        for (var i = 0; i < all.length; i++) {
            all[i].firstChild.className = i === at ? 'page chosen' : 'page';
        }
        all[at].scrollIntoView({ block: 'nearest' });
    }

    function render(value) {
        var words = terms(value);
        hits.textContent = '';
        at = -1;
        if (!words.length || !index) {
            miss.hidden = true; rest.hidden = false;
            return;
        }
        var found = [];
        for (var i = 0; i < index.length; i++) {
            var points = score(index[i], words);
            if (points) { found.push({ page: index[i], points: points }); }
        }
        found.sort(function (a, b) { return b.points - a.points; });
        found.slice(0, 12).forEach(function (found) {
            var li = document.createElement('li'),
                a = document.createElement('a'),
                small = document.createElement('small');
            a.className = 'page';
            a.href = up + found.page.url;
            marked(a, found.page.title, words[0]);
            marked(small, snippet(found.page, words[0]), words[0]);
            a.appendChild(small);
            li.appendChild(a);
            li.addEventListener('mousemove', function () { choose(Array.prototype.indexOf.call(hits.children, li)); });
            hits.appendChild(li);
        });
        rest.hidden = true;
        miss.hidden = found.length !== 0;
        // The best hit is the one the return key opens, so a reader who knows
        // what a page is called types three letters and presses it.
        choose(0);
    }

    function open() {
        dialog.showModal();
        box.select();
        if (index || pending) { return; }
        pending = true;
        fetch(up + 'search.json').then(function (answer) { return answer.json(); }).then(function (loaded) {
            index = loaded;
            render(box.value);
        }).catch(function () { pending = false; });
    }

    find.hidden = false;
    find.addEventListener('click', open);

    document.addEventListener('keydown', function (event) {
        if ((event.metaKey || event.ctrlKey) && event.key.toLowerCase() === 'k') {
            event.preventDefault();
            if (dialog.open) { dialog.close(); } else { open(); }
        }
    });

    box.addEventListener('input', function () { render(box.value); });

    box.addEventListener('keydown', function (event) {
        if (event.key === 'ArrowDown') { event.preventDefault(); choose(at + 1); }
        if (event.key === 'ArrowUp') { event.preventDefault(); choose(at - 1); }
        if (event.key === 'Enter' && at !== -1) { event.preventDefault(); hits.children[at].firstChild.click(); }
    });

    // A dialog fills its own backdrop, so a click outside the panel arrives on
    // the dialog itself and nowhere else.
    dialog.addEventListener('click', function (event) {
        if (event.target === dialog) { dialog.close(); }
    });
})();

// The drawings: inlined, in the mode being read, and openable.
//
// Inlined rather than linked, because an `<img>` is a document of its own and
// cannot see this page's `@font-face` rules — the type inside it would be
// whatever sans the reader's machine happens to have, which is not the one the
// columns were placed against. The markup keeps the `<img>` until this runs,
// so a browser without a script still gets the drawing.
//
// Each ships twice, the dark file a straight token swap of the light one, and
// which one is right depends on a mode this page can hold against the machine
// — more than `<picture>` can be told, since a media query reads the machine
// and nothing else.
(function () {
    var dialog = document.getElementById('zoom'),
        body = dialog.querySelector('.zoom__body'),
        said = document.getElementById('zoom-said'),
        root = document.documentElement,
        machine = matchMedia('(prefers-color-scheme: dark)'),
        drawings = [];

    function dark() {
        return root.dataset.theme ? root.dataset.theme === 'dark' : machine.matches;
    }

    Array.prototype.forEach.call(document.querySelectorAll('main img[src*="images/"]'), function (image) {
        var drawing = {
            base: image.getAttribute('src').replace(/(-dark)?\.svg$/, ''),
            said: image.alt,
            shown: image,
        };

        // A button rather than a listener on the drawing: nothing here is
        // reachable by pointer only, and this is what carries the focus ring.
        var open = document.createElement('button');
        open.type = 'button';
        open.className = 'zoom';
        open.setAttribute('aria-label', 'Enlarge the drawing');
        image.parentNode.insertBefore(open, image);
        open.appendChild(image);

        open.addEventListener('click', function () {
            body.textContent = '';
            body.appendChild(drawing.shown.cloneNode(true));
            said.textContent = drawing.said;
            dialog.showModal();
        });

        drawings.push(drawing);
    });

    function draw() {
        drawings.forEach(function (drawing) {
            fetch(drawing.base + (dark() ? '-dark' : '') + '.svg').then(function (answer) {
                return answer.ok ? answer.text() : null;
            }).then(function (file) {
                if (!file) { return; }
                var holder = document.createElement('div');
                holder.innerHTML = file;
                var svg = holder.querySelector('svg');
                if (!svg) { return; }
                svg.setAttribute('role', 'img');
                svg.setAttribute('aria-label', drawing.said);
                drawing.shown.parentNode.replaceChild(svg, drawing.shown);
                drawing.shown = svg;
            }).catch(function () {});
        });
    }

    draw();
    document.addEventListener('mode', draw);

    dialog.querySelector('.zoom__close').addEventListener('click', function () { dialog.close(); });
    // The panel does not fill its own backdrop, so a click beside it arrives on
    // the dialog itself.
    dialog.addEventListener('click', function (event) {
        if (event.target === dialog) { dialog.close(); }
    });
})();

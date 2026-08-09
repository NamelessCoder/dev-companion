// What the theme adds to a rendered page: the search, the folds, the copy
// button and the colouring of a fenced block. Bundled by `assets/build.mjs`
// and served as one file, so 48 pages share one download instead of carrying
// it each — `D-DOC-019`.
import hljs from 'highlight.js/lib/core';
import bash from 'highlight.js/lib/languages/bash';
import json from 'highlight.js/lib/languages/json';
import yaml from 'highlight.js/lib/languages/yaml';

// How deep the page being read sits, which is what every path this script
// writes is relative to. It comes off the document rather than out of an
// inline script, because an inline script is the one thing an external file
// was supposed to remove.
var up = document.documentElement.getAttribute('data-up') || '';

// Which of the two the page is drawn in. The colours themselves are the
// stylesheet's `light-dark()` pairs, so all this writes is the attribute that
// says which half stands — and the head has already read the stored one, or
// the page would paint in the other for a frame.
(function () {
    var button = document.getElementById('theme'),
        root = document.documentElement,
        // The empty one is the machine's own setting, which is where a reader
        // who has never pressed this starts and where pressing it twice more
        // puts them back.
        order = ['', 'light', 'dark'];

    function next() {
        return order[(order.indexOf(root.dataset.theme || '') + 1) % order.length] || 'system';
    }

    function say() {
        button.setAttribute('aria-label', 'Colours: ' + (root.dataset.theme || 'system') + '. Switch to ' + next() + '.');
        button.title = button.getAttribute('aria-label');
    }

    button.hidden = false;
    say();
    button.addEventListener('click', function () {
        var chosen = next();
        if (chosen === 'system') {
            delete root.dataset.theme;
            localStorage.removeItem('theme');
        } else {
            root.dataset.theme = chosen;
            localStorage.setItem('theme', chosen);
        }
        say();
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

// The renderer names a fenced block's language and colours nothing, and a
// recorded tool answer is 200 lines of JSON in one grey. highlight.js does the
// colouring, with the three languages this corpus fences in registered and
// nothing else: json, yaml and bash are 137, 50 and 20 of its blocks, and the
// full build carries forty more languages nobody here writes.
//
// The palette stays this theme's. A stylesheet of its own would be a second
// set of colours to keep against the first, so `site.css` maps its classes onto
// the variables the rest of the page already uses.
hljs.registerLanguage('bash', bash);
hljs.registerLanguage('json', json);
hljs.registerLanguage('yaml', yaml);

Array.prototype.forEach.call(document.querySelectorAll('main pre > code'), function (block) {
    // The recorded answers run to tens of thousands of characters, and one of
    // them is not worth a visible pause on a page nobody reads for the colour.
    if (/(?:^|\s)language-(?:bash|json|yaml)(?:\s|$)/.test(block.className || '') && block.textContent.length < 40000) {
        hljs.highlightElement(block);
    }
});

// Half of what this documentation says is a command to run, and reading one out
// of a page is where a flag gets dropped. The button is written here rather
// than into the markup, because a browser without the clipboard would otherwise
// carry one that does nothing.
(function () {
    if (!navigator.clipboard) {
        return;
    }

    Array.prototype.forEach.call(document.querySelectorAll('main pre'), function (block) {
        var snippet = document.createElement('div'),
            copy = document.createElement('button'),
            said = null;

        snippet.className = 'snippet';
        block.parentNode.insertBefore(snippet, block);
        snippet.appendChild(block);

        copy.type = 'button';
        copy.className = 'copy';
        copy.textContent = 'Copy';
        copy.addEventListener('click', function () {
            navigator.clipboard.writeText(block.textContent.replace(/\n+$/, '')).then(function () {
                clearTimeout(said);
                copy.textContent = 'Copied';
                copy.className = 'copy done';
                said = setTimeout(function () {
                    copy.textContent = 'Copy';
                    copy.className = 'copy';
                }, 1600);
            });
        });
        snippet.appendChild(copy);
    });
})();

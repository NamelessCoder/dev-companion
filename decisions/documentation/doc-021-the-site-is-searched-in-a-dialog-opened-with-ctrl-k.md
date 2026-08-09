---
id: D-DOC-021
date: 2026-08-09
status: open
---

# D-DOC-021 — The site is searched in a dialog opened with Ctrl-K

**The search is a modal dialog, opened by the button in the bar or by Ctrl-K,
and the arrows and the return key move through the hits and open one.**

What it replaces wrote the hits into the sidebar, which is the list of pages a
reader had already looked past — and on a narrow screen it is folded away, so
the hits landed where nobody could see them.

## Evidence

- Under 860 px the stylesheet hides the sidebar until the menu button opens it.
  A reader searching from a phone filled a list that was `display: none`.
- The hits and the page list shared one 16.5 rem column, and each hit carries a
  snippet: twelve of them were shown where 48 page names had been.
- Nothing opened the search but the pointer. The field carried no shortcut, and
  Ctrl-K is what the documentation sites this audience arrives from bind.
- `showModal()` carries the focus, the backdrop and the escape key. Written
  against a `div` all three are the theme's, which is most of what a modal
  costs.
- The return key opening the best hit was driven in Chromium: on a page two
  directories deep, Ctrl-K, `feedback`, return lands on `feedback/judging.html`,
  and one arrow down lands on the second hit instead.
- The built assets grew from 7.6 KB of CSS and 28.7 KB of JS to 11.6 KB and 30.4
  KB. They are fetched once for the whole site — `D-DOC-019`.

## Decided

- The dialog is written into the layout, closed, and the script opens it. A
  `<dialog>` with no script is invisible rather than dead, which is what the
  hidden menu button already does for the sidebar.
- The button in the bar says the shortcut, and the script rewrites it to `⌘ K`
  on a Mac. A shortcut nothing names is one only a reader who learnt it
  elsewhere has.
- The index is still fetched on the first opening of the dialog rather than with
  the page: 245 KB nobody who does not search pays for.
- The best hit is chosen when the results are rendered, so three letters and the
  return key reach a page whose name the reader already knows.
- The searched word is marked in the title and in the snippet. A snippet is 130
  characters out of the middle of a page, and finding one's own word in it again
  is the reader's work otherwise.
- The footer names the three keys. The arrows and the return key are the half of
  this that nothing else would show.

## Assumed

- That Ctrl-K is free. It is the browser's on no engine this was opened in, and
  the handler takes it only where no other modifier is held.
- That `<dialog>` and `showModal()` are there. Both are baseline, and the button
  is the only way in, so a browser without them has no search at all.
- That nothing here runs a browser. The suite holds the index and the ranking;
  what the dialog does was driven once in Chromium and is held by nothing after
  that.

## Wrong if

- A reader cannot reach the search without a keyboard, which the button in the
  bar is what prevents.
- Ctrl-K is taken by the browser or by a screen reader for something the reader
  needed more, and the page takes it away.
- The dialog opens over a page that keeps scrolling under it, or the return key
  opens a hit that is not the one marked.
- The assets grow past what one fetch for the whole site is worth.

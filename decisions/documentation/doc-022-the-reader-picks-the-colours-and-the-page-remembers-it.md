---
id: D-DOC-022
date: 2026-08-09
status: open
---

# D-DOC-022 — The reader picks the colours and the page remembers it

**One button in the bar puts the site in light, in dark, or back on what the
machine says, and the choice is kept for the next page.**

The site had the machine's setting and nothing else, so a reader on a dark
desktop who wanted the page light had to change the desktop.

## Evidence

- The palette was two blocks of eleven variables, the second inside
  `@media (prefers-color-scheme: dark)`. A colour was written twice and could go
  wrong in one of the two.
- `light-dark()` reads the used `color-scheme`, which the stylesheet already
  declares — for the scrollbars, the search field and the caret, which a browser
  draws light on a dark page without it. So the pair is one declaration and the
  switch is one attribute.
- The stored choice has to be read before the page paints. The theme's script is
  deferred, and a reader who turned the light off would see it for a frame.
- Driven in Chromium with the browser preferring dark: the button walks system →
  light → dark → system, the body's background follows, and light survives a
  reload.

## Decided

- The three states are one button rather than three, and which one is on is
  drawn: a sun, a moon, and a half-filled circle for the machine's own setting.
  Its `aria-label` says the state and the next one, since an icon says neither.
- The machine's setting is the state a reader who never presses it is in, and
  pressing twice more puts them back. It is stored as the absence of the key, so
  a reader who chose nothing follows their machine when they change it.
- The colours are one set of `light-dark()` pairs. What the button writes is
  `data-theme` on `<html>`, and the stylesheet answers with
  `color-scheme: only light` or `only dark`.
- Two lines run in the head, before the stylesheet's first paint, and nothing
  else on this site does. What that buys is the frame; what it costs is the
  inline script `D-DOC-019` had removed from every page.
- The button is written hidden and shown by the script, like the menu button:
  nothing without a script can change what it names.

## Assumed

- That `light-dark()` is there. It is baseline, and a browser without it gets no
  colour from any of the eleven variables rather than the wrong one.
- That `localStorage` is available. A browser refusing it throws where the
  choice is written, which is a press that does not stick rather than a page
  that does not load.

## Wrong if

- A reader arrives on a page in the theme they turned off, which means the head
  script did not run or the attribute is written somewhere it does not reach.
- A colour reads wrongly in one of the two schemes, because a pair was written
  with one half taken from the other palette.
- The choice does not follow to the next page, or a reader who chose nothing
  stops following their machine.

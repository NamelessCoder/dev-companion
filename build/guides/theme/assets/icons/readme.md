# The signet

Two files, and both are this site's own drawing: the signet redrawn per optical
size rather than scaled, one for 16–19px and one for 20–31px. The small one is
the favicon and is linked; the other is inlined by
`structure/layout.html.twig`, because it carries a frame in the page's ink and
a marker in the accent, and a linked `<img>` can inherit neither.

No icon lives here any more. Every one on this site is `<sds-icon>`, which
resolves into the sprite the design system publishes — copied into `dist/` by
`assets/build.mjs` and pointed at by `assets/site.js`. A missing glyph is
contributed to `TYPO3/TYPO3.Icons` rather than drawn into this directory.

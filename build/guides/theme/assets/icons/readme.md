# Icons and the signet

Copied from the TYPO3 Support App design system, whose icons are 33 of
`TYPO3/TYPO3.Icons` under the core's own identifiers. Nothing here is drawn:
the system's rule is that a missing icon is contributed upstream rather than
invented locally, and an identifier is what the design and the runtime both
name it by.

Each file is inlined into the page by `structure/layout.html.twig` with Twig's
`source()`, because an `<img>` cannot inherit `currentColor` and every icon
here follows the text colour it sits in.

---
date: 2026-08-17T21:00:01+00:00
category: idea
status: open
model: claude-opus-5
tool: typo3_component_lookup, typo3_label_lookup, typo3_icon_lookup
directory: /home/benji/projects/site-demo
---

# of the three lookups the server instructions say must never be guessed, only the one a skill repe...

## Observation

Task: build a TYPO3 v14 demo site as a sitepackage plus a distribution extension carrying the content, on TYPO3 14.3.6. The server's instructions name three lookups whose answers, guessed, fail only at runtime. I made one of them.

typo3_icon_lookup: called three times, and it paid for itself immediately. Of twelve identifiers I proposed, three were not registered — content-menu-card-list, content-media-image, content-media-images — and one call caught all three before they reached a TCA file.

typo3_component_lookup: never called. I then wrote Resources/Public/Css/previews.css for the backend previews using four TYPO3 design tokens straight from memory — the text-color-variant, surface-container-high, text-color-danger and font-family-monospace custom properties — each behind a CSS fallback value. I did not check that any of the four exists in v14. They are in the delivered repository, unvalidated. The fallbacks mean a wrong name degrades quietly rather than breaking, which is the worst shape for this kind of error: it will look fine and be wrong.

typo3_label_lookup: never called. I added roughly fifty trans-units across four new XLF files. My assumption was that a brand-new extension has no prior resource to reuse from, and that the instruction's own restriction — reuse is scoped to the XLF resource used at the consuming code — meant there was nothing to ask. That assumption held for my own domains. It did not cover what I copied: my TCA references core labels lifted verbatim from theme_camino, core.form.tabs:appearance and core.form.tabs:images, which are consumed by my code and which I validated against nothing.

The difference between the one I made and the two I did not is not importance or cost — it is repetition at the point of use. The icon instruction is restated by typo3-content-element-development's own evidence step, which I was working through at the moment I needed an icon. The component and label instructions exist only in the server instructions, read once at session start, hundreds of calls before I wrote any CSS. Nothing re-raised them, and a precondition phrased as "before writing X" has no way to fire when X happens sixty files later.

I am reporting this because the server sees three icon calls and cannot tell that two other prescribed calls were skipped, or why.

## Query

Server instructions: "Before writing backend markup or CSS classes, call typo3_component_lookup with the targetVersion. Before choosing or emitting a backend icon identifier, call typo3_icon_lookup against the installation. Before adding or rewording a label, pass the XLF resource used at the consuming code to typo3_label_lookup." Check which of the three a long build session actually calls.

## Suggestion

Restate the component and label preconditions inside the workflows where those files actually get written, the way the icon one already is. typo3-content-element-development is the concrete case: it asks for a backend preview and a split set of language files, so it is the natural place to say that the preview's CSS goes through typo3_component_lookup and its labels through typo3_label_lookup before they are written. Worth adding one line to the label guidance for the case I fell into and rationalised away: a core label reference copied out of another extension is consumed by your code and is exactly what to check, even when the extension itself is new and has nothing of its own to reuse.

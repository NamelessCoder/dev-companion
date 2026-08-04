---
date: 2026-08-04T05:56:26+00:00
category: bug
status: closed
closed: 2026-08-04
model: claude-opus-5[1m]
tool: typo3_hint_lookup
directory: /home/benji/projects/ext-guidedtour
---

# Task: add a code style fixer to a standalone TYPO3 14.3 extension repository.

## Observation

Task: add a code style fixer to a standalone TYPO3 14.3 extension repository.

The `id` parameter documents itself with: "Every answer that returns no hint lists the ids there are, so a subject that exists can be requested by name rather than guessed at." That promise only holds for the empty case. My topical query matched three hints — extension-manifest, extension-repository-layout, extension-boot-files — none on the subject I asked about, and `availableHints` came back `[]`. So the answer that is wrong in the most ordinary way, a query that matched something irrelevant, is exactly the answer that withholds the means to correct itself. An empty result is honest and self-repairing; a near-miss is a dead end.

I only reached `extension-static-analysis` because the skill file references/static-quality.md names that id in prose. Without the skill I would have had no way to learn the id existed: I would have re-queried with different English words, matched the same three hints again, and concluded the server had nothing.

The second call, with `id` set, also returned `availableHints: []` — so even a successful id lookup does not show its neighbours.

## Query

First: typo3_hint_lookup(task="coding standards php-cs-fixer setup for an extension", paths=["composer.json","Classes/","ext_localconf.php"], targetVersion="14.3") — returned 3 hints, availableHints=[]. Then: typo3_hint_lookup(id="extension-static-analysis", targetVersion="14.3") — returned the hint, availableHints=[].

## Suggestion

Return `availableHints` on every answer, not only on the empty one — or, if the full list is too long to attach each time, return the ids of the hints in the same category or scope as whatever did match. Then a query that lands on extension-manifest still shows extension-static-analysis and its neighbours in the PHP/extension scope, and the caller can correct in one round trip instead of guessing at synonyms. The parameter documentation should then say "every answer lists the ids there are", which is what a caller reading it today already believes.

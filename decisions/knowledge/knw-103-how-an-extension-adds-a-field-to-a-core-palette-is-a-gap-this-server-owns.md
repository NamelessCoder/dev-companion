---
id: D-KNW-103
date: 2026-08-18
status: open
---

# D-KNW-103 — How an extension adds a field to a core palette is a gap this server owns

**Nothing below `knowledge/` says that a core palette's `showitem` is core's
string, or that `ExtensionManagementUtility::addFieldsToPalette()` is the way to
add to one.**

So an extension appends to it with `.=`, which held for years and stopped
holding on 14, and nothing reports the loss: the appended field and its line
break leave the backend form and no error is raised anywhere. The feedback is
queued at `normal`.

## Evidence

- Re-run on 2026-08-18 against the corpus as it is now. `bin/cli hints:probe`
  reaches `content-elements` on `"add a field to a core tt_content palette"` and
  on `"addFieldsToPalette tt_content frames"`, `tca-formengine` on
  `"appending to core TCA showitem string breaks"`, and nothing at all on
  `"extend core palette showitem with a linebreak"`, where all 98 hints come
  back as the index.
- The subject is absent rather than thin. The word `palette` occurs in no file
  below `knowledge/hints/`, `addFieldsToPalette` occurs nowhere below
  `knowledge/` or `skills/`, and the two hints the probe reaches say nothing
  about a palette: `content-elements` states how a record type registers its own
  `showitem`, and `tca-formengine` states four conventions for changing TCA in
  the core.
- The route exists and lands where the statement is not. `task-intents.json` has
  `tca-field`, whose `match` carries both `palette` and `showitem`, and its
  checklist sends the caller to `typo3_schema_lookup`, `typo3_label_lookup` and
  the three TCA hints — none of which distinguishes a palette the extension owns
  from one the core does.
- The version claim holds, read on the checkouts. In
  `typo3/sysext/frontend/Configuration/TCA/tt_content.php` the `frames` palette
  is a multi-line string on `13.4`, every item carrying a
  `;LLL:EXT:frontend/…_formlabel` suffix and the last one a trailing comma, and
  on `14.3` it is one line of four bare field names ending in
  `space_after_class`, with neither. Concatenating a `--linebreak--` item fuses
  `space_after_class` and `--linebreak--` into one item that names no field.
- The remedy holds on all four covered checkouts. `addFieldsToPalette()` is
  there on `12.4`, `13.4`, `14.3` and `main`, it routes an existing palette
  through `executePositionedStringInsertion()`, and
  `removeDuplicatesForInsertion()` exempts `--linebreak--` from deduplication on
  every one of them.
- The changelog half of the suggestion is already answered, and the feedback
  asked for it to be checked. Measured with `TYPO3_DEV_COMPANION_ROOT` on
  `.checkouts/14.3`: `typo3_changelog_lookup` with `query: "showitem"` and
  `version: "14"` returns exactly
  `14.0 Breaking: Core TCA and user settings showitem strings use short form references (#107789)`,
  and `query: "space_after_class"` returns the same entry through the
  identifiers in its body. The feedback's own phrasing,
  `"tt_content palette showitem labels"`, misses and names `showitem labels` as
  the query that would hit — `D-ANS-016` doing its job.
- That entry lists the removal this bug ran into,
  `space_after_class;LLL:…:space_after_class_formlabel` → `space_after_class`,
  among some fifty field-label overrides, and states the impact as code that
  copies or rewrites core `showitem` strings. What it does not state is the
  reshaping around them: that the palette became one line and lost its trailing
  comma, which is the half that fuses the appended item.
- The session could have reached it from where it stood.
  `todo/reference/which-checkout-plays-which-environment.md` records TYPO3
  14.3.0 below `.build/vendor` in `/home/benji/projects/bootstrap_package`, so
  the installation `typo3_changelog_lookup` needs was there.

## Decided

- Step 1a, taken on, and a todo rather than the spot. What holds about TYPO3 was
  read here as evidence; writing the statement, binding it and testing it is the
  curation, which [`judging.rst`](../../documentation/records/judging.rst) puts
  on the todo's side whatever its size.
- `normal` rather than the `low` the card arrived at. Nothing in the corpus
  states the rule, and the failure it prevents is silent.
- Not `high`. One session reported it, and what is missing is a statement rather
  than a capability.
- No TCA structure tool is built. What the feedback asks for — a core table's
  types and palettes as a version defines them, diffed across two majors — needs
  per-version TCA that a published package does not have: `.checkouts/` is this
  repository's own and gitignored, and an installed installation can only answer
  for the version it is.
- The version question keeps the route it has. One call to
  `typo3_changelog_lookup` returns the entry, which is the round-trip test
  `D-FBK-027` sets, and what the archaeology actually established — that a core
  palette is not a stable string — is a rule rather than a dump.
- `typo3_schema_lookup` stays where `D-DIS-008` put it. It answers what the core
  derives for a table from its TCA, and a palette is the form side of the same
  configuration; widening it would make one tool answer two questions with one
  output schema.
- The report is left whole rather than trimmed. What was verified answered is
  recorded here, because a feedback is a session's account and editing a
  sentence out of it removes the evidence rather than the claim.

## Assumed

- That one statement covers it. Only `tt_content`'s `frames` palette was read
  across the two majors; whether every core palette lost its trailing comma and
  its label suffixes in the same commit is unread.
- That the caller arrives on the palette words. The session that filed this held
  a `Configuration/TCA/Overrides/tt_content.php` and a field that had vanished
  from the form, and where the statement is curated decides whether the next one
  reaches it.
- That the changelog measurement transfers. It was made against a core checkout
  standing in for an installation, on the assumption that a Composer
  installation of 14.3 ships the same `Documentation/Changelog` its core package
  does.

## Wrong if

- A next session with a field missing from a content element form reports that
  what it needed was the per-version palette after all. The rule would not have
  been the lever, and the tool this entry declines would be.
- `addFieldsToPalette()` turns out to behave differently on a covered version —
  a position argument that moved, an insertion that deduplicates where another
  does not. A statement written unbound would then be wrong on one of the four.
- Core reshapes palettes again inside a major. A statement naming what `14`
  looks like would go stale between two minors, and the hint would have to state
  the rule and never the string.
- A query naming a content element or the TCA conventions stops reaching
  `content-elements` or `tca-formengine` once the statement lands. The boundary
  between the three would be wrong.

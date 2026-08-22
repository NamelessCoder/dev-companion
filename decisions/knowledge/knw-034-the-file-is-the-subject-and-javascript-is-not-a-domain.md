---
id: D-KNW-034
date: 2026-08-03
status: open
---

# D-KNW-034 — The file is the subject, and JavaScript is not a domain of its own

**The corpus is one file per subject, and the `javascript` domain that no path
detected and no hint carried is gone.**

Six of the twelve files were still named after a domain, which `D-KNW-029`
decided a file must not be — `general.json` worst of all, because "general"
stopped meaning anything the moment every entry named its own domains.

## Evidence

- 120 hints in 34 subject files, and every answer identical before and after:
  the same 312 title and prompt queries reach the same hints in the same order,
  and the ten miss-index listings hold the same sets. Only their order moved,
  which is the file order the loader reads in.
- `javascript` was a category in the file-name era that no file ever used, and
  it survived the tagging as a constant. Nothing detected as it — `.js` is read
  as TypeScript, because a `.js` file in the backend is the committed output of
  a `.ts` one — and `hintDomains()` added it whenever TypeScript was selected,
  so it could never select anything on its own.
- A caller says "javascript" more readily than "typescript". Both words already
  detect the domain; what did not carry the second one was the corpus itself —
  neither backend hint had it in its `appliesTo`, and the section heading said
  TypeScript alone.

## Decided

- One file per subject, named after it: `labels.json`, `records.json`,
  `content-elements.json`, `distribution.json`, `di.json`, `upgrade.json` and
  the rest. `css.json` became `backend-css.json`, because the subject is the
  backend's design system rather than the language it is written in.
- `tsconfig.json` is `typo3-tsconfig.json`. The name TypeScript's own compiler
  configuration has does not belong to a file about page and user TSconfig.
- Domains::JAVASCRIPT is deleted rather than kept for a hint that might want
  it. A domain nothing detects as is a tag that can only mislead the next
  author.
- The heading is "Backend TypeScript and JavaScript", and both backend hints
  carry the JavaScript vocabulary. The sources are TypeScript and the
  conventions are TypeScript's, but the artefact is JavaScript and a heading
  naming only the source reads as somebody else's subject.

## Assumed

- 34 files is the granularity a maintainer wants. Some hold one hint, which is
  what a subject nobody has written a second question for looks like.
- Nothing outside this repository reads a hint file by name. The ids did not
  move and they are what `typo3_hint_lookup` takes.

## Wrong if

- A new hint has no file to go in and one is invented per entry, which would
  mean the subjects are cut too fine to file against.
- Somebody looks for a subject under the domain it is written in, because two
  names for one shelf is what this removed.

## Covered by

- `HintsTest::theFileAHintSitsInDoesNotDecideWhatSelectsIt`
- `HintsTest::everyHintIsTaggedWithADomainSomeQuerySelects`

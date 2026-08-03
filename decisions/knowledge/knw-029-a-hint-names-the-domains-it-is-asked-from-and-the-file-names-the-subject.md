---
id: D-KNW-029
date: 2026-08-03
status: open
---

# D-KNW-029 — A hint names the domains it is asked from, and the file names the subject

**Each hint declares its domains in a `domains` field, and the file it sits in
is where it is kept rather than what selects it.**

The file name was three jobs at once: the shelf, the selector, and the heading
an answer prints. A hint belonging to two domains could therefore not say so —
it went into `general.json`, whose domain every query selects, and the corpus
grew a bucket that answers everything.

## Evidence

- `bin/cli hints:coverage`: General holds 19 of 66 hints and supplies 39 of 62
  matched over the scenario prompts; 16 of 32 answers are made of it alone. That
  is what `D-KNW-001`'s **Wrong if** named, and it is a property of the shelf
  rather than of the hints — `content-elements` is PHP, Fluid and TypoScript at
  once and had no way to say it.
- The granularity the file name imposed is uneven in the same direction:
  Backend CSS holds 19 hints of 115 words on average, one subject each, while
  `php.json` holds 21 for the whole framework at 338, and `general.json` 19 at
  418. `datahandler-persistence` is one hint over eight statements covering the
  datamap, relation resolution, record placement, the backend user, workspaces
  and the testing obligation.
- Its `appliesTo` names `querybuilder`, `restriction`, `enablecolumns`, `hidden
  record` and `deleted record`, and not one of its statements is about reading
  records. A grep for `removeAll|DefaultRestriction|enableFields|LanguageAspect|
  PageRepository|VersionState` over the whole corpus returns a single hit, and
  that one is about `excludeDoktypes` in a menu processor. The umbrella hid the
  gap by carrying the vocabulary for it.

## Decided

- The tag is the `Domains::` vocabulary — `php`, `fluid`, `typoscript`, `css`,
  `typescript`, `javascript` — and not the label an answer prints. The two are
  separate fields because they change for different reasons: a `.scss` path
  detects as `css`, while "Backend CSS" is a sentence telling somebody whose
  conventions those are.
- Per entry, with no file-level default. A default would make the file name mean
  something again, which is the whole of what this removes.
- The first domain is the heading the hint is printed under. A hint crossing
  domains is asked from all of them and filed under one, and which one is a
  judgment its author makes by writing it first.
- `any` stays as a tag rather than being dissolved into real domains in the same
  change. It is `general.json` renamed and it keeps the 63% share intact, so
  nothing about the answers moves while the mechanism does.
- This change alone is a freeze: `hints:coverage`, the domains, the scores, the
  order and the section headings for every hint title and every scenario prompt
  are byte-identical before and after. Splitting a hint and re-tagging `any` are
  the next steps and are the ones that move an answer.

## Assumed

- One primary domain is enough for the heading. A hint that would want to be
  printed under two is one hint doing two jobs, which is the thing being
  corrected rather than a case to support.
- Naming the domain per entry rather than per file survives the corpus being
  filed by subject. Six files times one tag is a defensible default today; forty
  subject files with a mixed set of tags is what it is for.

## Wrong if

- The `any` share does not fall once the corpus is filed by subject. Renaming
  General without dissolving it would leave every query answered from the same
  nineteen hints, and the mechanism would have bought nothing.
- A hint ends up tagged with every domain there is, to be found. That is `any`
  spelled longer, and it says the tag is being used as a reach control rather
  than as a statement about the subject.
- Somebody re-derives the domain from the file name because the two still agree
  everywhere except `general.json`.

## Covered by

- `HintsTest::everyHintIsTaggedWithADomainSomeQuerySelects`
- `HintsTest::theFileAHintSitsInDoesNotDecideWhatSelectsIt`

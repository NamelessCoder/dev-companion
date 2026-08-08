---
id: D-ANS-067
date: 2026-08-08
status: open
---

# D-ANS-067 — The changelog above the installed major comes from the manual

**`typo3_changelog_lookup` answers from the installation for the versions it
ships and from docs.typo3.org for every version above its own major, and each
entry says which side it came from.**

A package carries every changelog down to 7.0 and nothing above its own major,
so the entries a caller is upgrading *to* were the ones the tool could not show.
`skills/typo3-extension-upgrade/SKILL.md` names that gap in its own words.

## Evidence

- Measured 2026-08-08. A 13.4 installation ships 50 version directories and 3388
  entries; the host names 55 and 3855. The difference is exactly six directories
  and 469 entries — 14.0, 14.1, 14.2, 14.3, 14.3.x and 15.0.
- The manual is `cms-core` under `/c/`, not one of the books under `/m/`. TYPO3
  Explained indexes no changelog entry at all, which is what the first reading
  of this got wrong.
- **It is not versioned.** Every version in the URL redirects to `main` —
  `12.4`, `13.4`, `14.3` and `99.9` all resolve to the same `objects.inv`, and
  what answers carries the whole history. So a version is applied to the entry
  names and never to the URL, and nothing is read per version.
- The inventory line carries the stated title, in the form
  `Deprecation: #110148 - Experimental backend ViewHelpers` — the same title
  `Changelog::read()` parses out of a file. So a title costs no read.
- `_sources/<page>.rst.txt` returns the entry's RST byte for byte, `.. index::`
  included. `Changelog::parse()` runs on it unchanged and read `removal: 16.0`
  off a 15.0 deprecation on the first try.
- What it costs: 186 ms for the inventory on a cold process, 14 ms to revalidate
  it against its entity tag afterwards, and 14.3 ms per entry body. A whole
  answer against a 13.4 installation is 292 ms. The per-read figure is what
  `D-ANS-066` bought — before the connection was kept, it was 90 ms.

## Decided

- The two sides never overlap. A version the installation has a directory for is
  that installation's, whatever the host says about it: an entry on disk is the
  code that is running, and the host publishes what the branch carries today.
- Each entry carries `publishedIn`, and a manual entry is linked by URL where an
  installed one carries its `EXT:` path. A caller acting on an entry above its
  own major is reading a moving target for a major that is not released, and an
  answer that presented both as "the changelog" would hide exactly that.
- Naming a version the installation ships reaches no host at all. That is the
  ordinary question, and it must not pay a round trip — or, with no network, a
  connect timeout — for entries the narrowing has already excluded.
- A host that fails once is not asked again in this process. This is the tool a
  session calls most and it used to touch nothing outside the machine; an
  offline session pays the timeout once rather than once a question.
- A manual entry is searched by name and by its stated title, never by the
  identifiers in its body. Those are 469 reads on a fallback that runs on a
  miss, which is six seconds, and the answer says the identifier search does not
  reach them.
- The stated title is carried as `stated` rather than `title`, because a search
  reads `title` and an installed entry gains one only from a file read. Under
  the searched name it would let a manual entry answer in the pass where the
  installed entry it should have found is still searched by its file name alone.
- One parser reads both sides. The host serves the same RST the package ships,
  so what differs is the delivery — `Changelog::parse()` and
  `Changelog::named()` take contents, and the two readers supply them.
- The installation is still required. Without one the tool is `unsupported` as
  before: this fills the gap above an installation rather than replacing one.

## Assumed

- That the redirect to `main` stays. If `/c/typo3/cms-core/13.4/` ever served a
  13.4-shaped changelog, this would be reading one manual and calling it
  another.
- That `_sources` stays published. It is a Sphinx build artefact rather than a
  documented endpoint, and asking for it to be named in `llms.txt` is one of the
  five asks that card left waiting.
- That the entry-name grammar holds. A page whose name does not parse as
  `<Type>-<issue>-<Spelling>` is skipped silently, which is right for the
  handful of non-entry pages and wrong if the writer ever renames the rest.

## Wrong if

- An answer carries an entry twice, once from each side, which is what a change
  to how the installation names its directories would do to the overlap rule.
- A session acts on a 15.0 entry as settled and the entry changes before the
  release. The `publishedIn` field and the sentence beside it are the whole of
  what stands between a caller and that.
- The host is slow rather than absent, and every changelog call pays up to eight
  seconds. Only the failure is held, not a slow success.
- A caller on a current major pays for a read that adds nothing, because the
  installation's highest version and the host's are the same. Nothing withholds
  the read in that case today: the skip is by named version, not by comparing
  the two highest.

## Covered by

- `CoreChangelogTest::anEntryAboveTheInstalledMajorComesFromTheManual`
- `CoreChangelogTest::aVersionTheInstallationShipsIsNeverTakenFromTheManual`
- `CoreChangelogTest::askingForAnInstalledVersionReachesNoHostAtAll`
- `CoreChangelogTest::aHostThatDoesNotAnswerIsSaidRatherThanReadAsNothing`
- `CoreChangelogTest::aManualTitleDoesNotShadowTheInstalledEntryAQueryIsAbout`

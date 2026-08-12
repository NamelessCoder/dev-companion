---
id: D-KNW-047
date: 2026-08-03
status: open
---

# D-KNW-047 — What installs TYPO3 below the extension being developed is a gap this server owns

**The Composer layout that installs TYPO3 below the extension's own
`composer.json` is inside this server's boundary and missing from it, so the
card is queued at `normal`.**

The corpus knows the keys of that layout from the other side only. It names
`extra.typo3/cms.web-dir` to say which directory is served, and therefore where
a one-off script may not be written, and says nothing about the keys that put an
installation there in the first place. A session composing such a root package
gets silence on two of them, a resolver failure whose message points at the
wrong package, and a key that is accepted at write time and ignored at install
time.

## Evidence

- Re-run on 2026-08-03 against the corpus as it is now. `bin/cli hints:probe`
  with the feedback's own query reaches `extension-manifest` at
  `appliesTo(13) + text(194)` and `project-build-and-scripts` at
  `appliesTo(6) + text(238)`, and nothing else. The first is what an extension
  declares about itself, the second is where a script goes; neither says how an
  installation comes to be below the package.
- Narrowing the query loses even that.
  `bin/cli hints:probe "TYPO3 extension composer root package app-dir web-dir typo3/cms-cli local installation"`
  matches nothing at all, and 78 hints come back as the index.
- The words are absent. `app-dir`, `vendor-dir`, `bin-dir` and `cms-cli` occur
  nowhere below `knowledge/` or `skills/`. `web-dir` occurs once, in
  `project.json`, and only to name the document root — the ground
  [`D-KNW-045`](knw-045-the-document-root-is-named-by-what-configures-it-and-by-what-serves-it.md)
  and
  [`D-KNW-026`](knw-026-where-a-one-off-script-may-not-be-written-is-a-gap-this-server-owns.md)
  stand on.
- The second claim holds, read in `.checkouts/14.3`.
  `typo3/sysext/core/composer.json` line 92 and the root `composer.json` line
  118 both require `typo3/cms-cli` at `^3.1.3`, so a root constraint of `^5.0`
  cannot resolve against a 14.3 core. The package's version line has nothing to
  do with the core's, which is what makes `^5.0` a plausible thing to write.
- The other two claims cannot be read here. `typo3/cms-composer-installers` is
  below no checkout — `.checkouts/14.3` carries no vendor directory — so the
  message `app-dir` is ignored with, and where a root-package extension is
  loaded from, need an installation rather than a grep.
- Part of the third claim is answered already, and reachable. `public-assets`
  states that `Resources/Public/` of an installed package is published into the
  document root below `_assets/<hash>/`, and
  `bin/cli hints:probe "where does a composer-installed extension land, typo3conf/ext symlink or _assets"`
  reaches it at `appliesTo(7) + text(231)`. What no hint says is the half the
  reporting session called worth stating positively: that a root-package
  extension is loaded from the repository root, so an empty `typo3conf/ext/`
  beside it is not a broken installation.
- The discovery side already knows the root package.
  [`D-DIS-001`](../discovery/dis-001-the-root-package-counts-as-an-installed-package.md)
  counts it among the installed packages and was confirmed against two fixtures
  on 2026-08-01. What this server reads is therefore settled, and what TYPO3
  itself does with such a package is stated nowhere.
- The card is one of five. `bin/cli feedback:list` groups 12 open feedback under
  `/home/benji/projects/ext-guidedtour`, and `feedback/2026-08-03-162745` is the
  umbrella of five of them: it lists the five steps of the task, names four
  obstacles, says each was filed as its own feedback, and asks for a skill over
  the whole local environment. This is the first of the four. All five are in
  hand on their own branches today.

## Decided

- Step 1a, and queued rather than closed on the spot. What lands is a statement
  about Composer and about the TYPO3 installer, and this run could read one of
  its three claims.
- Trimmed rather than archived. The `_assets` half of the suggestion is in the
  corpus and reachable, so it is struck with `public-assets` named in its place;
  the rest stays open behind the card.
- `normal` rather than the `low` the card arrived at. Two of the three findings
  are traps rather than absences: `app-dir` is accepted when it is written and
  ignored when it is used, and the `cms-cli` failure blames a version line that
  looks wrong and is not.
- Not `high`. Nothing is blocked on it, and one session reported it, five times
  in nine minutes, rather than several sessions reporting it once.
- Not the suggestion's own wording. Its author was guessing about this
  repository, as [`judging.md`](../../documentation/records/judging.md) says
  every suggestion is, and a third of what it asks for is already here.
- The shape question is left where it was asked. Whether the local environment
  earns a skill is step 1b and belongs to the umbrella card, so this entry
  decides the knowledge half only.
- The four obstacle cards are not folded into one. They share a task and name
  four subjects — the Composer layout, the `setup` command, the site base of an
  imported distribution, and DDEV's settings management — and the umbrella is
  where a fold would be decided.
- Where the statement goes is the todo's. `project-build-and-scripts` is the
  candidate it starts from, since it already names `web-dir`.

## Assumed

- That one session wrote all five. They share a directory, a model and nine
  minutes, and nothing in a feedback records a session.
- That the pair the claims are about is the one the front matter states: TYPO3
  14.3.5 with `typo3/cms-composer-installers` v5. Nothing here records that
  pair, and the message about `app-dir` is a property of the installer rather
  than of the core.
- That a root package installing TYPO3 can be built where this repository runs.
  `Upkeep\Fixture` writes a Composer project below `.fixtures/` already, but it
  writes the vendor tree itself, and a real `composer install` needs the
  network.

## Wrong if

- The `app-dir` message turns out to be conditional — on the installer major, or
  on `web-dir` being set beside it. It is then one statement per line rather
  than one statement, and the todo plans one.
- The umbrella card lands a skill that owns the local environment. The statement
  belongs in that skill's material then, and these four cards should be folded
  into it under one `**Serves:**` line.
- A session composing the layout reads the written statement and still requires
  `typo3/cms-cli`. The gap would be in the routing rather than in the corpus,
  and this entry would have answered a cheap rung for a step-3 problem.
- Nothing here can install a root package without the network. The statement is
  then bound to a reading of the installer's source rather than to a run, and it
  has to say so.

## Since then

The card this entry queued was worked on 2026-08-03, and the last **Assumed** is
what it disproved: the network is reachable from here, so the layout was built
rather than reasoned about and all three claims were read off it. The first
**Wrong if** did not hold — the `app-dir` message is raised from the presence of
the key alone, and every covered major requires the same installer major — so
what landed is one statement per claim and unbound.
[`D-KNW-053`](knw-053-the-root-package-layout-is-stated-from-an-installation-and-holds-across-the-covered-majors.md)
carries the readings and the sentences,
[`R-KNW-064`](../../requirements/knowledge/knw-064-the-composer-keys-that-install-typo3-beneath-an-extension-are-answered.md)
what has to keep holding, and the statement is
`extension-repository-installation` rather than a clause in
`project-build-and-scripts`: that hint is about the repository that holds an
installation, and this is the repository that is only the extension.

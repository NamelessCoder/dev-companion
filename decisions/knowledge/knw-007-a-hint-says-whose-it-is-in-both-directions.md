---
id: D-KNW-007
date: 2026-08-02
status: open
---

# D-KNW-007 — A hint says whose it is in both directions

**A hint or statement declares `project` or `extension` the same way it declares
`core`, and the answer labels it where the caller is somewhere else.**

The enum has had the cases since `D-KNW-005` and nothing wrote them, so a
statement could say "this obliges a core patch" and could not say "this is what
a repository outside the core has to do" — it said nothing, which reads as
`any`: it holds wherever TYPO3 is written.

## Evidence

- Four hints are audience-specific and declared nothing: `core-tests` and
  `project-extension-tests` in `php.json`, `project-repository-layout` and
  `extension-repository-layout` in `general.json`. Their titles say whose they
  are and no field did, so `R-AUD-005` — an answer says who it obliges — was
  met for the core half of the corpus only.
- At least seven more read the same way — `extension-documentation`,
  `extension-asset-build`, `extension-static-analysis`, `sitepackage-layout`,
  `sitepackage-initial-content`, `installation-upgrade`, `site-sets` — and each
  is a judgment about a statement rather than a mechanical edit, which is why
  the queue carries them rather than this commit.

## Decided

- The pairs stay two hints, one subject each. Merging them into one hint whose
  statements carry a scope would cost each half its `appliesTo`, its `checks`
  and its title — the three things that make it findable — and the corpus
  already draws the line by splitting. What the field adds is not the split but
  the declaration.
- The label is symmetric and the rule is one: a declared scope is named where
  the paths it was matched for are somewhere else. `project` and `extension` are
  not distinguished from each other, because a session in a project works on the
  extensions in it and a notice between the two would fire on every sitepackage
  task.
- `Scope::of()` does not read a declared scope. It places a path, and a hint's
  scope is a property of the statement — letting the corpus decide what the
  caller's repository is would invert exactly the distinction `any` and
  `uncertain` keep apart.
- Nothing is filtered by it. `D-KNW-001` was the case for withholding rather
  than qualifying, and it turned on inverted advice — the backend's design
  system handed to a website theme. A project layout in a core answer is
  somebody else's convention, not the opposite of the right one.

## Assumed

- `Scope::Uncertain` gets no label at all. Where nothing placed the work there
  is nobody to contrast the statement with, and a notice on every hint of an
  unplaced call is the noise this rule exists to avoid.

## Wrong if

- The label starts appearing on answers a caller is squarely inside, which
  would mean the group's scope is not what the hint was matched for.
- A hint turns out to need both directions at once — binding for a project and
  for a core patch in different sentences. The statement-level field is where
  that goes, and a hint needing it per statement in both directions is one hint
  doing two jobs.

## Covered by

- `HintsTest::whatOnlyBindsOutsideTheCoreSaysSoInsideIt`
- `HintsTest::whatOnlyBindsACorePatchSaysSoOutsideTheCore`
- `VersionsTest::whoIsObligedIsWrittenAsDataToo`

---
id: D-SKL-036
date: 2026-08-12
status: open
---

# D-SKL-036 — A skill runs where the installer put it

**A copy of `skills/` taken out of this repository by hand is not something this
package supports, and `readme.md` is where that is said.**

Publication is what completes a skill: the installer writes `references/base.md`
into each directory, and a copy that never went through it opens on a link to
nothing.

## Evidence

- Every `SKILL.md` carries a `compatibility` line naming the server it needs and
  how it is installed, so a copy says what is missing. It does not put the base
  there.
- `Installer::BASE` is written at publication, one copy per skill, because each
  lands in another project alone and a link out of its own directory would
  resolve here and nowhere it is read.
- Making the copy work means committing one `references/base.md` per skill,
  which two things refuse: `digest()` hashes `skills/base.md` once because it is
  the only copy here, and `publishSkill()` writes the copy itself — so a
  committed one is overwritten at every publication and stale in between.
- Nobody has reported copying the directory. The three options were priced
  against each other on 2026-08-08 and against no session that hit it.

## Decided

- Unsupported, and said in `readme.md` where somebody about to copy the
  directory looks, rather than in a skill they would only read afterwards.
- Rejected: committing the base per skill. It buys an unreported case by putting
  a file in the repository that is stale between publications and overwritten at
  each one.
- Rejected: making the copy fail louder. The dead link and the `compatibility`
  line already say it, and anything further is a mechanism for a case nobody has
  hit.

## Assumed

- That somebody who copies a skill directory reads `readme.md` first, or reads
  it once the link fails.

## Wrong if

- A copied skill is used against this server and the session never notices the
  base is missing, so the order every task starts in is silently absent.
- A client appears that reads a skills tree straight out of a git checkout,
  which would make the copy the ordinary way in rather than a mistake.

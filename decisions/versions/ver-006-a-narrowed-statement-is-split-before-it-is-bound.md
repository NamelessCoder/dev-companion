---
id: D-VER-006
title: A narrowed statement is split before it is bound
date: 2026-08-18
status: confirmed
---

# D-VER-006 — A narrowed statement is split before it is bound

**A statement whose subject survived its boundary under a new condition is
split, so `until` binds only the half that stopped holding.**

A range is read as the whole truth set of the sentence it sits on, which is what
`D-VER-001` chose it for. Put on a sentence carrying two claims, it expires
both, and the caller reads the surviving half as removed.

## Evidence

- `installation-setup` carried "--create-site <url> and TYPO3_SETUP_CREATE_SITE
  create the root page and its site configuration under config/sites/, and
  nothing suppresses them" at `until: 13`, beside a `since: 14` statement about
  `--distribution`. A session installing on 14.3.6 read the pair as
  "--create-site is gone on v14" and recovered the true reading only from a
  clause in the middle of the neighbouring paragraph —
  `feedback/2026-08-18-070632`.
- `.checkouts/13.4` reaches `getSiteSetup()` and `createSiteConfiguration()`
  with no distribution branch in `SetupCommand` at all. `.checkouts/14.3` and
  `.checkouts/main` guard the same call with `$distributions['active'] === []`
  and warn where it does not hold. The option was narrowed at 14, not removed;
  the clause that expired is "nothing suppresses them".
- The cost runs both ways. Believing it removed leaves the installation with
  what the same hint calls no way to be told its own URL; believing it
  unconditional produces the doubled root page the operations checklist warns
  about.

## Decided

- The binding goes on the claim, so a compound statement is split before either
  half is bound.
- `installation-setup` is rewritten that way: what `--create-site` writes is
  unbound across the covered majors, and the condition that arrived becomes a
  `since: 14` statement of its own, standing in front of `--distribution` rather
  than inside it. The second copy of the condition goes with it.
- Rejected: keeping the tag and repairing the prose, which puts version words
  back into a sentence — `HintsTest` forbids that and `D-VER-001` is why.

## Assumed

- The reader takes the range as the statement's whole truth set rather than
  reading the neighbouring statements against it. That is what the answer
  renders, and `D-VER-001` chose filtering over qualifying so a caller need not
  read around it.

## Wrong if

- A split leaves the unbound half general enough that a caller acts on it where
  the condition applies — the doubled root page, rather than the missing site
  configuration this one produced.
- Another `until`-bound statement is found whose subject survived its boundary.
  `D-VER-001`'s reading of 2026-08-02 checked every range for holes and could
  not see this: a compound statement bound whole has a contiguous range.

## Confirmed on 2026-08-18

The second **Wrong if** happened twice and the rule absorbed both: every
`until`-bound statement was read against the four checkouts, and two carried a
claim that outlived the boundary — `css-color-surface-tokens`, whose second half
holds on all four branches, and `form-framework`, where the registration expires
at 14 and the deprecation is what arrives there. Both were split. Four more
looked like the defect and are not: they survive as deprecated routes, and the
successor is where a caller reading them as removed is sent anyway. The first
**Wrong if** shows up in a session acting on a split, not in a checkout.

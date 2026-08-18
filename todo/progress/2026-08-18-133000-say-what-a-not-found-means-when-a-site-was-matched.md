# Say what a not-found means when a site was matched, where a diagnosis will find it

**Serves:** knowledge/
**Priority:** low
**Branch:** todo/say-what-a-not-found-means-when-a-site-was-matched
**Claimed:** 2026-08-18

A request that matched a site and then answered not-found has one documented
cause here, and it sits where nobody diagnosing a failing request will reach it:
`datahandler-seeding` says a page tree seeded without saying otherwise is
written hidden, because `pages.hidden` defaults to 1 in TCA against 0 in the
schema, and that the receiving installation then answers not-found on every page
below the root. Its `appliesTo` is about writing a seeding script.

Establish against `.checkouts/` what a site whose root page is hidden or deleted
actually answers, and put that where the diagnosis looks — `installation-boot`
carries the neighbouring case, a base that matches no site at all. Then
`skills/typo3-development-installation/SKILL.md` can route the half it currently
leaves open: it separates a request that matched a site and failed inside it
from one that matched none, and only the second half has a lookup today.

The gap was reported in
[feedback/archive/2026-08-18-074606-no-skill-owns-an-installation-that-boots-but.md](../../feedback/archive/2026-08-18-074606-no-skill-owns-an-installation-that-boots-but.md),
whose skill question `D-SKL-059` answered; this is the fact it listed that no
entry owns.

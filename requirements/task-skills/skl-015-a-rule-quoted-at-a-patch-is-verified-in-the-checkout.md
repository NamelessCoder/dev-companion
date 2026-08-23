---
id: R-SKL-015
title: 'A rule quoted at a patch is verified in the checkout'
status: held
restsOn: [D-KNW-043, D-SKL-005]
heldBy:
  - SkillTest::aRuleQuotedAtTheIssueIsVerifiedInTheCheckout
---

# R-SKL-015 — A rule quoted at a patch is verified in the checkout

**A rule about what an API may be used for is verified in the checkout, and
reported at the strength its own source puts on it.**

An assessment does not rest on such a rule before that reading, and a rule that
forbids something is the one it rests on hardest.

A tracker comment and a documentation page are claims about the code, the way a
path and an identifier are, and the assessment that opens a core patch is where
one of them decides whether there is a patch at all. What holds the claim is
named: the class, its docblock, the tests that cover the form under dispute.

Enforced in code, warned about as fragile and advised in prose are three
different rules, and two neighbouring APIs regularly carry different ones. So
the strength is carried rather than rounded up — a rule hardened on the way into
an assessment closes the report on a prohibition nothing in the checkout holds,
and reads exactly like one that was checked.

## From

`feedback/2026-08-02-144814` (2026-08-02): Forge #105403 was answered with "you
*must not* use `f:image` for anything but FAL resources", and the session
repeated it as correct until its user asked what it made of the statement. The
checkout contradicted it — the ViewHelper's own first example is an `EXT:` path,
the core's functional suite renders that form with scaling and cropping, and
both docblocks warn about stability rather than support (`D-KNW-043`).

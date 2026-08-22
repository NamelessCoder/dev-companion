---
id: R-SKL-025
title: "An audit's list says what the repository already carries unmerged"
status: held
restsOn: [D-SKL-068]
---

# R-SKL-025 — An audit's list says what the repository already carries unmerged

**Every item on the list an audit shows carries what the repository already has
in flight against it, and the list is not shown before that is established.**

A finding and the work in flight against it are two different readings, and the
checkout answers only the first. An item already fixed on a branch nobody merged
reads on the list exactly like one nothing has touched, and the maintainer is
the only party who can tell them apart — which is what the agreement step exists
to stop being necessary.

The surface is wider than the open pull requests, and stating it is part of the
demand. A branch pushed without a pull request is where a maintainer's own
unfinished work sits, and it is the one that gets missed; a maintained release
line is the third. Naming only the first sends a session to a complete-looking
answer about a third of the surface.

An item found already carried is not thereby settled. What an unmerged branch
holds is a claim about a finding, and the audit's own bar for dropping a
candidate is what it has to clear.

## From

The feedback of 2026-08-19 09:43. A v14 release audit of a blog extension mapped
23 open pull requests against its 17 items and told the maintainer that item 2
was untouched. Thirteen branches had been pushed without a pull request, and one
of them carried item 2 already fixed — with the same diagnosis the audit had
reached, with the test the audit had found missing, and with two further v14
defects the audit had not found at all.

## Held by

- `SkillTest::anAuditsListSaysWhatTheRepositoryAlreadyCarriesUnmerged`

The step is step 6 of `typo3-extension-health`, between writing the list and
showing it. The assertion reads it for its position, for the three surfaces, for
the one answer per item, and for the method under it — including the sentence
that says the empty diff is the only reading the command settles.

---
id: R-SKL-014
status: held
restsOn: [D-SKL-008]
---

# R-SKL-014 — A review reads what the project already says about the patch

**The issue the message resolves and the change on the review server are read,
each asked for by its own number.**

Both are read before the code is read a second time, and neither is in the
checkout. The issue carries what the change is *for*, which a commit message can
only report from the author's side, and it is where a series announces itself —
an issue calling itself a part decides every finding about what is missing from
the patch. The review server carries the patch set that actually exists and any
comment nobody answered, which is the commonest reason a change sits unmerged
and the finding a second review makes twice.

The Forge issue and the Gerrit change are different numbers, and swapping them
answers rather than fails: both lookups return a real issue and a real change
under the wrong number, and neither payload says so. The check that catches it
is the subject — the change that comes back carries the subject of the commit
under review, or the number was wrong.

An answer of nothing is a result the review states. Where the commit in the
checkout and the change on the server differ, the review names which of the two
it read.

## From

The third recorded `REVIEW-03` run reviewed change 95070 without asking for
either, and closed its own report by saying Forge #110359 had not been fetched.
Both calls answered at once when they were made while judging it: the issue is
"Avoid calling ImageService methods - part 2" with an empty description, and its
part 1 is already in `origin/main`. The run judged a series as a patch standing
alone. Until then both tools existed and no skill routed to either.

## Held by

- `SkillTest::aReviewReadsTheReviewThePatchIsAlreadyIn`

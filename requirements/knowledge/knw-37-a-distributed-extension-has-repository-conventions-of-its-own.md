---
id: R-KNW-37
status: held
---

# R-KNW-37 — A distributed extension has repository conventions of its own

**A repository that is only the extension has conventions of its own, written
down as such.**

They are one unit rather than two, the declared constraint rather than the
installed version as the statement of support, no committed lock file,
dependencies shipped where the extension also installs outside Composer, and
the browser suite belonging to the package.

The project repository layout names what it is about, so the two are not read
for each other.

## From

`REVIEW-02`. The corpus had a hint for a project repository and none for a
distributed extension, and the review quoted the project one — moving the
browser suite to a repository that does not exist and calling the ignored lock
file non-reproducible (2026-07-31).

## Held by

- `HintsTest::aDistributedExtensionIsNotAnsweredWithTheProjectRepositoryLayout`

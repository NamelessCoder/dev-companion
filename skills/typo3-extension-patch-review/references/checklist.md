# Incoming change review checklist

The surfaces below are the work list, and the coverage the report closes on is
this same list with every entry answered. It is written from the diff: an entry
the diff does not touch is not applicable and costs the line it costs here,
while one it touches and nobody read is unassessed, which is not clean.

## Review surfaces

- **The change itself.** What it claims to do, what it does, and whether the two
  are the same thing. Where it changes what the frontend or the backend form
  renders, the diff says what it sets and nothing about what comes out.
- **What it removes or renames.** The package's own public surface: PHP members,
  TCA fields and types, TypoScript paths, Fluid templates, partials and
  sections, ViewHelper arguments, label keys, site set settings, database
  columns. Whoever installed the package calls them.
- **The declared range.** Every TYPO3 and PHP version the package's manifest
  claims, not the one the installation happens to be. A change verified on one
  point of the range is verified there.
- **The API it reaches for.** Whether the core already offers the construct, on
  every declared major, and whether the form used is the one that survives the
  next one.
- **Persisted data and editors.** Where the diff touches TCA, the schema or a
  migration: what happens to rows that already exist, and what the editor sees.
- **Labels and icons.** A user-facing string or an identifier the diff adds, and
  whether it resolves in the installation rather than only in the file.
- **Security.** A user-controlled value the diff moves, and the sink it reaches.
- **Coverage.** A test that fails before the change and passes after it, and
  where the package's suite has no layer that could carry one, that absence.
- **The commit message**, against the convention this repository writes.
- **Merge state and checks.** Whether the branch merges, what the pipeline ran,
  and which of the repository's own checks were run here.

## Severity

The bands are the merge decision, because that is what the review is for:

- **Blocks the merge** — the change is wrong, breaks a version the package
  declares, loses data, opens a security boundary, or removes a public surface
  without a migration path.
- **Send it back** — the change works and is not ready: a defect in a case it
  covers, a missing test for risky behaviour, a hand-rolled construct where a
  core API is established, a message that does not meet the convention.
- **Worth changing** — a concrete cost that does not stop the merge.
- **Recommendation** — a beneficial improvement with no verified violation.

Severity follows the demonstrated consequence and not the size of the diff. A
one-line change that breaks a declared major blocks; a large change that only
moves code does not.

## What a finding owes

A concrete location in the diff, what the code there does, the rule,
documentation or reading that says it is wrong, the consequence, and what would
remediate it. Short of those it is a question rather than a violation, and
reporting it as one costs the author exactly the reading the review skipped.

Say what the finding rests on: a line that was read, a command that was run with
what it printed, or a mechanism traced into an installed package. A finding read
out of a pipeline configuration and one with a verified line are not worth the
same, and a report that does not separate them says they are.

Say also whether **this change** introduced it. A defect the diff only stands
next to is reported as pre-existing, with that word: asking an author to fix
what they did not break is a different request, and it is theirs to decline.

Where a claim could not be settled on a version nobody can run here, the finding
says so and names the reading or the run that would settle it. Unverified is a
result; a confident sentence in its place is not.

## What a dropped candidate owes

A review drops more than it reports, and dropping is the step nothing records.
Each candidate raised while reading and then let go is named with what let it go
— the guard that turned out to be there, the default that turned out to be the
core's, the class that was actually read. One sentence each, beside the
findings.

The two directions are not held to the same bar. Raising a candidate costs a
reading; dropping one costs the maintainer a finding, silently. So a candidate
is dropped only where something concretely disproves it, and one that can be
neither established nor disproved is reported as open with the reading that
would settle it named beside it.

Two dismissals go wrong reliably: dropped because a comment or a docblock says
the code behaves that way, which is a sentence somebody wrote rather than the
behaviour; and dropped because the case looks unlikely, which is not disproved.
What disproves a path is what makes it impossible — a guard that cannot be
passed or a caller that cannot exist, at a line.

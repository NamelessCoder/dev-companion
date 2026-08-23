# Reach the session that never asked about the borrowed class

**Serves:** feedback/2026-08-19-090231-a-borrowed-backend-css-class-shipped-unverified.md
**Priority:** normal
**Waiting on:** nothing of its own. What this feedback still needs is
    `skills/typo3-extension-asset-build/` published, and the review that was the
    condition of it was given on 2026-08-24 — `todo/open/2026-08-19-090200`
    carries what it said and the rework it asks for. No second question is asked
    here, and `bin/cli todo:waiting` should not put one. The publishing commit
    is where this feedback is archived with `bin/cli feedback:archive`, and the
    sibling names that step.

Both halves the feedback asked for are answered, and only the delivery of the
second one is left.

The catalog half is settled: `classesSince` is derived on every entry,
`coveredClasses` answers a class the query named outright on a target its entry
is withheld for, and `D-CAT-006` records the shape and the range it costs. Asked
on 2026-08-21 whether that range goes down to one class each, the maintainer
answered the class list, so a v12 caller asking about `table-fit` is still told
nothing — the table class list binds at v13, and `D-CAT-006` carries that
measurement.

The entry point half is the draft, and it was read against this session's own
account on 2026-08-21, call by call. The description carries the words the task
arrived with, a dependency update and a Dependabot pull request, so it does not
depend on the session reclassifying npm work as TYPO3 work mid-flight. The order
reaches the declared majors in its first step and the borrowed class in the step
after every rebuild, which is where the class actually appeared. Two of the
three assumptions the session shipped are reached that way; the third, a
stylesheet rule deleted because the core was believed to have dropped the icon
font it names, was not, and the workflow now routes it to
`typo3_changelog_lookup`. What the class bullet claimed about a withholding
predated `D-CAT-006` and was corrected in the same commit. `D-SKL-067` carries
the whole reading.

---
name: typo3-extension-cleanup
description: Put a TYPO3 project, sitepackage, or extension right — fix, clean up, improve, modernize or tidy a repository and work off what a review found. Use for a request to change a repository as a whole rather than one subsystem: "look over my repository and put it right", "improve the code quality of my sitepackage", "clean up my extension and fix what is wrong", bringing a package back into line after an audit, and carrying a list of findings through to committed changes.
---

# TYPO3 Extension Cleanup

Turn an audit into changes that are made, in an order somebody can interrupt and
come back to. Keep this skill as routing and sequencing method; do not embed
versioned TYPO3 facts.

## The findings are the audit's, not this skill's

1. Work through [references/base.md](references/base.md) — it fixes the order
   every task here starts in, and here it establishes the installation and the
   package the list is written about.
2. Activate `typo3-extension-conformance` and let it run to its report. It owns
   the surfaces, the evidence a finding rests on, the severity and who each
   finding belongs to, and nothing here re-derives any of that. A cleanup that
   opens the checkout looking for findings of its own has replaced an audit with
   an impression, and the list it writes is not the one the report would have
   given.
3. Where a report already exists — this session produced one, or the request
   arrives carrying one — read it whole rather than running the audit again, and
   say which of the two the list was built from. A report from an earlier
   session is evidence about the checkout as it stood then.

## The list is written down and agreed before anything changes

4. Write one item per finding, in the report's own severity order, each carrying
   what the finding is, the file or registration it is about, the severity the
   report gave it, the workflow the report named as its owner, and a state. A
   finding the report left open, and a surface it reported unassessed, are items
   too: their work is establishing what the audit could not.
5. Show that list whole and let the maintainer cut items, reorder them or stop,
   before a single change is made. That is what the whole order exists for, and
   it is the one step nothing downstream recovers.
6. Keep the list in the session rather than in the repository. A worklist
   committed into somebody's history is a file nobody asked for, in a project
   this workflow is a guest in, and it has to be taken out again afterwards. So
   each item's state is reported as the work goes, and what the history keeps is
   the commits the items produced.
7. Do not begin the work while the list is being shown. A list arriving together
   with the changes it produced is one nobody had the chance to disagree with.

## The list is worked off item by item

8. Take the items in the list's order, grouped by the workflow that owns them.
   One activation covering that owner's items costs less than one per finding,
   and the owner is what decides how its own area is changed.
9. Activate the owning skill and carry across only the scope and the verified
   behaviour it needs: the finding, the evidence under it, the paths. Stop
   before editing files another owner has — the crossing is the transition
   itself, not a detail of the item.
10. Where an item has no owning workflow, it is worked here only where the
    project's own suite, linter or static analysis proves the change: the
    change, the check that covers it, and nothing wider than the finding. An
    item nothing here can prove goes back unassigned in the closing report
    instead. A finding no workflow owns and no check covers is a hole in the
    map, and changing it on judgement is what hides the hole.
11. Commit per item, or per group of items in one owner's area, and say which
    item that commit closed — the message from `typo3_commit_message_guide` with
    `workflow="project"`. A session that ends halfway is read out of the log,
    which is why the state belongs in the commits rather than in the list alone,
    and a log that says which finding each commit closed is what makes it
    readable.

## What closes it

12. Hand the worked list back to `typo3-extension-conformance` for the re-check.
    It kept responsibility for that when it handed the finding over, and a
    cleanup that grades its own work has no evidence the finding is gone.
13. Report what is left: the items still open, the items dropped with what
    dropped them, the ones sent back unassigned, and every finding the audit
    reported as open or unassessed that this work did not settle. A finished
    list and an abandoned one read alike in a summary.

This skill owns the entry point for a request worded as a change, the order the
findings are worked in, and staying with the list until it is empty. It does not
own what a finding is, what it is worth, or who fixes it: that is
`typo3-extension-conformance`'s, and it is read out of the report rather than
formed here. It does not own the changes in another workflow's area either —
those cross to `typo3-extension-testing`, `typo3-extension-documentation`,
`typo3-backend-module-development`, `typo3-content-element-development` or
`typo3-extension-upgrade`, and what this skill carries across the crossing is
the item and not the work.

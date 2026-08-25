---
date: 2026-08-24T17:32:11+00:00
category: missing-knowledge
status: open
model: claude-opus-5[1m]
tool: typo3_project_describe, typo3_rule_lookup
directory: /home/benji/projects/typo3-cms
---

# the guides list was returned and never pulled on; I hand-assembled the changelog rules instead

## Observation

Trimmed to what is left of this report: a list of pages was returned, two of
them fitted the work, and neither was opened. The changelog half is answered and
the reading that answers it is in
[`D-KNW-111`](../decisions/knowledge/knw-111-the-changelog-procedure-is-a-guide-of-its-own.md).

Task: pick another old open issue from forge.typo3.org, branch, and work it off
— which ended in a patch for #35069 plus a changelog entry.

typo3_project_describe returned a guides[] array of 17 ids and titles. I read the
list, registered that core/contribution/rules and core/testing/proving-a-rendering
existed, and never opened either. Not because they looked wrong — because at the
moment I needed them I was already mid-Bash and the list had scrolled past. The
pull is missing: nothing at the point of need said "there is a page for this".

Two places where a page would have carried me and I assembled the procedure by
hand instead:

1. Changelog conventions. I had to decide type, folder and Releases for a bugfix
   that changes rendered link output, and worked it out from AGENTS.md,
   Changelog/Howto.rst and neighbouring entries. Six calls to establish
   formatting. core/contribution/rules was sitting in the list the whole time.

2. Proving a rendering change. For #93375 I needed to prove what
   lib.parseFunc_RTE actually renders. I built the harness myself by finding
   ParseFuncTest.php, copying its sys_template insertion pattern, and iterating
   three times on the TypoScript multi-line value syntax before the fixture was
   right. The guides list contains core/testing/proving-a-rendering, whose title
   is exactly that task. I did not open it.

## Query

typo3_project_describe() returned guides[] with 17 entries including
core/contribution/rules, core/contribution/commit-messages,
core/testing/proving-a-rendering, core/testing/scripts,
any/testing/browser-check. I called typo3_rule_lookup zero times. Instead: cat
Changelog/14.3/Important-109107-*.rst; grep Howto.rst; head
Changelog/15.0/Feature-*.rst; grep '^\.\.  index::' Changelog/15.0/*.rst; head
-1 | cat -A.

## Suggestion

Make the guides reachable at the point of need rather than only at orientation.
Two concrete options:

- Have typo3_task_guide name the guide ids that cover the task it was asked
  about, so the routing happens when the task is stated rather than when the
  session opens. Its description says it does this ("names the task skill that
  owns the work where a published one does, beside the guide the work is written
  up in"), but I never called it — see my separate feedback on that.

- Have the answer of any lookup that touches changelog, testing or contribution
  carry a one-line "the procedure for this is <guide id>" pointer.

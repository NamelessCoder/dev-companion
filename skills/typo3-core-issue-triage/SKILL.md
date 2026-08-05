---
name: typo3-core-issue-triage
description: Say what is still true about an open issue on forge.typo3.org — find the candidates in the backlog, read what the report actually claims, and establish against the core checkout you are standing in whether it still happens, was fixed in the meantime, or was never a defect. Use for going through old or untouched issues, for asking what is known about one area, for deciding whether a report is worth taking on, and for saying what a maintainer would need before it can move. Writing the patch that fixes it is a different workflow, and so is reviewing one somebody pushed.
---

# TYPO3 Core Issue Triage

Take one open issue and say what is still true about it. Keep this skill as
routing and working order; the tracker, the review server and the checkout's own
commands are lookups, and a copy of what they answer goes stale here with
nothing to report it.

Triage is not a smaller version of writing the patch. What it produces is a
statement somebody can act on — this still happens, this is gone, this was never
a defect, this cannot be settled without X — and the outcome that ends the work
early is the valuable one.

## Find the candidates

1. Work through [references/base.md](references/base.md), which fixes the order
   every task here starts in. It establishes the checkout you are standing in,
   which is what the verification below is against.
2. `typo3_forge_lookup` with `open` to get the backlog rather than one issue.
   `oldest` and `stale` are two different questions and the second is usually
   the one being asked: filed long ago is about the report, untouched for years
   is about the attention it got, and an issue filed in 2009 with a comment from
   last month is being worked by somebody. Narrow with `category` in the user's
   own words, and with `tracker`, because an old Bug and an old Feature are two
   different findings — one claims something is broken today, the other that
   something was wanted once.

   Read the count that comes back against the number of entries. A page is not
   the set, and a triage that takes thirty of two thousand for the problem has
   measured the limit rather than the backlog.

**Nothing in that list is a finding.** Age is what makes an issue a candidate
and says nothing about whether it is right; a report from 2011 can describe
behaviour the branch still has, and one from last year can be about code that
no longer exists. What separates them is the rest of this order.

## Establish what the issue claims

3. `typo3_forge_lookup` with the number, and read what comes back as a report
   rather than as a specification. Three parts of it are not in the description
   a session otherwise starts from: the **status and target version as they
   stand today**, the **relations**, which are one hop from the change that
   introduced the behaviour, and the **notes**, where a maintainer said why. Who
   it is assigned to is the fourth, and on an old issue it usually names who
   last touched it rather than who is on it — an assignee is not evidence that
   anybody is working on it, and an unassigned issue is not evidence that nobody
   minds.

Separate the three claims the report mixes before verifying any of them: what
the reporter saw, what they believed caused it, and what they wanted instead.
The first is the only one a checkout can settle. A report is regularly right
about the symptom and wrong about the cause, and verifying the cause and
reporting the issue as invalid is the most common way this work goes wrong.

Where the issue quotes a rule — an API may not be used this way, this is not
supported — verify it in the checkout rather than carrying it at the strength
the reporter put on it. Enforced in code, warned about in a docblock and advised
in prose are three different claims, and the reporter's word for all three is
the same.

## Ask what happened since it was filed

4. `typo3_gerrit_lookup` with the issue number, **before opening the checkout**.
   Its cheapest outcome is the one that ends the work: somebody has a patch up,
   and the triage is that it is under review rather than unaddressed. An answer
   of nothing is a result and a narrow one — the review server is read without a
   credential, so nothing public names the issue, which is not that nobody fixed
   it.
5. `typo3_changelog_lookup` with the words the report uses, for whether the area
   was deprecated, removed or reworked since it was filed. A rework is what
   turns a valid report into one about code that is gone, and it is also what
   makes the reproduction below fail for a reason that has nothing to do with
   the defect.

A changelog records change events, so an area nobody has touched has no entry at
all. An empty answer is not evidence that the behaviour is unchanged.

## Verify against the checkout you are standing in

Reproduce against what the branch does today, never against the version in the
report. Half of what an old issue describes is usually gone, and the half that
remains is the finding.

Establish the code path first: find the class the report is about and read
whether the behaviour it describes is still written there. That is what
separates "still happens" from "cannot happen any more, the method is gone", and
the second is a verdict that needs no reproduction at all.

`typo3_test_run_guide` with the paths you have just read says which suites can
fail on them, and whether the behaviour can be pinned by a test at all. Where it
can, a failing test is the strongest thing a triage produces: it survives being
handed to somebody else, and it is the patch's first half already written. Where
no layer can hold it — backend markup, a build step, shipped JavaScript — say so
and reproduce by hand instead, writing down the steps and what you saw.

**The core's suites are not the ones a manifest here declares, and they are not
run the way an extension's are.** They belong to the core's own runner, which no
`composer` script names, and `typo3_test_run_guide` is what gives the targeted
invocation for the paths in hand rather than a suite name to guess at.
`typo3_script_lookup` is the rest of what that runner offers — the options that
decide which PHP and which database a suite runs against, which is exactly what
an old report turns on when it says the behaviour depends on either.

Two of those decide whether a reproduction means anything, and both were already
answered by the step the base opens with:

- **Where the checkout has a DDEV project, the suites and the console run inside
  it.** The same command in your own shell runs on whatever PHP the machine
  carries and against whatever database it has, which reproduces something else
  and looks identical in the output. `typo3_project_describe` says whether this
  checkout is one of those and what the form is; take it from there rather than
  from what worked in another repository.
- **A green that ran over no files is not a green.** Where a suite reports
  success, confirm it inspected something — the count of tests or files it names
  — before reading it as the behaviour being gone. That is the failure mode a
  triage is most exposed to, because "the suite passes" is the evidence it is
  about to write a verdict on.

An old report frequently names the versions it was seen on. Those are what the
reporter had, not what it still reproduces on, and the version the suites run
against here is a property of this checkout. Say which one the verification
used; a verdict that names no version and no branch cannot be repeated.

**A reproduction that fails to reproduce is a result and not a dead end.** Say
which of the three it is: the behaviour is gone, the steps were insufficient, or
the report never contained enough to try. They lead to opposite outcomes and
they look identical in a session that only writes down "could not reproduce".

## Say what the triage found

[references/checklist.md](references/checklist.md) carries the verdicts, what
evidence each one owes, and the questions that decide between them. Read it
before writing the answer rather than after: the verdicts are not degrees of
confidence in one finding, and picking one first decides what still has to be
established.

Report what you did not establish beside what you did. A triage whose reading
stopped at the code path says so, because the next person's work is exactly the
part that was left.

This skill owns saying what is still true about an issue: choosing it out of the
backlog, reading the report against the branch, reproducing it or failing to,
and the verdict that comes out. It stops at the tracker — nothing here comments,
assigns, closes or reopens anything, and the verdict is written for the person
who will. It does not own fixing what it confirmed: where the work is to make
the change, `typo3-core-patch-development` owns it, and what crosses over is the
issue number, the verdict, the code path that was established and the failing
test where there is one. Judging a patch somebody pushed is
`typo3-core-patch-review`, which reads the diff rather than the report.

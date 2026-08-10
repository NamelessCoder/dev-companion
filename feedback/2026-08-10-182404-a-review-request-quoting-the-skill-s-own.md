---
date: 2026-08-10T18:24:04+00:00
category: idea
status: open
model: claude-opus-5
tool: typo3-core-patch-review
directory: /home/benji/projects/typo3-cms
---

# a review request quoting the skill's own trigger words did not activate the skill

## Observation

Task: review TYPO3 core Gerrit change 95163 ("[TASK] Keep the docheader navigation row sticky") in a core checkout at 15.0.0-dev, then rework it.

The opening request was, verbatim, "bitte review mir den patch [TASK] Keep the docheader navigation row sticky" — the word "review", the word "patch", and the commit subject. typo3-core-patch-review's description opens with "Review a TYPO3 core patch". I did not activate it. I ran eight ad-hoc calls first: git show --stat, git show, three repo-wide greps for the changed selectors and CSS custom properties, a git branch listing, and a failed `npm -- run build:css`. The skill only activated after the user interrupted mid-turn with "warum fragst du das review tool nicht an?".

The order matters and the skill says so: base.md fixes typo3_project_describe first because every later answer is version-filtered, and the review checklist is meant to be written down whole before the diff is read a second time. I had already read the diff and formed most of my findings before any of that ran. The findings happened to survive, but the sequence the skill exists to impose did not.

Two things plausibly contributed. The request was in German while the skill description is English, so lexical matching had only "review" and "patch" as loanwords to work with. And the request named a local commit rather than a Gerrit change or issue number, which may read as "look at this diff" rather than "run a core patch review".

This is a selection finding, not a content finding: once activated, the skill fitted the task closely and I would keep it as it stands.

## Query

User request, verbatim: "bitte review mir den patch [TASK] Keep the docheader navigation row sticky" — German, naming a local commit on a core checkout, no issue or change number.

## Suggestion

Consider whether skill descriptions can be matched against non-English requests, or whether typo3-core-patch-review's description should carry the shapes a review request actually arrives in — a bare commit subject, a local SHA, "look over this diff", "was ist an dem patch falsch" — rather than only the English noun phrase. A client that matches on description alone has nothing else to go on, and a review that starts after the diff has been read has already lost the order the skill prescribes.

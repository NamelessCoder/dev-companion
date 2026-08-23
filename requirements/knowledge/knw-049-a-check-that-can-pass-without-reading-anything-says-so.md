---
id: R-KNW-049
title: 'A check that can pass without reading anything says so'
status: held
restsOn: [D-KNW-036]
heldBy:
  - KnowledgeTest::aSuiteThatAsksGitForItsFilesNamesWhereItDoesNotHold
---

# R-KNW-049 — A check that can pass without reading anything says so

**A check whose file list comes from git carries, in the entry that offers it,
the condition under which it inspects nothing and passes anyway.**

`cglGit` and `cglHeaderGit` take the files of the last commit from
`git diff-tree`, and their script treats an empty answer as "all is well".
`runTests.sh` mounts the checkout and nothing else, so from a git worktree —
whose gitdir sits outside that mount — git fails, the list is empty, and the
suite reports SUCCESS. A session that trusted it reports a standards check as
passed having run none, and nothing in the output it keeps says otherwise.

The condition goes in the same entry as the command. A caller reads one entry
and is handed one command; nothing carries them from the entry that offers it to
a warning somewhere else in the corpus. Where a check has to be handed over
without room for the condition — the `checks` of a task intent are bare command
strings — the command is the one that holds everywhere, which for coding
standards is `cgl -n`.

This is the neighbour of
[`R-KNW-024`](knw-024-a-check-is-offered-only-where-the-command-exists.md): that
one keeps a check off a checkout that does not have the command, and this one
keeps a check that is there and green from being read as a check that ran.

## From

A core patch session in a git worktree, given `cglGit` among the checks for the
`fluid-viewhelpers` hint, watching it print `fatal: not a git repository`, then
`No PHP files to check, all is well`, then `SUCCESS` — and noticing only because
the fatal line was still on screen (2026-08-02).

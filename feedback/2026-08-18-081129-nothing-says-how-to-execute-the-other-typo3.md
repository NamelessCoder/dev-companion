---
date: 2026-08-18T08:11:29+00:00
category: tool-gap
status: open
model: claude-opus-5[1m]
tool: typo3-extension-upgrade, typo3-development-installation, typo3_project_describe
directory: /home/benji/projects/blog
---

# Nothing says how to execute the other TYPO3 major a package claims, so half of a two-major fix sh...

## Observation

Task: fix ext:blog's [blog.isPost()] / [blog.isPage()] TypoScript conditions on v14, with the explicit user requirement "they must still work in v13".

I proved the v14 half by execution: the frontend rendered the wrong template before the fix and the right one after, against the live 14.3.6 installation. I proved the v13 half by argument only. That asymmetry is the weakest part of what I delivered, and it is a knowledge gap rather than a decision I made.

The installation has one major installed. The package claims two, plus a dev constraint on a third. Nothing in this server, and nothing I could find in the repository, says how to bring the other one up so a suite can run against it. So I substituted a static compatibility argument: I verified against a core checkout that every API the fix uses exists identically on origin/13.4 — the event class byte-identical, PageInformation::getPageRecord present, the Attribute\AsEventListener class present, the container branch of GeneralUtility::makeInstance identical, and the middleware ordering such that the event still fires before condition matching. That is a good argument. It is not a test run, and I said so to the user rather than dressing it up.

What I would have needed, in the order the questions arise:

1. Whether the second major is meant to be run locally at all, or whether the project's answer is "CI does it" — the repository declares GitHub workflows, and if the matrix already covers 13.4 then the honest local answer is "push and let it run", which nobody told me either.
2. If locally: what actually changes. A composer update pinned to the other major inside the same DDEV project mutates .build/ and the installed state, so the installation is afterwards not the one the user left me. Whether that is acceptable, and how to get back, is exactly the kind of operational fact that decides whether an agent tries it or not. I did not try it, because an unrecoverable installation is a worse outcome than an unproven claim.
3. Whether the database survives a major swap in either direction, which is the thing that makes such an experiment cheap or expensive.

The scope's typo3-development-installation covers bringing the declared installation into existence and repairing one that will not boot. It does not cover standing up a second, deliberately different major beside it. typo3-extension-upgrade's description promises "proving every version it claims", which is the sentence that made me look for this in the first place — but the skill never activated (filed separately), and the description promises a proof without the procedure existing anywhere I could reach.

This is the concrete cost of that gap in one session: a two-major bugfix, committed, with one major verified by execution and the other by reading.

## Query

Would have been asked as: typo3_task_guide or typo3_project_describe for "run this extension's test suite and frontend against TYPO3 13.4 when the DDEV installation has 14.3.6 installed". Package: ext:blog, requires typo3/cms-core ^13.4.15 || ^14.3 || 15.*.*@dev; installed 14.3.6; DDEV project with .build/ vendor dir. Not called; this is the question I could not answer and worked around.

## Suggestion

A procedure — belonging to typo3-extension-upgrade, and worth its own guide under typo3://guides/extension/ — for exercising a package against a TYPO3 major other than the installed one, answering in this order: whether the repository's CI matrix already covers it, which makes the local run unnecessary; if not, the composer invocation that pins the other major, what it overwrites, and the invocation that restores the declared state afterwards; whether the database survives the swap; and which checks are worth re-running versus which are version-independent.

It should also state the honest fallback, because it is often the right answer: where the second major cannot be run, name what a static compatibility argument must cover to be worth anything — that every symbol used exists on the other branch, and that the execution order the fix relies on holds there too — and require that the unproven half be declared as unproven rather than implied.

typo3_project_describe is the natural place to surface the input: it already reports the declared commands and the environment, and the TYPO3 constraint from composer.json plus the CI matrix would tell a caller immediately whether a claimed major is covered by anything at all.

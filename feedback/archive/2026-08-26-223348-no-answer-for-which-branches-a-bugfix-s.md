---
date: 2026-08-26T22:33:48+00:00
category: missing-knowledge
status: closed
closed: 2026-08-27
model: claude-opus-5[1m]
tool: typo3_project_describe, typo3_commit_message_guide
directory: /home/benji/projects/typo3-cms
---

# no answer for which branches a bugfix's Releases: trailer should name, so I guessed from git

## Observation

Task: "please find 1 old forge issue and fix it" — ended in a patch for #76202 plus a commit message.

The commit message needs `Releases:` naming the branches the fix is meant for. AGENTS.md states the rule ("Features go to main only; bugfixes are backported to the maintained release branches") but not the *set* — which branches are maintained today is a project fact that changes over time and is nowhere in the checkout.

I did not ask this server. I ran `git branch -r` and read the remote branch list: 9.5, 10.4, 11.5, 12.4, 13.0, 13.1, 13.3, 13.4, 14.0, 14.1, 14.3, main. From that I wrote "Releases: main, 14.3, 13.4" — inferring that the highest 13.x and 14.x are the maintained ones and that the rest are historical. I flagged it to the user as unverified rather than asserting it, and told them to check before pushing.

Why I did not ask: I had already called typo3_project_describe, whose answer is about the installed version, the PHP floor, the extensions and the declared commands. Nothing in it or in typo3_commit_message_guide's description suggested either would know the *project's* release-support state, as opposed to this checkout's. I took it to be outside what a server reading one installation can see, and did not test that assumption.

The guess may well be right. But a remote branch list cannot distinguish a maintained branch from an ELTS one from an abandoned one, and getting `Releases:` wrong is the kind of thing a Gerrit reviewer sends back — so it is exactly the fact worth having authoritatively rather than plausibly.

For completeness on what else I never put to the server this session: typo3_test_run_guide (I used AGENTS.md's runTests.sh invocations directly — they worked), typo3_changelog_lookup (I decided from AGENTS.md that a crash fix with no API change owes no RST — unverified), typo3_gerrit_lookup (forge_lookup's inline `reviews` field already gave me change numbers and statuses, which was enough), and typo3_commit_message_guide itself.

## Query

Not asked of the server. Established instead with: git branch -r | head -20, in a core checkout at 15.0.0-dev. Wanted: the value of the Releases: trailer for a bugfix targeting main.

## Suggestion

Answer the maintained-branch set somewhere a patch author will actually be standing when the question arises — typo3_commit_message_guide is the natural place, since Releases: is a trailer.

What it would have to return: the branches currently taking bugfixes, distinguished from ELTS and from ended ones, with the date or source the statement is read from so a stale answer is visible as stale. Ideally phrased as the trailer itself for the change shape in hand — "bugfix from main: Releases: main, 14.3, 13.4" — because that is the string being written, and the rule without the case in front of me is what AGENTS.md already gave me.

If the server deliberately does not track project-wide release policy because it reads one installation, say so in typo3_commit_message_guide's answer rather than leaving it unmentioned. An explicit "this server does not know which branches are maintained; check get.typo3.org" would have stopped me guessing and cost nothing — I would have asked the user instead of inferring from git.

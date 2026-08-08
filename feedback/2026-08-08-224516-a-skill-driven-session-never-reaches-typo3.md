---
date: 2026-08-08T22:45:16+00:00
category: idea
status: open
model: claude-opus-5[1m]
tool: typo3_server_scope, typo3_documentation_lookup
directory: /home/benji/projects/typo3-cms
---

# a skill-driven session never reaches typo3_server_scope, so I shipped an unverified claim about w...

## Observation

Task: review Gerrit change 95179 ("[BUGFIX] Let stdWrap override apply the value 0") in a git worktree of a TYPO3 core checkout. This reports what I never put to the server, which it has no other way of learning.

I delivered a review containing the sentence, in German, that "the TypoScript Reference wording for stdWrap.override lives outside this repository", offered as the reason no documentation change is owed here. I never called typo3_documentation_lookup to check it. The assumption was that the TypoScript Reference is a separate documentation repository and therefore out of scope, and that a stdWrap property is closer to a PHP identifier than to a documented surface. It may well hold — but the patch changes the exact behaviour that page describes ("override the current value if it is not empty"), so if that page is reachable, the review should have named it as needing a follow-up, and my line was the wrong line. The assumption went into a delivered work product unchecked.

The structural reason sits one level up. typo3_server_scope is the tool that would have answered "is the TypoScript Reference covered, and by which lookup" in one call, and the server's own instructions offer it for exactly that. I never called it, and I would not have in any session run this way: the review skill routes through references/base.md, whose fixed order opens "Nothing starts until the server answers" and makes typo3_project_describe the first call and the connectivity check. server_scope appears nowhere in that order. So in a skill-driven session the orientation tool is structurally unreachable — the skill has already decided what to ask, and scope questions only arise later, at which point the order has moved on.

Two smaller ones in the same class. I read typo3_script_lookup in the deferred-tool list and passed it over, because typo3_test_run_guide had already returned every command I needed; that judgement held. And typo3_hint_lookup's answer carries an availableHints list of roughly 90 ids, which I did use — scanning it told me there is no hint covering frontend stdWrap or TypoScript rendering specifically, which is a real answer about absence that a file listing could not give. Noisy, but I would not remove it.

On resources: my client (Claude Code) renders no MCP resource list, so the only place the nine guides were named to me was the tail of typo3_project_describe's answer. I read none of them whole. I received two sections of core/contribution/commit-messages through a query and used both. The page I actually wanted and did not know to ask for by id would have been called something like "when a bugfix owes an Important entry" or "behaviour changes in a patchlevel release" — I assembled that answer from one rule section plus a precedent I found by ls in the checkout.

## Query

Never called: typo3_server_scope, typo3_documentation_lookup(query "stdWrap override" / "TypoScript Reference stdWrap"). Session task: review Gerrit change 95179 in a git worktree.

## Suggestion

Consider naming typo3_server_scope in the skills' base procedure as a conditional step — not a call every session makes, but the named move for "I am about to state that something is out of scope". As written, base.md's order has no slot for it, and its opening framing ("nothing starts until the server answers", with project_describe as the check) makes project_describe read as the orientation call it is not. Separately: if the TypoScript Reference is within typo3_documentation_lookup's reach, saying so in that tool's description would help, since a stdWrap property does not obviously read as "a documented surface" next to the examples given there (a ViewHelper, a TCA type, a TypoScript setting — the last one is the closest, but a stdWrap property is not what most readers call a setting).

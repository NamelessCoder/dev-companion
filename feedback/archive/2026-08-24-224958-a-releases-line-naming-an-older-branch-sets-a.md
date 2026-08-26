---
date: 2026-08-24T22:49:58+00:00
category: missing-knowledge
status: closed
closed: 2026-08-26
model: claude-opus-5[1m]
tool: typo3_hint_lookup, typo3_commit_message_guide, typo3_test_run_guide
directory: /home/benji/projects/typo3-cms
---

# A Releases: line naming an older branch sets a PHP syntax floor nothing states

## Observation

Task: review core change 93177 on main (Releases: main, 14.3), then amend it.

The single blocking finding of the whole review was one the server could not have given me, and it is fully general rather than specific to this patch.

The patch's functional test wrote `return new ServerRequest()->withQueryParams([...])`. That is PHP 8.4 "new without parentheses" syntax. v15 declares php ^8.5 so it parses on main. But the commit says `Releases: main, 14.3`, and v14 declares `"php": "^8.2"` — the cherry-pick to 14.3 is a parse error that kills the entire test class, not a failing test.

I proved it by hand: read origin/14.3:composer.json, appended the patch's added block to the 14.3 copy of the test file, and ran `docker run php:8.3-cli php -l`, which printed `Parse error: syntax error, unexpected token "->"`. Cross-check: `git grep -l 'new [A-Za-z]*()->' origin/14.3 -- 'typo3/sysext/**/*.php'` returns zero files, while main uses the form throughout.

The obvious fix — parenthesise — is also wrong, and finding that out cost more calls. Build/php-cs-fixer/config.php sets `'new_expression_parentheses' => true`, and reading vendor/friendsofphp/php-cs-fixer/src/Fixer/Operator/NewExpressionParenthesesFixer.php shows `use_parentheses` defaults to false, i.e. the rule STRIPS parentheses on main. I confirmed with a counterfactual `-s cgl -n` run that flagged exactly that file. The only stable form is binding to a local variable first.

What the server said, and where it stopped: typo3_hint_lookup returned the `php-versions` hint, which correctly states for v15 "The constraint is PHP ^8.5, config.platform.php is PHP 8.5.0, and the suites run on PHP 8.5 and PHP 8.6". That is right and it stopped exactly one step short — it never connects the version a patch is WRITTEN on to the versions its Releases: trailer commits it to. typo3_commit_message_guide validates the Releases: line against maintained branches and says nothing about syntax level. typo3_test_run_guide names the suites and not this.

This is not a niche trap. Every core patch on main today that touches PHP and is backported to 14.3 or 13.4 is exposed to it, and the failure surfaces at cherry-pick time in front of whoever does the backport rather than in CI on main.

## Query

typo3_hint_lookup(paths=["typo3/sysext/backend/Tests/Functional/Controller/Wizard/LocalizationControllerTest.php"], task="PHP version compatibility of test code that has to be backported to an older release branch", targetVersion="15.0"); typo3_commit_message_guide(workflow="core", changeType="BUGFIX", message=<ps10 message carrying "Releases: main, 14.3">)

## Suggestion

Given a Releases: trailer, state the lowest PHP version the diff must parse under, and what that forbids.

Best home is typo3_commit_message_guide, because it already reads the Releases: line and already holds it against the branches that take a patch today. One added check: "Releases: names 14.3; the diff must parse on PHP 8.2. PHP 8.4+ syntax (new X()->method(), asymmetric visibility, property hooks) makes the backport a parse error."

Second home is the php-versions hint, which should carry the backport sentence next to the per-major constraints it already lists: "A patch written on main and backported is bound by the floor of the OLDEST branch its Releases: trailer names, not by main's."

And the php-cs-fixer half is worth its own line wherever the first lands, because it removes the obvious workaround: main's Build/php-cs-fixer/config.php enables new_expression_parentheses with use_parentheses defaulting to false, so `(new X())->m()` is rewritten to `new X()->m()` by -s cgl. Backport-safe code binds the instance to a variable first.

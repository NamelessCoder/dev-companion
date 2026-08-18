---
date: 2026-08-18T08:07:43+00:00
category: idea
status: closed
closed: 2026-08-18
model: claude-opus-5[1m]
tool: typo3_server_scope
directory: /home/benji/projects/blog
---

# What worked: doesNotCover was accurate enough to confirm a session was right not to call the server

## Observation

Task: fix ext:blog's [blog.isPost()] / [blog.isPage()] TypoScript conditions, broken on TYPO3 v14, while keeping them working on v13. This records the positive finding that was truncated out of the companion note "A whole session ran with zero calls because a multi-branch core checkout outcompeted the server".

That session made no calls to this server during the work. When I finally read typo3_server_scope afterwards, the interesting result was that it agreed with me.

Its doesNotCover entry for "PHP source as code: a method signature, whether a class or member is @internal or public API, an implementation to copy" — with "instead: Read the class" — describes almost exactly what the session consisted of: establishing that TypoScriptFrontendController no longer exists on 14.3, where $GLOBALS['TYPO3_REQUEST'] is assigned on each branch, what the condition matcher hands to conditions, and whether an event class is identical on 13.4 and 14.3. A scope document that says plainly "this is not mine" is worth more than a lookup that would have half-answered it, because the half-answer is the one that gets believed.

The same call also confirmed the installation discovery was correct without my having asked: root /home/benji/projects/blog, kind composer-project, console reachable via ddev at PHP 8.2. That matched what I had worked out by hand from .ddev and composer.json.

Two further things that the scope answer got right and that made this debrief possible at all: every covered topic names the tool that owns it and the source it answers from, so I could tell which of my Bash detours had a tool behind them (typo3_changelog_lookup did; the middleware-order questions did not). And the answersFrom block distinguishes installation, packages, knowledge, network and checkout, which is what let me judge that a cross-major question was never going to be answered from an installation-bound source.

Keep all of that. The temptation with a scope document is to widen the covers list and soften doesNotCover; the value here was entirely in how firm the exclusions are.

Two smaller notes belonging to the same truncated section. My client never listed the typo3://guides resources — I learned they exist only from this scope call, after the work was done, which is a second angle on the already-open note about the guides list being visible only from inside a tool answer; a session that calls nothing sees it under no tool at all. And no tool of this server errored during the session, because none ran; the only failures were mine in Bash — a heredoc into a directory I had not created, and an unquoted --filter A|B whose pipe the shell ate inside ddev exec.

## Query

typo3_server_scope, called once at the end of a session as part of a debrief, in /home/benji/projects/blog (ext:blog, typo3/cms-core v14.3.6). Recovering material cut from feedback/2026-08-18-080710 by the 4000-character observation limit.

## Suggestion

Do not soften doesNotCover to make the server look broader. The specific entries that earned their keep here were "PHP source as code" and the answersFrom split by source; both should survive any future rewrite.

If anything, extend the pattern: several covers entries would be more useful with an explicit "and not X" clause of the same firmness, so a caller can rule a tool out as fast as in.

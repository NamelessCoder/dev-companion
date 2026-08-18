---
date: 2026-08-18T08:11:00+00:00
category: missing-knowledge
status: open
model: claude-opus-5[1m]
tool: typo3_rule_lookup, typo3_hint_lookup, typo3_task_guide
directory: /home/benji/projects/blog
---

# No answer for proving a TypoScript condition verdict against a running frontend, and the two near...

## Observation

Task: fix ext:blog's [blog.isPost()] / [blog.isPage()] TypoScript conditions on v14 without breaking v13. The user asked me to validate the breakage before fixing it, so proving the verdict — first false, then true — was half the job.

A condition verdict is invisible. It emits nothing, logs nothing, and its only trace is that a different TypoScript branch was applied. So the proof has to be indirect: find something in the rendered output that only the conditional branch produces, and diff it before and after.

I worked that out from scratch and got it wrong once. The condition swaps page.10.templateName from BlogList to BlogPost, so I grepped the rendered HTML for a class from the BlogPost template — "blogcontainer" — and got three hits, which looked like proof that the condition matched. It was not: BlogList carries the same wrapper markup. I had to open both templates, find what actually differs, and re-grep for the plugin frames only BlogPost renders (frame-type-blog_header, blog_comments, blog_authors, blog_relatedposts) against BlogList's frame-type-blog_posts. That detour cost about two round trips and, worse, briefly produced a false positive I could have shipped.

The whole proof cluster ran about seven round trips: ddev describe, a query for pages with the blog doktypes, a curl that 404'd because I guessed the slug from the wrong parent, reading config/sites to find the right root, the successful curl, and then the two spent discriminating the marker.

The generalisable knowledge, none of which is anywhere here:

1. A condition verdict is only observable through what the conditional branch changes, so the first step is diffing the two branches to find a discriminating marker — not grepping for anything that looks related.
2. Where the branch swaps a Fluid template, markup shared by both templates is not a marker. This is the trap, because the shared markup is usually the wrapper you would grep for first.
3. Condition verdicts are part of the page cache identifier, so the check needs a cache flush between runs; I did flush, but by habit rather than because anything told me to.

The two nearest guides in the scope are both about something else. typo3://guides/core/testing/proving-a-rendering is the closest by name and is scoped "core" and aimed at what parseFunc does to a snippet — a throwaway functional test that renders one snippet and prints it. That is a different question from whether a condition fired. typo3://guides/any/testing/browser-check covers reaching a DDEV site from a container with a browser, which is about screenshots rather than about what to look for. I used curl and string comparison and needed neither.

I never saw either guide during the work; my client did not list the typo3:// resources at all.

## Query

Would have been asked as: typo3_rule_lookup or typo3_hint_lookup for "prove that a TypoScript condition matches in the frontend", targetVersion 14.3, in /home/benji/projects/blog with DDEV up and demo content present. Not called; reconstructed from the ~7 round trips the session actually spent on it.

## Suggestion

A guide, scope "any" — typo3://guides/any/testing/proving-a-condition, or a section inside proving-a-rendering that is not core-scoped — covering how to establish that a TypoScript condition fired against a running installation: derive a discriminating marker by diffing what the conditional branch changes against what it replaces; the specific trap that two Fluid templates usually share their wrapper markup, so the obvious grep proves nothing; flush the page cache between runs because verdicts feed the cache identifier; and the negative control, i.e. also check a page the condition must not match, which is what turns one green result into evidence.

The existing proving-a-rendering guide is the right neighbour to file it beside, but it needs to stop being core-only: an extension author proving a condition or a template swap has exactly this problem and the scope currently routes them to a core testing document.

---
date: 2026-08-17T21:13:06+00:00
category: idea
status: open
model: claude-opus-5
tool: typo3_hint_lookup
directory: /home/benji/projects/site-demo
---

# the closing neighbour sentence is read at the moment the caller already has what it came for, and...

## Observation

Task: build a TYPO3 v14 demo site as a sitepackage plus a distribution extension carrying the content, on TYPO3 14.3.6. The user asked me why I had not called certain hints, so I traced the actual decision points in my own transcript rather than guessing. This is what I found, and it is a measurable property of the answers rather than a story about me.

Most hints close with a sentence naming their neighbours. That sentence is the server's own routing mechanism, and it sits at the very end of the answer — at the moment the caller has just got what it asked for and is turning back to the work. Across this session I followed roughly half of those branches, and the ones I dropped are the ones that cost:

- project-repository-layout closes with "What goes around the site rather than in it is project-build-and-scripts, and what the installation is configured by is project-configuration-files." Two ids, one sentence. I fetched project-configuration-files and not project-build-and-scripts. The same pair is named in typo3-development-installation step 5, and I split it the same way there. The skipped one held the answers to five later review findings.
- frontend-page-rendering closes with "page-content-areas for addressing a column, page-variables-and-processors, page-cache-headers". I fetched none of them at the time, wrote the page templates, and got HTTP 500 on every page from f:render.contentArea. I then fetched page-content-areas to diagnose it — the same id, three calls later, after the failure.
- content-element-preview closes with the browser-tests reference for asserting what an editor actually sees. Not fetched. The backend previews shipped unverified.
- sitepackage-layout named three; I took two. site-sets named three; I took none. sitepackage-initial-content named three; I took two.

The mechanism is consistent and it is not forgetfulness. I fetched hints in batches of four, chosen for what I was about to write in the next few minutes. A batch has to be composed before the writing starts, so the selection is made on anticipated need — and a neighbour whose relevance would only become obvious while writing never enters the batch. project-build-and-scripts lost on exactly that basis: at the moment I read its name, Build/ did not exist and the project had no scripts, so "what goes around the site" read as not-yet-relevant. It became relevant forty files later, when nothing was re-raising it.

So the branch is offered at the point of lowest receptiveness: after the answer has landed, before the work that makes the neighbour matter has started. The two cases that cost the most — project-build-and-scripts and page-content-areas — were both named in a closing sentence I read and both fetched too late or not at all.

I am reporting this because the server sees which ids were fetched and cannot see which ones were offered and declined, or in what order relative to the work.

## Query

Count, across a long build session, how many of the neighbour ids named in the closing sentence of each typo3_hint_lookup answer were subsequently fetched. Session: 22 hint_lookup calls on TYPO3 14.3.6 building a sitepackage plus a distribution.

## Suggestion

Give the neighbour reference a reason to be taken now rather than only a name. The closing sentences that worked on me were the ones carrying a consequence — sitepackage-templates' layout-collision warning made me fetch and act immediately, because it said what breaks. The ones that read as a table of contents ("its neighbours: A, B, C") were skipped. Saying what the neighbour prevents rather than what it covers would change the calculation at the moment it is read: "page-content-areas for addressing a column — handed a missing area, f:render.contentArea throws rather than rendering nothing" is a branch a caller takes before writing the template, not after the 500. Where a neighbour only matters at a later stage of the work, saying so is worth as much as the name, since the caller can then schedule it instead of dropping it.

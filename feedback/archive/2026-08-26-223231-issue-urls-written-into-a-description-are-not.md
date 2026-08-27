---
date: 2026-08-26T22:32:31+00:00
category: tool-gap
status: closed
closed: 2026-08-27
model: claude-opus-5[1m]
tool: typo3_forge_lookup
directory: /home/benji/projects/typo3-cms
---

# issue URLs written into a description are not lifted out, though review URLs are

## Observation

Task: "please find 1 old forge issue and fix it", narrowed by the user to Extbase. I settled on #76202 and shipped a patch for it.

typo3_forge_lookup(issue="76202") returned description, all 3 comments, relations: [] and reviews: []. I read the comments and acted on them. What I skimmed past is that the description's *first line* reads "seems bug from 6.2 version https://forge.typo3.org/issues/62553 is still in typo3 7.6 version". That URL is not a Redmine relation, so it appeared nowhere in the structured output — relations was genuinely empty. It surfaced only when the user pushed back and I re-read the description text by eye.

#62553 turned out to matter: it is a *different* bug (ObjectStorage::offsetExists() reached from Fluid `{product.images.0.title}`, not attach() on the property-mapping write path), and it was fixed in 2014 by 279d0ac91f9, with the guard still standing at ObjectStorage.php:164. So the reporter's framing — "same bug, still not fixed" — was wrong, and I had been one prompt away from repeating that framing in a commit message.

The sharp point: the tool already parses one class of URL out of free text. The `reviews` field is populated by scraping review.typo3.org links out of description and comments — that is why I could see ABANDONED changes on #70921, #72962, #82228, #52070 without asking Gerrit. It does that work for one host and not for its own. A forge.typo3.org/issues/NNN URL in the same prose is dropped.

This is a low-confidence-to-high-confidence gap: I had no signal that anything was missing. relations: [] reads as "nothing linked", and I believed it.

## Query

typo3_forge_lookup(issue="76202") — then, only after the user asked "did you read all forge comments carefully?", typo3_forge_lookup(issue="62553")

## Suggestion

Scrape forge.typo3.org/issues/NNN URLs out of description and comment text the same way review.typo3.org URLs already are, and return them in a field distinct from `relations` — e.g. `mentioned: [{issue, subject, status, where: "description"|"note"}]`, carrying at least subject and status so a reader can see "Closed" without a second call.

The distinction from `relations` is worth keeping rather than merging: a Redmine relation is somebody's deliberate triage, a URL in prose is the reporter's own claim about prior art, and on an old issue the second is more often the load-bearing one. Marking `where` would let a reader weigh "the reporter said this is a duplicate of X" against "a maintainer linked X".

Cheap partial version if the scrape is unwanted: have the issue answer state a count — "description and notes mention 1 other forge issue" — which is enough to stop a reader trusting relations: [] as the whole picture.

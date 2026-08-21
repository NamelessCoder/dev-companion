---
date: 2026-08-19T09:44:03+00:00
category: idea
status: closed
closed: 2026-08-21
model: claude-opus-5[1m]
tool: typo3_changelog_lookup
directory: /home/benji/projects/blog
---

# The bounded deprecation sweep costs one call per tag per major — eleven round trips for one question

## Observation

Task: full audit of the blog extension before its v14 release. The extension-health skill's base.md prescribes the sweep: typo3_changelog_lookup with type=deprecation, bounded by tag, query omitted, at each major the package declares. I followed it exactly and it produced good findings — but it cost eleven calls for one question.

The calls, in order, all at version=14 type=deprecation: ext:core (30 entries), ext:extbase (3), ext:backend (19), ext:frontend (4), ext:fluid (5), ext:form (6), TCA (12), TypoScript (3), Fluid (5), TSConfig (1), ext:install (2).

Of those eleven, four returned nothing I used. TSConfig returned a single entry (doktypesToShowInNewPageDragArea) the extension does not touch. TypoScript and Fluid returned entries I had already seen under ext:core and ext:fluid — the surface tags and the extension tags overlap heavily, so the later calls were largely re-reads. ext:install returned two entries, one of which I already had from the phpstan baseline.

The base is right that tags are the axis and that the extension's own vocabulary is not among them. It is also right that every call returns the full tag list, so calls 2..11 were guided rather than guessed — that part works well. What is missing is that the sweep is one question, not eleven, and the caller knows the whole tag set after the first answer.

The base also says this is "the most expensive step of the order", which is accurate, and it is the reason it gives for the narrow skip condition. So the cost is known; the sweep just has no cheaper shape.

Worth noting what the sweep could not do at all: I still had to verify every returned identifier against the checkout myself (correct — a deprecation nothing calls is not a finding), and separately establish, per entry, whether the migration target exists on the package's other declared major. I have filed that second one separately.

## Query

typo3_changelog_lookup version=14 type=deprecation limit=50, repeated with tag= ext:core, ext:extbase, ext:backend, ext:frontend, ext:fluid, ext:form, TCA, TypoScript, TSConfig, ext:install, Fluid

## Suggestion

Let tag take a list. typo3_changelog_lookup version=14 type=deprecation tag=["ext:core","ext:extbase","ext:backend",...] returning the union, deduplicated, each entry carrying the tags it matched. That turns eleven round trips into one and removes the re-reads, and it changes nothing about how the sweep is bounded — the caller still picks the tags off the package's own surfaces.

If a list is not wanted, the alternative is a sweep mode that takes the package instead of the tags: given an extension key that typo3_project_describe reports, return every deprecation at the declared majors whose tag matches a surface that extension actually ships. The extension key matches no tag, correctly — but typo3_extension_describe already knows which system extensions it requires, which file kinds it ships and which tables it touches, so the server can pick the tags the base currently asks the caller to pick.

Either way, keep returning the full tag list in the answer. That is what made the manual sweep tractable at all.

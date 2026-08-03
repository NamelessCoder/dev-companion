---
date: 2026-08-02T14:50:43+00:00
category: missing-knowledge
status: closed
closed: 2026-08-03
model: claude-opus-5[1m]
tool: typo3_task_guide, typo3_architecture_lookup
directory: /home/benji/projects/typo3-cms
---

# Task: assess Forge #105403 and fix it. The single reason my first assessment was wrong is that I ...

## Observation

Task: assess Forge #105403 and fix it. The single reason my first assessment was wrong is that I had not internalised the context of a content management system, and I want that recorded plainly, because it is a model-behaviour finding this server can act on.

I reasoned about the report as an API-usage question: is the value passed to f:image's "src" argument of the type that argument accepts? Answered that way the reporter is wrong, the maintainers who closed it are right, and the correct deliverable is a clearer exception. That is exactly what I produced first, and I committed it.

The question a CMS makes you ask instead is what happens to the editor and the visitor. Content changes — that is the premise of the product. An editor replaces an image in the file module; the system must not go on serving the old one. Seen that way the report is not a misuse at all: the reporter had wrapped f:uri.resource inside f:image precisely to get a cache buster, because f:image did not produce one. The exception was a symptom of a missing capability, and the missing capability was the system failing at its core job.

The same blind spot produced three further wrong turns the user had to correct:

- I proposed a useCacheBusting argument so the new behaviour could be switched off. In a CMS, serving a stale asset after an editor replaced it is a defect, not a preference; nobody offers an option to omit the processing checksum from a csm_<hash>.jpg file name either. The user rejected optionality on principle and was right.
- I accepted "use f:image for FAL only, render package resources as a plain img tag" as a workable answer. To an editor and a visitor a logo is a logo; where the file happens to live is an implementation detail. Forcing the template author to branch on provenance is the confusion, not the fix. The user made this concrete with the ordinary sitepackage case: a logo from the site configuration when maintained, the shipped one otherwise.
- I let the analysis drift into redesigning the FAL fallback storage while the reported defect stood unfixed, and had to be pulled back with "wir geben uns nicht damit zufrieden es einfach abzutun".

Every fact I needed was in the catalogue or the checkout. What was missing was the premise that decides which facts matter.

## Query

Whole session: assess Forge #105403 ("f:image and cache busting issue"), decide whether it is valid, and fix it. typo3_project_scope; typo3_task_guide task="Fix f:image ViewHelper failing when src contains a cache busting query string produced by f:uri.resource", changeType=bugfix, area=fluid, targetVersion=15.0

## Suggestion

Carry the product premises next to the API facts, because they are what a session judges a bug report against and it will otherwise judge by argument types. Concretely for this area: content is mutable, so any URI pointing at an unaltered original is a caching defect waiting to happen, and correctness of caching is not a configurable preference; an editor and a visitor do not distinguish a FAL image from a shipped package resource, so an answer that forces template authors to branch on provenance is a workaround rather than a fix; and where the same outcome has two APIs, the inconsistency between them is itself the bug. Placing this in the bugfix checklist of typo3_task_guide — assess the report by the outcome for editor and visitor before assessing the code that was written — would have changed my first assessment and saved the whole detour.

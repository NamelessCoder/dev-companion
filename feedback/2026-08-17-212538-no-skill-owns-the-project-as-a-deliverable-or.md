---
date: 2026-08-17T21:25:38+00:00
category: tool-gap
status: open
model: claude-opus-5
tool: typo3_task_guide, typo3-development-installation, typo3-content-element-development
directory: /home/benji/projects/site-demo
---

# no skill owns the project as a deliverable or the packaging of content as a distribution, and bot...

## Observation

*Trimmed on 2026-08-18, and the title is left as it was filed. The first of the
two skills this asks for is already written:
`typo3-development-installation` step 5 is "Decide what the install wrote into
the repository", it routes to `project-configuration-files` and
`project-build-and-scripts`, and it says the ignore rules follow from both
answers and are written before the first commit. The rest of that half has
owners too — Playwright, PHPUnit and php-cs-fixer are
`typo3-extension-testing`'s by its own description, the untested PHP floor is
`typo3-extension-upgrade`'s "proving every version it claims", and the container
declaration is the installation skill's. None of them arrived, which is
delivery rather than a gap and is what `2026-08-17-213027` reports. `D-SKL-050`
carries the reading. What is left below is the half nothing owns.*

Task, verbatim from the user: build a TYPO3 site using custom content elements,
delivered as an installable sitepackage with the content maintained as a
distribution. On TYPO3 14.3.6, from an empty directory.

typo3_task_guide, given that task text in full, answered scope "extension" and
named exactly one skill: typo3-content-element-development. It did not route to
typo3-development-installation — I activated that myself from the client's skill
list because the directory was empty and I could see I needed it.

What no skill owns is packaging content as a distribution. This has a real
sequence — seed the content with DataHandler because nothing exists to export
yet, export it with the right table and relation flags, place the artifact and
its files directory in the package, ship the site configuration through
Initialisation/Site rather than inside the export, then prove the whole thing on
a clean install. I assembled that from sitepackage-initial-content,
impexp-artifact, initial-content-references, initial-content-import-once and
datahandler-seeding, plus trial and error. Each of those hints is correct about
its own piece and none of them owns the order or the proof. Two of the three
incorrect answers I hit in this entire session were in that stretch, and both
were silent failures: an export that ships no images and reports success, and a
filename argument that is ignored while success is reported against a different
path.

The facts are present and version-bound; what is absent is a workflow that puts
them in an order and says what the finished thing owes. That is precisely what
the existing skills do well for an installation and for a content element.

## Query

typo3_task_guide task="Build a TYPO3 site from scratch: development
installation, a sitepackage extension with custom content elements, and a
distribution extension carrying the content" changeType=feature — see which
scope and which skills come back.

## Suggestion

One skill for distribution packaging — the seed-export-place-prove sequence
above, which currently exists only as five hints that each know one step. The
knowledge is already written; what is missing is the ordering and the terminal
check. Worth also looking at why task_guide answered scope "extension" to a task
text naming a site, a sitepackage and a distribution — if project-scoped work
cannot be recognised from the request, a project skill would not be reached even
once it exists.

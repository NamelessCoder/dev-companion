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

Task, verbatim from the user: build a TYPO3 site using custom content elements, delivered as an installable sitepackage with the content maintained as a distribution. On TYPO3 14.3.6, from an empty directory. The published skills carried two thirds of that and there is no owner for the rest.

typo3_task_guide, given that task text in full, answered scope "extension" and named exactly one skill: typo3-content-element-development. It did not route to typo3-development-installation — I activated that myself from the client's skill list because the directory was empty and I could see I needed it. And nothing at all covers the two units the brief actually asked for.

What no skill owns, first: the project repository as the thing being delivered. The Composer root package, the layout keys, the ignore rules, which scripts a colleague runs and what they are named, where the seeding and test tooling lives, and what has to be true before the whole is called finished. The knowledge for this exists and is good — project-repository-layout, project-build-and-scripts and project-configuration-files all carry scope "project" — but nothing sequences them, so which of the three you fetch is left to whichever one your current file makes you think of. I fetched two of the three and wrote the ignore file from memory before installing. When the user reviewed the result, seven of his ten findings were in that unowned territory: the incomplete ignore file, no Playwright, no PHPUnit or php-cs-fixer or editorconfig, a composer script wrapping a shell file, a seeding script left in place with no decision about its lifecycle, an untested PHP floor, and a container PHP version chosen from memory.

What no skill owns, second: packaging content as a distribution. This has a real sequence — seed the content with DataHandler because nothing exists to export yet, export it with the right table and relation flags, place the artifact and its files directory in the package, ship the site configuration through Initialisation/Site rather than inside the export, then prove the whole thing on a clean install. I assembled that from sitepackage-initial-content, impexp-artifact, initial-content-references, initial-content-import-once and datahandler-seeding, plus trial and error. Each of those hints is correct about its own piece and none of them owns the order or the proof. Two of the three incorrect answers I hit in this entire session were in that stretch, and both were silent failures: an export that ships no images and reports success, and a filename argument that is ignored while success is reported against a different path.

The shape is the same in both cases. The facts are present and version-bound; what is absent is a workflow that puts them in an order and says what the finished thing owes. That is precisely what the existing skills do well for an installation and for a content element.

## Query

typo3_task_guide task="Build a TYPO3 site from scratch: development installation, a sitepackage extension with custom content elements, and a distribution extension carrying the content" changeType=feature — see which scope and which skills come back.

## Suggestion

Two skills would close it. One for the project as a deliverable — call it what a caller would search for, the repository rather than the installation, since typo3-development-installation already owns booting and installing and stops at the site answering: it would sequence the three project-scoped hints, own the ignore rules at the moment the install reveals what it generated, own the script names and the Build/ contents, and end in an acceptance list. One for distribution packaging — the seed-export-place-prove sequence above, which currently exists only as five hints that each know one step. Both are areas where the knowledge is already written; what is missing is the ordering and the terminal check. Worth also looking at why task_guide answered scope "extension" to a task text naming a site, a sitepackage and a distribution — if project-scoped work cannot be recognised from the request, a project skill would not be reached even once it exists.

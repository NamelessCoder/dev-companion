---
date: 2026-08-17T21:11:41+00:00
category: missing-knowledge
status: open
model: claude-opus-5
tool: typo3_hint_lookup
directory: /home/benji/projects/site-demo
---

# nothing enumerates what a Composer install writes into the document root, so an ignore file writt...

## Observation

Task: build a TYPO3 v14 demo site as a sitepackage plus a distribution extension carrying the content, on TYPO3 14.3.6. A reviewer's first finding was that my .gitignore is incomplete.

It is. public/ on the finished installation holds .htaccess, _assets, _assets_install, fileadmin, index.php and typo3temp. My ignore file names /public/index.php, /public/typo3/ and /public/_assets/. It does not name _assets_install or typo3temp, and neither the generated .htaccess in public/ nor the one in var/. Every one of those is written by the installer or by TYPO3 at runtime and none of them belongs in a commit.

Two reasons it came out that way, and only the second is the server's. First: I wrote the file from memory before running the install, so I could not see the directories — that is mine. Second: nothing I could have called would have listed them.

I checked three hints after the review. public-assets is precise about the mechanism — Resources/Public/ is published below _assets/<hash>/ in the document root, asset:publish, the filesystemPublishingType choice between linking and copying — and names _assets. It never mentions _assets_install, which sits right beside it and is what the installer writes. project-build-and-scripts is explicit that "the .gitignore is where the answer is read off" for the built-frontend-assets decision, and correctly separates var/ and the document root as places a script must not go, but lists no ignore entries. project-configuration-files covers config/system exactly and stops there.

So the ignore set is treated as derivable from three hints that each cover one slice, and the union of them still misses two directories that exist in every Composer installation. The failure is quiet in the worst way: nothing errors, the repository just carries generated files, and on this project it would have carried typo3temp — which holds compiled Fluid templates and the concatenated CSS the frontend links.

The timing makes it worse than a lookup miss. The ignore rules have to be written before the first commit, which is before or during the install; the directories only appear after it. A caller who writes them at the prescribed time cannot observe the answer, and a caller who writes them later has usually committed already.

## Query

typo3_hint_lookup id=public-assets targetVersion=14; typo3_hint_lookup id=project-build-and-scripts targetVersion=14; typo3_hint_lookup id=project-configuration-files targetVersion=14 — then compare what they name against `ls -a public/` on an installed TYPO3 14.3.6 Composer project

## Suggestion

Name the set. A short enumeration in project-build-and-scripts of what a Composer installation generates below the document root and the project root — index.php, typo3/, _assets/, _assets_install/, typo3temp/, the generated .htaccess files, var/, vendor/, fileadmin/_processed_/ — turns "the .gitignore is where the answer is read off" into something a caller can actually read it off. It is stable across a major and does not depend on the installation, so it can be answered before anything is installed, which is when it is needed. If it belongs anywhere else, public-assets is the natural home for the _assets / _assets_install pair, since it already explains the first of the two and a reader who has just learned about _assets has no reason to suspect a sibling.

---
date: 2026-08-18T07:14:35+00:00
category: idea
status: closed
closed: 2026-08-18
model: claude-opus-5[1m]
tool: typo3-development-installation, typo3_hint_lookup
directory: /home/benji/projects/blog
---

# the hint answering the extension-repository layout is filed under the branch a booting caller dec...

## Observation

Task: boot the local DDEV development installation of the t3g/blog extension repository from a clean checkout. Follow-up to my earlier feedback on the skill's two shapes; this is the concrete cost of that structure, which I did not report at the time.

The skill files hint id=extension-repository-installation under "Create one where none is declared", step 1, and describes it as covering "which keys move the installation out of the way and where the console lands under them, which layout key is accepted and then reported rather than honoured, which package the core brings that a root constraint for it cannot resolve, and why the extension directory below the document root is empty rather than broken."

That last clause is exactly the question I hit. I decided I was in the other branch — the repository declares a container, so I took "Boot what the repository already declares" — and therefore never fetched the id, because it was presented as belonging to a branch I was not in. Then, after `ddev composer install`, I probed the layout by hand and my probe failed:

  ls -la .build/public/typo3conf  →  No such file or directory

I had expected a typo3conf/ext/blog symlink for the root extension. On TYPO3 v14 with cms-composer-installers v5 there is none; extensions stay in vendor and the root package is registered from there. I resolved it by reasoning from the successful `typo3 extension:setup` output, which listed "blog" among the extensions set up, rather than by reading anything authoritative. The hint would have told me directly, and it is the difference between "the layout is fine" and "the install is broken" at a moment when nothing else in the session says which.

The same call also carried a failing probe for whether bk2k/bootstrap-package ships an Initialisation/ directory, and I re-ran that as a `find` in the next round trip. Two failed shell probes and one repeat, all inside the subject of a hint the skill had named and filed out of my reach.

The general shape: the skill's branches are decided by the repository, but the hint ids underneath them are needed by task. extension-repository-installation is about how an extension repository is laid out once TYPO3 is installed beneath it — which is true whether that installation was created in this session or booted from a declaration. Filing it under "create" makes it invisible to every caller who correctly took the "boot" branch, and those callers are looking at the same .build/ layout.

## Query

Skill typo3-development-installation, section "Create one where none is declared", step 1, which names typo3_hint_lookup id=extension-repository-installation. That id was never fetched this session. Instead: `ls .build/public/typo3conf` (failed, no such directory), `ls .build/vendor/bk2k/bootstrap-package/Initialisation` (failed), `ls .build/public`.

## Suggestion

Move extension-repository-installation out of the branch, or name it in both: any caller working in an extension repository whose TYPO3 lives beneath it needs the layout facts regardless of which branch brought them there. Concretely, add to the boot branch a line such as "where the repository is an extension with TYPO3 installed beneath it, typo3_hint_lookup id=extension-repository-installation is what says the layout is right — in particular why there is no typo3conf/ext below the document root on a Composer installation, which otherwise reads as a failed install." Generally: the skill's branch headings currently gate hint ids by how the installation came to exist, when what decides relevance is what the repository is.

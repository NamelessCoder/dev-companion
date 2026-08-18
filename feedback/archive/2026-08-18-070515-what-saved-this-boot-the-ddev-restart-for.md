---
date: 2026-08-18T07:05:15+00:00
category: idea
status: closed
closed: 2026-08-18
model: claude-opus-5[1m]
tool: typo3_task_guide, typo3_hint_lookup
directory: /home/benji/projects/blog
---

# what saved this boot: the ddev restart for additional.php, the distribution test, and the checkli...

## Observation

Task: boot the local DDEV development installation of the t3g/blog extension repository. Recording what worked, concretely, because it is what must not be broken later.

1. The DDEV additional.php ordering trap — the single most valuable line in the session. The checklist said: DDEV reads vendor/typo3/cms-core/Classes/Information/Typo3Version.php to decide whether the file belongs there at all, so the `ddev start` that necessarily precedes `composer install` writes no additional.php, and every request then answers exception 1396795884 for the trusted hosts pattern while `typo3 setup` reports success. That is exactly what my sequence would have hit: `ddev start` on a clone with no .build/ printed "The project docroot does not yet exist" and wrote nothing. I ran `ddev restart` after `ddev composer install` on the strength of that line alone, and it printed "Generating additional.php file for database connection." The file then carried the DB connection, the ImageMagick paths, Mailpit, and SYS/trustedHostsPattern. Without the hint I would have run setup, curled, received a trusted-hosts exception from a green install, and spent several round trips hunting a cause that is in DDEV's detection order rather than in TYPO3. This is the paragraph to protect.

2. The distribution test, stated as a test rather than as a fact. installation-setup says --distribution and --create-site are inert in Composer mode as soon as any required package ships Initialisation/data.xml or data.t3d, and that shipping such a file "is the whole test for being one". The repository has bk2k/bootstrap-package 16.0.0 in require-dev, which I might well have assumed was a distribution. Because the hint gave me a decidable test I ran `find .build/vendor -maxdepth 3 -type d -name Initialisation`, got nothing, kept --create-site, and it worked: config/sites/main/config.yaml was written with base https://blog.ddev.site/ and rootPageId 1 on TYPO3 14.3.6. A hint that had merely asserted "on v14 --create-site is inert" would have sent me down the wrong branch.

3. The password checklist. Four lines that changed what I did and what I reported: generate the password rather than inventing a quiet default; state username, password and the backend URL in the reply because the setup command prints neither; the same value silently becomes BE/installToolPassword; neither is stored reversibly, so recovery is backend:resetpassword and not a lookup. I generated one, reported it with the URL, and told the user it is also the install-tool password. I would not have said the third thing on my own — it is not visible anywhere in the setup output.

4. "Leave config/system/additional.php to DDEV where DDEV writes it" stopped me hand-writing a database connection into it, which I had been about to consider when the first `ddev start` produced no file.

5. installation-setup's exception numbers (1669747685 for an existing settings.php, 1669747200 for a database with tables, --force rewriting settings and never schema) did not fire this session, but they are what told me the order was setup → extension:setup → cache:flush and that a re-run needs its own guard on settings.php. They shaped the sequence rather than rescuing it.

Net: four MCP calls total (project_describe, hint_lookup id=installation-boot, task_guide, project_describe again) carried a boot that succeeded end to end — frontend and backend both 200, admin row present, git status clean. The failures I filed separately are all at the edges of this; the core of it worked.

## Query

typo3_hint_lookup id=installation-boot, then typo3_task_guide changeType="operations" paths=[".ddev/config.yaml","composer.json","blog"] — the checklist and hints those two returned, against a clean clone of github.com/TYPO3GmbH/blog on DDEV v1.25.1.

## Suggestion

Keep the installation-boot hint and the operations checklist as they are, in particular the Typo3Version.php/additional.php ordering paragraph, the "shipping Initialisation/data.xml is the whole test" phrasing, and the four password lines. If the operations checklist is ever shortened, these are the entries whose absence costs a session real round trips. The pattern worth generalising from item 2: state the decidable test alongside the conclusion, so the caller can check the branch rather than inherit it.

---
date: 2026-08-18T07:04:23+00:00
category: missing-knowledge
status: closed
closed: 2026-08-18
model: claude-opus-5[1m]
tool: typo3_hint_lookup, typo3_task_guide
directory: /home/benji/projects/blog
---

# the hints name the TYPO3_SETUP_ variables but nothing says how they reach a console command insid...

## Observation

Task: boot the local DDEV development installation of the t3g/blog extension repository, which meant running `typo3 setup` unattended inside the DDEV web container.

The knowledge stops one step short of the command line. environment-runtime-readers and installation-setup tell me, correctly and usefully, that setup reads TYPO3_DB_DRIVER/HOST/PORT/DBNAME/USERNAME/PASSWORD, TYPO3_SETUP_ADMIN_USERNAME/PASSWORD/EMAIL, TYPO3_SETUP_CREATE_SITE, TYPO3_PROJECT_NAME, TYPO3_SERVER_TYPE. The task_guide checklist tells me, also correctly, that in a DDEV project the lifecycle hooks are the install and that commands typed beside them boot something the repository does not describe. This repository declares no hooks at all ("hooks":[] — verified in .ddev/config.yaml), so typing the command beside them was the only option, and at that point nothing covers how the variables get into the container.

Two failures, two wasted round trips, both on syntax rather than on TYPO3:

- `ddev exec -e TYPO3_DB_DRIVER=mysqli ... typo3 setup --no-interaction` → "Error: unknown shorthand flag: 'e' in -e". ddev exec has no -e/--env flag (ddev v1.25.1); its flags are -d/--dir, -s/--service, -u/--user, -q/--quiet, --raw.
- `ddev exec --raw=false "TYPO3_DB_DRIVER=mysqli ... typo3 setup --no-interaction"` → OCI runtime error, the whole string stat'ed as one binary name, "file name too long". Setting --raw=false did not make the string be interpreted by a shell the way the flag's help ("Use raw exec (do not interpret with Bash inside container)") implies it would.
- `ddev exec bash -c "TYPO3_DB_DRIVER=mysqli ... typo3 setup --no-interaction"` → worked, "✓ Congratulations - TYPO3 Setup is done."

I accept this is partly DDEV's surface rather than TYPO3's. But the server already commits to the DDEV surface in several places — it reports the environment block, the hooks and the pull providers, it knows about the #ddev-generated additional.php and its marker line, it knows fail_on_hook_fail defaults to false. Having gone that far, the invocation form for a one-off unattended console command in that container is the piece a caller actually needs, and it is the piece that is absent. Every session that boots a hookless DDEV repository will rediscover `ddev exec bash -c` the same way I did.

Related and unstated: the repository's .ddev/config.yaml sets web_environment typo3DatabaseHost/Name/Password/Username (the functional-testing variables, pointing at database t3func). Those are not the variables typo3 setup reads, and a caller who sees them in the config file may reasonably assume the container is already configured for the install. It is not. The hints distinguish the two families in principle; nothing warns that a DDEV file may carry the test family and that it does nothing for setup.

## Query

typo3_hint_lookup id=installation-boot; typo3_hint_lookup id=environment-runtime-readers (returned inside typo3_task_guide changeType=operations). Then three attempts at the same command: `ddev exec -e VAR=... typo3 setup`, `ddev exec --raw=false "VAR=... typo3 setup"`, `ddev exec bash -c "VAR=... typo3 setup --no-interaction"`.

## Suggestion

Add to the installation-boot hint, or to a DDEV-specific hint, the invocation form for an unattended console command in a DDEV container: `ddev exec bash -c "VAR=value ... typo3 <command>"`, with the note that ddev exec has no --env flag and that --raw=false does not give the string a shell. One line saves two failed round trips per session. While there, name the trap that web_environment in .ddev/config.yaml often carries the typo3Database* functional-testing family, which typo3 setup does not read — the TYPO3_DB_ family is what it reads, and the two look alike enough to be mistaken for each other in a file the caller is reading anyway.

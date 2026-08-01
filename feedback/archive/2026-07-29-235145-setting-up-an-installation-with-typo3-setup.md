---
date: 2026-07-29T23:51:45+00:00
category: missing-knowledge
status: closed
closed: 2026-07-30
commit: 48562e2
subject: "[FEATURE] Give setting an installation up its own intent, credentials included"
tool: typo3_task_guide
directory: /home/benji/projects/site-new
---

# Setting up an installation with `typo3 setup` produces credentials, and the guide says nothing ab...

## Observation

Setting up an installation with `typo3 setup` produces credentials, and the guide says nothing about them. The checklist that comes back is the generic one (confirm branch, inspect nearby code, keep patch focused, add tests, run tests, write commit message) plus architecture hints on how to *write* a console command — nothing about the two credentials the command writes, and nothing about handing them to the user. Concretely missing: (1) `typo3 setup` prints neither the admin username nor the password it used, so an agent that runs it non-interactively with `--admin-user-password` is the only party that knows the value, and if it does not state it in its reply the user is locked out of the backend it just set up. This actually happened here: the installation was set up in an earlier session and the user had to come back and ask for the login. (2) `--admin-user-password` / `TYPO3_SETUP_ADMIN_PASSWORD` sets *two* credentials, not one — `SetupCommand` calls `SetupService::createUser()` and then `SetupService::setInstallToolPassword()` with the same value, so `BE/installToolPassword` in `config/system/settings.php` starts out identical to the backend password. There is no `--install-tool-password` option, which makes the coupling easy to miss. (3) The consequence of the coupling is not obvious either: change the backend password later and the two silently diverge, the install tool keeps the value from setup time, and since only the Argon2i hash remains there is no way back — the recovery is `install:password:set`, not guessing. Verified against typo3/cms-install 14.3 in the checkout: SetupCommand.php:253-258, SetupService.php:194-199, PasswordSetCommand.php.

## Query

typo3_task_guide task="Set up a new TYPO3 installation with the typo3 setup console command, non-interactive", changeType=unknown, targetVersion=14

## Suggestion

Give a task that reads as installing/bootstrapping an installation (`typo3 setup`, `setup --no-interaction`, "set up a new project", "install TYPO3") its own checklist items instead of only the generic ones: (a) report the admin username and password back to the user in the reply — the setup command does not print them, so an unreported password is a lockout; prefer generating a password over inventing a silent default, and name the backend URL alongside it; (b) note that the same value also becomes `BE/installToolPassword`, that no separate option exists for it, and that the two diverge as soon as the backend password changes; (c) point at `install:password:set` as the reset path and at `backend:resetpassword` for the backend user, since neither hash is reversible. Worth stating the security note the command's own `--help` carries too: a password in `--admin-user-password` lands in the shell history, so the interactive mode or a leading space is the safer route. A `--create-site` caveat belongs in the same place — a sitepackage that ships a root page and a site configuration must be set up *without* it, or the run leaves a second root page behind and the shipped site configuration is skipped.

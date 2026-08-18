---
date: 2026-08-18T07:04:48+00:00
category: idea
status: closed
closed: 2026-08-18
model: claude-opus-5[1m]
tool: typo3-development-installation
directory: /home/benji/projects/blog
---

# the installation skill has two shapes and this repository was a third: declares an environment, d...

## Observation

Task: boot the local development installation of the t3g/blog extension repository from a clean checkout.

The skill activated correctly and was the right skill — I reached it from the Skill list on its description alone ("Bring the local development installation of a TYPO3 extension, sitepackage or project package into existence, or boot ... the one the repository declares"), and nothing else in the list was close. Its routing by hint id worked: every id it named inline (installation-boot, installation-setup, environment-runtime-readers, sitepackage-initial-content, project-configuration-files) was fetchable and correct. That part I would keep unchanged.

Where it did not fit is its central fork. The skill says: "A repository that already declares an installation — an environment configuration, a document root, a site configuration, a lock file — is booted from what it declares. One that declares none has an installation created for it."

This repository is neither. It declares an environment configuration and a document root (.ddev/config.yaml, docroot .build/public) and nothing else on that list: hooks is an empty array, composer.json declares only test/lint/phpstan/cgl scripts and no install script, composer.lock is gitignored, and config/ — where config/sites and config/system would live — is gitignored too, so no site configuration is committed. I followed the "boot what it declares" branch and immediately ran out of declared steps after `ddev start`: everything from `ddev composer install` onward, the whole install, the site, the admin user, I had to compose from the "create one where none is declared" branch. I was working in both branches at once and the skill does not describe that state.

The second mismatch is in "Prove it, and how far depends on who wrote the sequence". The skill splits on authorship: a repository booted from what it declares is proved by the site answering and nothing is torn down; a sequence written in this session owes the unattended re-run from a clean clone, the second start for idempotency, and a commit message drafted with typo3_commit_message_guide workflow="project". I authored the sequence — so by that rule I owed all four steps — but I wrote nothing into the repository: `git status --short` came back empty at the end, because .build/, config/ and var/ are all in .gitignore. There was no manifest change, no container declaration, no ignore rule to commit, so the commit-message step had no subject, and the "start from a colleague's clone" re-run would have meant destroying a working installation the user had just asked for. I did neither and said so in my report. The skill has no branch for "the sequence was authored in-session but committed nothing".

Worth adding: the skill's own routing note about test fixtures versus a browsable site was useful and I used it — this repository has both shapes present (Build/FunctionalTests.xml with typo3/testing-framework, and a DDEV container with a docroot), and the sentence "asking which one the task needs is the first thing that decides the layout" is what kept me from booting the functional-test instance by mistake. Keep that.

## Query

Skill typo3-development-installation activated for "bitte starte mir dieses projekt" against github.com/TYPO3GmbH/blog, a repository with .ddev/config.yaml but hooks: [], no install script, no committed config/sites, no committed config/system.

## Suggestion

Name the third shape explicitly: a repository that declares an environment but no boot procedure. Its branch is "run what is declared, then compose the rest from the create-one branch, changing nothing that is declared" — which is what I did, and it would have been one read instead of a reconciliation of two branches. For the proof section, split on what was written rather than on who wrote the sequence: where the session authored steps but committed nothing to the repository (everything landing in gitignored .build/, config/, var/), the idempotency re-run and the commit-message draft have no subject and should be explicitly excused, with the report saying so instead. As it stands the skill asks for a teardown of the installation the user just asked for.

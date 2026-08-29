---
id: D-COD-007
title: 'The pins go to the current major except where PHP 8.2 falls'
date: 2026-08-29
status: open
coveredBy: []
---

# D-COD-007 — The pins go to the current major except where PHP 8.2 falls

**Every version pinned here is raised to the current major, except
`phpunit/phpunit`: 12 requires PHP 8.3 and this package declares 8.2.**

This is `R-COD-004` carried out the day it was written, and it is the reading
that rule asks for rather than a number anybody may keep.

## Evidence

- The actions, read on 2026-08-29: `actions/checkout` was pinned at v4 against a
  current v7.0.1, `actions/setup-node` at v4 against v7.0.0,
  `actions/upload-pages-artifact` at v3 against v5.0.0 and
  `actions/deploy-pages` at v4 against v5.0.0.
- Two pins were already current and stay: `shivammathur/setup-php` is on v2, and
  2.37.2 is the newest release of it; node is on 24, which is the active LTS
  under the name Krypton, while 26 is the current line and is not an LTS yet.
- `composer outdated --direct` offered one major and three patch levels:
  `phpunit/phpunit` 11.5.56 against 12.5.34, and `friendsofphp/php-cs-fixer`,
  `phpstan/phpstan` and the three Symfony components inside their declared
  constraints.
- What each PHPUnit line requires: 11.5.56 takes `php >=8.2`, 12.5.34 takes
  `php >=8.3`, 13.3.2 takes `php >=8.4.1`. `composer.json` declares `php >=8.2`
  and `ci.yml` runs the suite on 8.2, 8.3 and 8.4.
- The breaking notes of the raised majors were read against what these two
  workflows do. `checkout` 7.0.0 refuses a fork's head on `pull_request_target`
  and `workflow_run`, and neither event appears here. `setup-node` 5.0.0 caches
  by itself where `package.json` carries a `packageManager` field, and this
  repository has no `package.json` at all. `upload-pages-artifact` 4.0.0 stopped
  putting dotfiles in the artifact.

## Decided

- The four action pins go to the current major, in `ci.yml` and
  `documentation.yml`.
- `phpunit/phpunit` stays at `^11.5`. Raising it drops PHP 8.2, which is a
  decision about who can install this server rather than one about the test
  runner, and nothing here has asked for it.
- The patch levels are taken by `composer update` against the constraints as
  they stand, so the lock file carries them and no constraint moves.
- `coveredBy: []`. What holds the raise is the two workflows themselves on the
  next push, and what holds the PHPUnit line is the 8.2 leg of the matrix.

## Assumed

- That the rendered site carries no dotfile the deployment needs. It was not
  read: the render needs the theme installed from the network, and the artifact
  the next deployment uploads is where the answer is cheapest.
- That a raise verified by a green run on `ubuntu-latest` says nothing about a
  self-hosted runner, which `checkout` 5.0.0 requires at 2.327.1 or newer. This
  repository has none.

## Wrong if

- The site loses a file after this. That is the dotfile assumption above, and
  the deployment is where it shows.
- `deploy-pages` fails for a permission the workflow's `permissions:` block does
  not grant: its 4.0.0 asked for `actions: read`, and the block names
  `contents`, `pages` and `id-token` alone. The runs on v4 were green, which is
  what makes this a question about v5 rather than about the block.
- Somebody raises PHPUnit anyway and the 8.2 leg of the matrix goes with it,
  quietly narrowing who can install this package.

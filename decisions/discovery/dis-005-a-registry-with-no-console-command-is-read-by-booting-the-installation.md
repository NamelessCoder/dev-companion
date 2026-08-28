---
id: D-DIS-005
title: A registry with no console command is read by booting the installation
date: 2026-07-31
status: confirmed
---

# D-DIS-005 — A registry with no console command is read by booting the installation

**Where TYPO3 exposes no command for a registry, the installation is booted in a
subprocess and its container is asked.**

The registry is not reconstructed from the files the packages ship.

## Evidence

- Measured against two set-up sites on 2026-07-31. On one with
  `georgringer/news` installed the booted registry holds 1314 icon identifiers
  and the files yield 1289 — the 25 that exist only after the boot are the ones
  its `Configuration/Icons.php` builds in a `foreach`, plus
  `tcarecords-tx_news_domain_model_news-default`, which TYPO3 derives from TCA
  and no file contains. On the other, without news, both sources agree on all
  1287. Not one identifier was read from the files that the runtime does not
  have, so the parser is exact and short, never wrong.
- The same probe against `georgringer/news` itself — an extension repository
  with `composer install` and no `settings.php` — returns a `FailsafeContainer`
  with 1259 core icons and none of the extension's own, while
  `ExtensionManagementUtility::isLoaded('news')` answers `true`.
  `Bootstrap::init()` sets failsafe when `checkIfEssentialConfigurationExists()`
  fails, and every registry then answers a core-only subset that looks whole.

## Decided

- The container is the source where it comes up complete, the files are the
  fallback, and the answer carries which of the two answered plus what the
  fallback leaves out. A failsafe container is never handed on as a result; it
  is a reason to fall back.
- Against shipping a console command in a package of our own, for now. It would
  reach the same container through the invocation already resolved, and would
  work for a stated `TYPO3_DEV_COMPANION_CONSOLE` where deriving an interpreter
  does not — but it has to be installed into somebody else's project first, and
  in a non-configured system it is not registered either. Worth revisiting when
  the payload outgrows a few dozen lines or the topics need more than a
  read-only dump.

## Assumed

- Booting a set-up installation is safe to do while answering a lookup. It is
  the same thing every console command already does, and TYPO3's own CLI writes
  the same caches.
- `Bootstrap::init()`, `IconRegistry::getAllRegisteredIconIdentifiers()` and
  `FailsafeContainer` stay where they are. Verified present and unchanged in
  12.4, 13.4, 14.3 and main.

## Wrong if

- A boot has a side effect a lookup must not have — an extension whose
  `ext_localconf.php` writes outside the cache, or one that fails hard on a
  database that is not running where the console commands do not. The symptom is
  a lookup that changes the checkout, or one that takes the full 90 seconds and
  times out.
- The delivery breaks on a transport nobody here tested. The payload travels as
  `php -r 'eval(base64_decode("…"))'`, which survives `ddev exec` joining its
  arguments into a bash line; a transport that quotes differently would fail as
  an exit code, and the answer would degrade silently to the files with a reason
  nobody can act on.

## Confirmed on 2026-08-02

The first **Wrong if** did not fire, run against an `E-SITE` through this server
over stdio: a full boot of 1292 icons and 26 TCA tables cost 1.2 seconds warm
and 3.65 with the cache deleted, and every one of the seven files it wrote was
below `var/cache/`. Neither symptom appeared. What the run does not settle
travels with it — one environment with one project extension says the boot is
safe here, not that no `ext_localconf.php` writes — and a clean `git status` is
evidence about the checkout rather than about the boot, since `var/` is ignored.
What watches this now is step 5 of a forward run rather than a test that would
be measuring its own mock.

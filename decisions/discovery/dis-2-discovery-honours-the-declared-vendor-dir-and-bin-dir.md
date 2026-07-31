---
id: D-DIS-2
date: 2026-07-29
status: standing
---

# D-DIS-2 — Discovery honours the declared vendor-dir and bin-dir

**The packages and the console are found through the `vendor-dir` and `bin-dir`
the root `composer.json` declares, rather than through their default paths.**

Closes the notes about `.build/bin/typo3` being unreachable and about the
extension checkout not being recognised as an installation at all.

- **Evidence:** `bootstrap_package` on this machine — 21 packages found,
  console resolved as `ddev exec -- .build/bin/typo3` on PHP 8.5, and the 29
  `content-bootstrappackage-*` icons that were previously reported as
  non-existent.
- **Assumed:** what the root `composer.json` declares is enough to find both the
  packages and the console. Composer's `config.vendor-dir` and `config.bin-dir`
  are the only two ways either moves in practice, and everything else — DDEV,
  the interpreter choice — was already right and simply never got a binary.
- **Assumed:** invoking the console through a path relative to the installation
  root works inside DDEV as it does on the host.
- **Wrong if:** an installation whose console is invoked from somewhere other
  than the root — a DDEV project whose container working directory is the
  docroot rather than the project root would need an absolute path or a `cd`.
  Also an absolute `bin-dir`, which is accepted by Composer and ignored here.

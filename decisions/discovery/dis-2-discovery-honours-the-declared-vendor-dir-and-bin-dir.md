---
id: D-DIS-2
date: 2026-07-29
status: tested
---

# D-DIS-2 — Discovery honours the declared vendor-dir and bin-dir

**The packages and the console are found through the `vendor-dir` and `bin-dir`
the root `composer.json` declares, rather than through their default paths.**

Closes the feedback about `.build/bin/typo3` being unreachable and about the
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
- **Tested on 2026-08-01:** the absolute `bin-dir` half happened as written.
  Composer 2.9.5 accepts one and installs the binaries there — a fixture project
  declaring `"bin-dir": "<root>/.build/bin"` got its console at exactly that
  path — and this server then found no console at all and named only the two
  defaults. The decision holds; what was missing is that absolute is a spelling
  of the same directory. One below the root is now expressed relative to it,
  which is the form both DDEV and the host need, and `autoloader()` reads an
  absolute `vendor-dir` the same way. One outside the root still has no usable
  form and is named in the reason with `TYPO3_MCP_CONSOLE`. `R-DIS-3` holds
  both.
- **Since then:** the other half of that **Wrong if** is untested and needs a
  project rather than a fixture. DDEV 1.25.1 defaults `working_dir.web` to
  `/var/www/html`, the project root, and its generated `config.yaml` names that
  as the directory `ddev exec` runs in — so the second **Assumed** holds by
  default, and only a project that overrides the working directory to its
  docroot would break it. No environment here has one; the todo carries the
  question of which to build.

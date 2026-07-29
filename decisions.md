# What was decided, and on what evidence

A feedback note is deleted by the commit that closes it, and the commit message
says what changed and why. What a commit message cannot carry is the part that
may not survive: the assumption the change rests on, the evidence that was
available at the time, and what would show the decision to have been wrong.

That is what this file is for. One entry per decision worth revisiting, newest
first. An entry is not a changelog line — a change nobody would need to
reconsider does not belong here. When an assumption is later disproved, the
entry stays and gains a **Corrected** line: the wrong assumption is the useful
part, because it names the place where the next one is likely to sit.

## 2026-07-29 — Discovery honours the declared vendor-dir and bin-dir

Closes the notes about `.build/bin/typo3` being unreachable and about the
extension checkout not being recognised as an installation at all.

- **Assumed:** what the root `composer.json` declares is enough to find both the
  packages and the console. Composer's `config.vendor-dir` and `config.bin-dir`
  are the only two ways either moves in practice, and everything else — DDEV,
  the interpreter choice — was already right and simply never got a binary.
- **Assumed:** invoking the console through a path relative to the installation
  root works inside DDEV as it does on the host.
- **Evidence:** `bootstrap_package` on this machine — 21 packages found,
  console resolved as `ddev exec -- .build/bin/typo3` on PHP 8.5, and the 29
  `content-bootstrappackage-*` icons that were previously reported as
  non-existent.
- **Would falsify it:** an installation whose console is invoked from somewhere
  other than the root — a DDEV project whose container working directory is the
  docroot rather than the project root would need an absolute path or a `cd`.
  Also an absolute `bin-dir`, which is accepted by Composer and ignored here.

## 2026-07-29 — The root package counts as an installed package

- **Assumed:** in an extension development checkout, the extension being edited
  is the root package and is meant to be part of every answer about "this
  installation" — its icons and labels are as registered as any dependency's.
- **Assumed:** a root package alone is not an installation. The root is only
  added when Composer's metadata yielded packages, so an extension repository
  whose dependencies were never installed still reports no installation rather
  than one holding a single package and no console.
- **Would falsify it:** a monorepo whose root declares a TYPO3 package type but
  is not the thing being worked on, or a setup that installs the root into the
  vendor directory as well — the two entries then resolve to the same realpath
  under one key, which is intended, but has not been seen in the wild here.

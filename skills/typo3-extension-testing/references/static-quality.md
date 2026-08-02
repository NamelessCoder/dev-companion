# Static analysis and coding standards guidance

Read this after choosing the static-quality layer. It covers analysis, coding
standards, and the linting or normalisation steps that run beside them. Let the
checkout, the package's declared TYPO3 and PHP range, and versioned
documentation decide concrete package versions, rule sets, configuration
contents, and commands.

## Verify what is already there

1. Inspect the package manifests, the lock file, installed analysers and fixers,
   their configuration and rule sets, existing baselines, Composer scripts, the
   development environment, and CI before changing any of them. Half an
   infrastructure is the ordinary case: a fixer without an analyser, a lint step
   without either, a configuration nothing calls.
2. Run every check that already exists, unchanged, and record its output. That
   run is what every later claim is measured against, and a first analyser
   report on a project that never ran one is a finding list rather than a
   regression.
3. Separate a missing executable, an unreadable configuration, and a real
   finding. Only the third says anything about the code.
4. Read what existing configuration deliberately excludes before widening it. A
   generated directory, a vendored library, or a fixture tree with intentionally
   broken code is a decision, not an oversight.
5. For a review-only request this is the whole workflow: report what is missing
   or unenforced and change nothing.

## What a complete surface covers

This is the expectation the checkout is measured against, named by what each
check establishes rather than by the tool behind it. Which of them apply is
decided by what the package ships: a check whose subject the extension does not
ship is absent for a reason rather than missing.

- **Syntax** — every shipped PHP file parses on every PHP version the package
  declares support for. `php -l` through a lint runner such as
  `overtrue/phplint` or `php-parallel-lint/php-parallel-lint`, so one command
  covers the tree.
- **Static analysis** — types, unreachable code, and calls that cannot succeed.
  `phpstan/phpstan` against the extension's own paths, which is what the core
  runs on itself: `GeneralUtility::makeInstance()` and its neighbours carry
  `@template` annotations, so the analyser gets its types from the installed
  core rather than from a TYPO3-specific analyser extension. Beside it,
  `phpstan/extension-installer` wires up whatever extensions are installed,
  `phpstan/phpstan-phpunit` belongs beside a PHPUnit suite,
  `bnf/phpstan-psr-container` types the container's `get()` calls, and
  `phpstan/phpstan-deprecation-rules` reports use of deprecated API. Establish
  that a TYPO3-specific analyser extension is still maintained before adding
  one: several are not, and an abandoned extension on a current core produces
  false findings instead of types. `vimeo/psalm` is the alternative where a
  project already runs it. Which packages to require is the whole of what this
  page decides about the analyser; what goes into its configuration is not: `typo3_architecture_lookup` with `id=extension-static-analysis`
  answers where the file belongs, which include it carries, the constants an
  extension's analysis never sees, the manifest excluded rather than fixed, the
  result cache directory, the level, and what a baseline is for. It is read off
  the packages that configure themselves this way, so ask it rather than
  recalling a configuration from another project.
- **Coding standards** — the TYPO3 coding guidelines as the project applies
  them. `friendsofphp/php-cs-fixer` driven by the `typo3/coding-standards` rule
  set, which also owns the file header the guidelines require.
  `editorconfig-checker` where the repository ships an `.editorconfig`, and
  `squizlabs/php_codesniffer` with `phpcompatibility/php-compatibility` where
  the declared PHP range has to be proven without running a matrix.
- **Manifests and dependencies** — `composer validate` on the extension's own
  manifest and its agreement with what the extension declares about itself
  elsewhere, `composer audit` for advisories against what it requires, and
  `ergebnis/composer-normalize` where the project keeps its manifest normalised.
- **Shipped configuration and data** — the files the package actually ships: XML
  well-formedness for the XLIFF files, a YAML lint such as `j13k/yaml-lint` or
  the framework's own `lint:yaml` for configuration, and
  `helmich/typo3-typoscript-lint` for TypoScript. Fluid templates have no
  established linter — they are proven by the functional tests that render them,
  and saying so is better than inventing a check for them.
- **Shipped frontend assets** — where the package ships JavaScript, TypeScript
  or CSS. `eslint` for the scripts, with `@typescript-eslint` where the sources
  are TypeScript; `stylelint` for the stylesheets, with `stylelint-scss` and
  `stylelint-order` where Sass is compiled; a formatter such as `prettier` on
  the fix side and never wired into the check. Lint the sources the repository
  maintains rather than the compiled bundle below `Resources/Public/` — a
  finding in generated output is a finding about the build step that produced
  it. Declare each of them as a script in the package's own `package.json`, so
  the same one command exists locally and in CI, and take the Node version from
  what the project declares rather than from the machine that happens to run it.

Read the names as the default per check where the checkout covers it with
nothing, never as a replacement for what it already runs: a project with another
analyser, another lint runner or its own wrapper around one has answered that
question already, and a second tool for the same check is a second answer to
maintain. Versions come from the solver and from what the package's range
allows, never from this list.

`rector/rector` with `ssch/typo3-rector` reads like a check in its dry-run mode
and is not one: it proposes migrations, which belong to an upgrade task rather
than to the surface CI has to keep green.

Then say which axis each one has, because that is what decides where it runs.
Syntax and analysis depend on the PHP and TYPO3 combination and belong in the
matrix; a standards, manifest or format check is version-independent and one run
of it proves as much as sixteen. A matrix whose every cell runs only
version-independent steps proves that the files parse and nothing more — say so,
with what it costs and what it does not buy.

## Resolve the dependencies

1. Form candidates from the package's own declared TYPO3 and PHP range, never
   from another project's manifest and never from the core's build tooling.
2. Let Composer resolve the newest candidate that intersects those constraints
   together with what is already installed. Do not write a concrete constraint
   until the solver has accepted it.
3. Take a rule set from a package the project already requires before adding
   another one. Every added package is a further thing that has to stay current
   across the whole declared range, which is what an abandoned one stops doing.
4. Keep every one of them in the development requirements.

## Establish one command per check

1. Give each check one stable project-owned command, declared where the project
   already declares its commands and named for what it checks rather than for
   the binary behind it.
2. Keep checking and fixing apart. A check reports and fails; a fix writes. One
   command that rewrites the working tree on the way to its verdict is not a
   check, and it is not what CI can call.
3. Keep automatic formatting inside the first-party paths the project intends it
   to touch. Vendored code, generated output, other packages' files, and
   fixtures asserting exact bytes stay outside the fixer's paths — confirm that
   with the fixer's own dry run before the first write and with the working
   tree's status afterwards.
4. Point analysis at the paths the extension owns, at the level the project can
   hold today. A level chosen for the eventual state produces a wall of findings
   nobody works off.
5. Run each command locally until it passes, then make CI call that same
   command. A CI step assembled independently proves something the developer
   cannot reproduce.

## Work the findings off

- Fix the finding. A baseline records what was already there on the day it was
  written and never receives an error the change in hand introduced.
- Where a baseline exists, read it as a work list with an owner and a horizon —
  usually the release that drops the oldest supported version — and say which of
  its entries the current change retires.
- A suppression that is correct while an older version is supported is evidence
  rather than debt. Establish what it is there for, and what would remove it,
  before proposing that it goes.
- Keep a formatting pass in its own commit, apart from behavioural change. A
  diff that mixes both is one nobody can review.
- Report a finding in code the task does not touch instead of quietly fixing it
  alongside the requested work.

## Prove it

1. Run each check on the narrowest scope it supports, then on its full target.
2. Run the fix command, run the check again, and inspect the working tree for
   files outside the intended scope.
3. Run the CI-equivalent commands after the local commands pass.
4. Report the exact commands, their results, the files the fixer changed, and
   every check not run with the reason.

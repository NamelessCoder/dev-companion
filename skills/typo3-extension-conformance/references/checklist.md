# Conformance audit checklist

Read the relevant sections for a scoped review; read all sections for a full
extension audit. Absence of an optional subsystem is not a defect.

## Audit surfaces

- Package: identity, Composer constraints, autoloading, extension metadata, and
  supported TYPO3/PHP range.
- Registration and runtime: services, events, middleware, plugins, content
  elements, backend modules, routes, permissions, and effective configuration.
- Persistence: TCA, schema, relations, DataHandler use, repositories, fixtures,
  and upgrade paths.
- Rendering: site sets, TypoScript, TSconfig, Fluid roots and namespaces,
  templates, translations, and public assets.
- Security: authorization boundaries, state-changing requests, output-context
  escaping, raw rendering, user-controlled attributes/URLs, and secret exposure.
- Quality: tests, the check layer, documentation, deprecations, and upgrade
  readiness.

## The check layer

The commands a repository declares are where this surface is read, never what
it is. Measure them against what a complete layer covers, each entry named by
what the check establishes rather than by the tool behind it:

- **Syntax** — every shipped PHP file parses on every PHP version the package
  declares support for.
- **Static analysis** — types, unreachable code, and calls that cannot succeed,
  over the paths the package owns and at a level the project can hold today.
- **Coding standards** — the TYPO3 coding guidelines as the project applies
  them, with the editor-configuration and declared-PHP-range checks that run
  beside them.
- **Manifests and dependencies** — the manifest validates, agrees with what the
  package declares about itself elsewhere, and carries no open advisory.
- **Shipped configuration and data** — the XLIFF, YAML and TypoScript the
  package ships are well-formed. Fluid templates have no established linter and
  are proven by the tests that render them.
- **Shipped frontend assets** — the JavaScript, TypeScript and CSS sources the
  repository maintains, never the bundle a build step produced from them.

Which of them apply is decided by what the package ships: a check whose subject
it does not ship is absent for a reason, while one whose subject it ships and
no command covers is a gap in the layer rather than an optional subsystem, and
that absence is the finding. Ask the same of where each one runs — syntax and
analysis depend on the PHP and TYPO3 combination and belong in a matrix, while
a standards, manifest or format check is version-independent and one run of it
proves as much as sixteen, so a matrix whose every cell runs only
version-independent steps establishes that the files parse and nothing more.

The checks that exist are run, and what they printed is the ceiling of what
this surface is worth rather than its verdict: a green net proves the entries
it covers and nothing about the ones it has none for, so say which of the
entries above it leaves untouched. Establishing a missing one is
`typo3-extension-testing`'s workflow, and it names the default tool per check;
a review names the gap, routes it there, and changes nothing.

## Content element architecture

Before accepting a content-element implementation, verify the editor workflow
and ownership model rather than only the CType registration:

- Repeatable content owned by one element should normally use a dedicated
  inline child table. References to existing records require an explicit reuse
  requirement and reviewed visibility, localization, lifecycle and duplicate
  rendering behavior.
- Keep shared content-element setup in the generic override and move one
  element's fields and CType registration into a named sibling.
- Keep one content element per TypoScript file under the project's established
  content-element directory.
- Load element-only CSS and JavaScript through the template AssetCollector.
  Page-level inclusion is for assets needed across the site.
- Require functional coverage for inline persistence and rendering; require
  browser coverage for JavaScript interaction and accessibility.

For each surface, compare checkout declarations, runtime evidence when
available, applicable architecture guidance, and versioned official
documentation. When an installation parser misses a dynamic PHP registration,
report that limitation and inspect the checkout rather than treating it as
absence.

## Severity

- Critical: exploitable security issue, destructive data loss, or release-wide
  outage with no practical containment.
- High: likely security boundary failure, data corruption, or a primary feature
  unusable in a supported setup.
- Medium: concrete incompatibility, unsafe rendering pattern, broken secondary
  behavior, missing regression coverage for risky code, or misleading operator
  documentation.
- Low: limited maintainability or convention issue with a concrete future cost.
- Recommendation: beneficial improvement without a verified violation.

Severity follows demonstrated consequence, not the number of files involved.
State missing evidence instead of inflating severity.

## Finding gate

A finding needs a concrete location, observed evidence, applicable rule or
documentation, consequence, remediation, and relevant project check. Otherwise
record it as a question or unverified category, not a violation.

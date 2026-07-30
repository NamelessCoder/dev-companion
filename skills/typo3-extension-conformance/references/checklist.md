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
- Quality: tests, declared validation commands, documentation, deprecations,
  and upgrade readiness.

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

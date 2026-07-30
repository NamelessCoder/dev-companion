# Documentation decision checklist

Read this before choosing the documentation surface and again before finishing.

## Audience and surface

- Identify the reader: evaluator, integrator/administrator, editor, extension
  developer, or maintainer.
- Put purpose and essential setup in README; use the existing Documentation/
  manual for durable configuration, workflows, API, and migration material.
- Separate installation and administration from editor workflows and developer
  extension points.
- Extend the repository's existing structure and markup; do not create a second
  canonical manual.

## Evidence and conflicts

- The checkout is authoritative for what this package implements: registration,
  keys, defaults, paths, examples, and supported behavior.
- Installation lookups are authoritative for effective registrations and
  runtime state, but a parser miss is not proof of absence. If dynamic PHP code
  and parsed output disagree, describe the parser limitation and verify the
  code path.
- Composer constraints and versioned official documentation own compatibility
  and external TYPO3 API claims.
- Tests may demonstrate behavior, but do not turn internal implementation
  details into promised public API.

## Secret hygiene

- Never copy passwords, tokens, private keys, personal addresses, internal
  hosts, or machine-specific paths into examples.
- Replace required credentials with named placeholders and explain how they are
  supplied securely.
- When existing documentation contains plausible credentials or personal data,
  flag it explicitly and avoid reproducing it.

## Completion gate

Stop gathering evidence when every requested audience and surface has:

1. an authoritative implementation source;
2. version evidence for external TYPO3 claims;
3. a verified example or an explicit unverified marker;
4. validated paths, identifiers, and links; and
5. no unresolved contradiction that changes user instructions.

Report remaining unknowns instead of continuing broad searches. Run only
declared validation commands and finish with changed files, checks, and
unverified behavior.

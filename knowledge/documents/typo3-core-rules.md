# TYPO3 Core Contribution Rules

This file is the first local knowledge base for the TYPO3 CMS MCP server. Keep
it practical, versioned, and conservative. Prefer links to official TYPO3
documentation when rules depend on a specific branch or current policy.

## Contribution Flow

- Work against the TYPO3 core repository and target the intended active branch.
- Keep changes focused on one bug, feature, cleanup, or test improvement.
- Add or update tests for behavior changes.
- Run the narrowest useful test first, then broaden when shared behavior is
  touched.
- Mention affected subsystems and executed commands in the final task summary.

## Code Style

- Follow the coding style already used by the touched TYPO3 subsystem.
- Prefer existing TYPO3 APIs and services over new local abstractions.
- Keep public API changes explicit and documented.
- Avoid unrelated refactoring in bug fix patches.

## Testing

- Unit tests are expected for isolated behavior.
- Functional tests are expected for persistence, configuration, routing, backend
  behavior, or integration with TYPO3 services.
- End-to-end tests, the `e2e` suite, are useful when the change affects editor or
  administrator workflows and only breaks in the assembled backend. They replaced
  the former acceptance suites.
- Document tests that could not be executed and why.

## Review Readiness

- The change should be reproducible from the issue or task description.
- The patch should include a concise explanation of the problem and the chosen
  fix.
- Breaking changes, migrations, and deprecations need clear notes.
- Security-sensitive behavior needs extra care and focused tests.

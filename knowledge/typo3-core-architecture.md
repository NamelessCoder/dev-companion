# TYPO3 Core Architecture Hints

This document collects practical architecture hints for TYPO3 core work. Keep entries short, conservative, and tied to established TYPO3 core patterns. Prefer official documentation or nearby core code over broad framework advice.

## System Extension Boundaries

- Core code lives in `typo3/sysext/*`.
- Keep changes inside the owning system extension unless a cross-extension contract really changes.
- Reuse public APIs from another system extension instead of reaching into internal implementation details.
- Check extension-local tests before adding shared behavior.

## Dependency Injection and Services

- Prefer constructor injection for new service dependencies.
- Use `Configuration/Services.yaml` when explicit service wiring is needed.
- Avoid new `GeneralUtility::makeInstance()` calls for regular service dependencies unless nearby core code or lifecycle constraints require it.
- Runtime service wiring changes usually need functional tests.

## Events, Hooks, and Extension Points

- Prefer modern TYPO3 event APIs for new extension points.
- Avoid adding hooks for new behavior unless the subsystem still intentionally uses hook-based extension points.
- Event payloads should expose stable, minimal state and avoid leaking mutable internals.
- Public extension points need careful naming, documentation, and tests.

## Backend TypeScript Modules

- TypeScript source lives in `Build/Sources/TypeScript/`.
- Generated JavaScript lives in `typo3/sysext/*/Resources/Public/JavaScript/`.
- Generated JavaScript should match the TypeScript source output.
- Prefer existing TYPO3 imports, event patterns, and custom element conventions used nearby.
- Run `./Build/Scripts/runTests.sh -s build`, `lintTypescript`, and relevant `unitJavascript` tests.

## Backend UI and Web Components

- Keep custom elements focused on rendering and interaction orchestration.
- Keep state transitions explicit and testable.
- Prefer existing TYPO3 backend UI utilities and module patterns before introducing new client-side infrastructure.
- Check accessibility behavior when adding controls, dialogs, drag and drop, or status indicators.

## TCA, FormEngine, and Backend Forms

- TCA changes can affect persisted data, editor workflows, and generated backend forms.
- Prefer established TCA option names and existing FormEngine behavior.
- Add functional coverage when behavior depends on TCA processing, data providers, or backend form rendering.
- Check language labels when new UI fields or options are introduced.

## DataHandler and Persistence

- DataHandler changes are high-impact and usually need functional tests.
- Preserve workspace, localization, permissions, and hook or event behavior unless intentionally changed.
- Avoid changing persistence side effects without focused regression coverage.
- Test edge cases with deleted, hidden, localized, versioned, or workspace records when relevant.

## Routing, Middleware, and Request Handling

- Prefer PSR-7 request and response objects for HTTP behavior.
- Keep middleware focused and ordered deliberately.
- Functional tests are expected for routing, authentication, permission, and request lifecycle behavior.
- Avoid global state when request-scoped data is available.

## Language Files

- XLIFF labels should use clear, stable identifiers and concise wording.
- Reuse existing labels where the meaning is identical.
- Run `checkIntegrityXliff` and consider `normalizeXliff -n` after editing language files.

## Documentation and Changelog

- User-facing behavior changes may need ReST documentation or changelog entries.
- Deprecations need explicit migration guidance and scanner considerations when applicable.
- Run `checkRst` for ReST changes.

## Testing Strategy

- Start with the narrowest test that exercises the changed behavior.
- Add unit tests for isolated logic.
- Add functional tests for TYPO3 service wiring, persistence, TCA, DataHandler, routing, backend integration, and configuration behavior.
- Add frontend unit tests for TypeScript modules with meaningful logic or state transitions.
- Run broader checks before review when shared behavior or generated assets are touched.

# Test strategy checklist

Read this when choosing test layers, reviewing coverage, or selecting commands.
Use only rows supported by the task and checkout.

## Select the layer

- Unit: isolated logic whose behavior needs neither TYPO3 bootstrapping nor
  persistence.
- Functional: services, configuration, database schema, repositories,
  DataHandler, fixtures, or interaction between TYPO3 components.
- Browser: rendered journeys, backend interaction, JavaScript, or accessibility
  behavior that cannot be established below the UI.
- Architecture/static: structural rules already enforced by the project, or
  infrastructure the task explicitly asks to establish.

## Respect the functional frontend boundary

- A TYPO3 functional frontend subrequest proves server-side routing, rendering
  and the generated response.
- An asset URL or AssetCollector tag in that response proves registration, not
  that the browser loaded or executed the asset.
- Functional tests do not execute JavaScript, apply CSS, calculate layout,
  exercise focus behavior or inspect the browser accessibility tree.
- Use a browser test for interactive claims, and describe untested interaction
  as unverified rather than as frontend-tested.
- Documentation/configuration: validate examples and declarations directly;
  do not disguise a file-presence assertion as a behavioral test.

Prefer the lowest layer that observes the contract and would fail for the
reported defect. Record why a higher layer is necessary.

## Select commands

1. Prefer the narrowest existing command that names the affected test or suite.
2. Use a direct PHPUnit or browser-runner invocation only when its executable,
   configuration, and target path exist in the checkout.
3. Then run the containing command declared by `typo3_project_scope` or the
   repository's documented test setup.
4. Never translate TYPO3 core commands into extension commands by analogy.
5. Separate missing infrastructure or environment prerequisites from a failing
   test.

## Coverage review

- Trace every claimed behavior to an existing test or a concrete missing case.
- Include success, boundary, failure, authorization, and persistence behavior
  only where the production code exposes those branches.
- Check that fixtures are minimal, deterministic, and independent of local site
  data, time, execution order, and external services.
- Name exact files and commands; label anything not executed as unverified.

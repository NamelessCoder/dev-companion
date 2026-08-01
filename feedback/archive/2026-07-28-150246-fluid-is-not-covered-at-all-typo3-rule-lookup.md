---
date: 2026-07-28T15:02:46+00:00
category: missing-knowledge
status: closed
closed: 2026-07-28
commit: 944e261
subject: "Cover Fluid templates and ViewHelpers"
tool: typo3_rule_lookup
---

# Fluid is not covered at all. typo3_rule_lookup returned matchCount 0, and the document list it fe...

## Observation

Fluid is not covered at all. typo3_rule_lookup returned matchCount 0, and the document list it fell back to confirms it: none of the seven documents has a Fluid or ViewHelper topic. typo3_architecture_hint for a .fluid.html partial plus a ViewHelper class returned Backend UI and Web Components (TypeScript), System Extension Boundaries, and Icons/Text/Layout Stability (CSS) — not one hint about Fluid. A .fluid.html path apparently classifies as the generic "frontend" domain and is routed to the TypeScript and CSS sections. This is a large blind spot: Fluid templates and ViewHelpers are among the most commonly touched things in a backend patch, and the conventions are both strict and recently changed, so an agent's priors are actively wrong. Verified against the checkout at the pinned revision: (1) template files use the .fluid.html extension — 617 files under typo3/sysext/*/Resources/Private carry it against 3 remaining plain .html; (2) renderStatic and the CompileWithRenderStatic trait were deprecated in 13.3 (Deprecation-104789-RenderStaticForFluidViewHelpers.rst) and are gone from every core ViewHelper — zero occurrences under typo3/sysext/*/Classes/ViewHelpers/, the only two remaining mentions are the changelog and the extension scanner matcher; the current shape is final class extends AbstractViewHelper with initializeArguments(): void plus registerArgument() and public function render(): string, and protected $escapeOutput = false where markup is returned; (3) ViewHelpers are covered by functional tests, not unit tests — 85 test classes under Tests/Functional/ViewHelpers/ against 2 under Tests/Unit/ViewHelpers/, which the generic "Testing Strategy" section would not tell you; (4) Fluid parsing is configured via TYPO3_CONF_VARS SYS fluid in typo3/sysext/core/Configuration/DefaultConfiguration.php:520 with interceptors, preProcessors including NamespaceDetectionTemplateProcessor, and expressionNodeTypes.

## Query

typo3_rule_lookup query="Fluid template ViewHelper conventions escaping namespace" (0 matches) and typo3_architecture_hint paths=["typo3/sysext/backend/Resources/Private/Partials/DocHeader.fluid.html","typo3/sysext/core/Classes/ViewHelpers/IconViewHelper.php"]

## Suggestion

Add a Fluid and ViewHelpers architecture section, matched on Resources/Private/**/*.fluid.html and Classes/ViewHelpers/**/*.php, and split the frontend domain so a .fluid.html path stops routing to the TypeScript and CSS sections. It should cover: the .fluid.html naming convention for Templates, Partials and Layouts; the ViewHelper class shape (final, AbstractViewHelper, initializeArguments/registerArgument, typed render(), escapeOutput and escapeChildren and when to set them) with an explicit note that renderStatic and CompileWithRenderStatic are deprecated since 13.3 and must not be used in new code; that ViewHelpers get functional tests under Tests/Functional/ViewHelpers/ rather than unit tests; the globally registered namespaces (f: for TYPO3\CMS\Fluid\ViewHelpers, core: for TYPO3\CMS\Core\ViewHelpers, be: for the backend ones) and the data-namespace-typo3-fluid="true" attribute; and where template, partial and layout paths are configured. A companion rule entry in typo3-core-rules would make it reachable from typo3_rule_lookup, which currently answers Fluid questions with nothing.

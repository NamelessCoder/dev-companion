# TYPO3 Core CSS Architecture

Authoritative, binding rules for CSS and Sass work in the TYPO3 backend. Keep it aligned with the
sources under `Build/Sources/Sass/`. Scope: backend UI (Sass, component styles, custom-element
styles, design tokens). Frontend and site-package CSS are out of scope.

## Core Principles

When a detailed rule seems ambiguous, resolve it in favour of the principle behind it.

- **Reuse before authoring.** Compose existing components and tokens before writing new CSS.
- **Tokens before raw values.** Express colors, spacing, borders, shadows, z-index, and transitions
  through `--typo3-*` custom properties — never hard-coded values.
- **Semantics before visual hooks.** Drive state and structure from semantic attributes and
  component properties, not ad hoc classes or DOM position.
- **Sass is the source.** `Build/Sources/Sass/` is canonical; generated CSS only ever follows from
  it.
- **Demos are part of the component.** No backend component is complete without a styleguide demo.
- **Light/dark, RTL, and accessibility are non-negotiable.** Every component must work in both color
  schemes, in both writing directions, and for keyboard and assistive-technology users.

## Source and Build

- Sass sources live under `Build/Sources/Sass/`; generated public CSS must never be the primary edit
  when a Sass source exists.
- After Sass changes, run `./Build/Scripts/runTests.sh -s build` and `-s lintScss` (TYPO3's
  stylelint setup for `Build/Sources/Sass/**/*.scss`).
- For a focused iteration use `-s npm -- run build-css` (Grunt `css`: compile Sass, Autoprefixer,
  cssnano, file banner); run the full `build` suite before review.
- Some assets have special build paths, for example CKEditor CSS in `Build/rollup/ckeditor.js`.
- If generated assets are committed, the matching source change must exist. Never hand-edit
  generated output as the only change, and keep names stable when TypeScript and Sass share class
  names or custom elements.

## Browser Target

- Backend CSS targets the evergreen browsers current on the day of the matching TYPO3 LTS release.
- Modern features are allowed when they fit that baseline. They may be adopted shortly before an LTS
  only when clearly on track for its baseline — intentionally and reviewably.
- Backports must fully match the evergreen baseline of the target release year; do not backport
  features the target branch's baseline does not support.
- Add no workarounds for browsers outside the active LTS target.
- Prefer native CSS over JavaScript when the target supports it, for example the Popover API over
  custom positioning or visibility logic.

## Component Structure

The backend stylesheet is a set of **bundles** compiled from `Build/Sources/Sass/`. Each top-level
entry file without a leading underscore — `backend.scss`, `dashboard.scss`, `adminpanel.scss`,
`form.scss`, `workspace.scss`, and a few more — compiles to one generated public CSS file.
Everything else is a partial named `_name.scss`, pulled into a bundle through `@import`.

The backend bundle has two layers:

- **Base (`_minimal.scss`).** The Bootstrap foundation plus TYPO3's own foundational component
  partials (`component/buttons`, `badges`, `panel`, `table`, `nav`, `modal`, the `scaffold/*`
  layout, …). Most Bootstrap *component* imports are kept here as explicit commented-out `//
  @import` lines — disabled on purpose because TYPO3 ships its own versions, and left visible so
  they are never mistaken for an oversight.
- **Application (`backend.scss`).** Imports `_minimal`, then backend-specific partials
  (`component/tree`, `recordlist`, `livesearch`, …), the `element/*` custom-element styles, and
  the `typo3/*` glue.

Folder taxonomy under `Build/Sources/Sass/`: `component/` (one partial per component),
`component/forms/` (form controls), `component/scaffold/` (topbar, toolbar, module menu, sidebar),
`element/` (custom elements, named after the host element), `dashboard/` and `module/` (area
styles), `variables/` and `mixins/` (tokens and helpers), `libs/` and `typo3/` (third-party and
legacy glue — add no new component styles here).

- Add focused component partials under `component/`. Keep selectors close to the owning component
  and avoid global selectors. One partial owns one component; do not spread a component across
  partials.
- Forms → `component/forms/`; scaffold layout → `component/scaffold/`; dashboard →
  `dashboard/`; custom elements → `element/`.
- Name a partial `_<component>.scss`, aligned with its class root, for example `_badges.scss` for
  `.badge`.
- A partial is compiled only once it is `@import`ed into a bundle — there is no glob or index.
  Wire foundational, reusable components into `_minimal.scss`; wire app-specific ones into
  `backend.scss` (or the bundle that owns the feature).
- Document a component's canonical markup in a `// Markup:` block atop its partial; the styleguide
  demo mirrors that markup.
- The Sass layer uses `@import`, not Dart Sass `@use`/`@forward`. Follow the existing import style
  and ordering.

## Minimal CSS

- Write as little new CSS as possible; reuse, extend, or compose existing components first.
- New CSS must express reusable component behavior, not one-off page tweaks. When a pattern repeats,
  make it a component or component property instead of repeating local CSS.
- Utility classes are exceptional only — never a substitute for a missing component, and never a
  source of hidden markup/layout coupling.

## Class Naming

- Follow nearby conventions: a short component root plus hyphenated elements or variants (`.panel`,
  `.panel-heading`, `.toolbar-item`, `.module-docheader`). Prefer readable kebab-case.
- Do not introduce BEM-style `block__element--modifier` unless the surrounding component already
  uses it.
- Variants and sizes append a suffix (`.btn-sm`, `.card-size-large`, `.table-fit`) and should mainly
  set custom properties the base component consumes — do not duplicate full rule sets per variant.
- State classes are explicit and consistent (`.active`, `.disabled`, `.selected`, `.is-*`,
  `.has-*`).
- `t3js-*` classes are JavaScript hooks only — never attach styling to them, and keep hook and
  styling classes separate.
- Avoid generic names that can collide globally.

## Styleguide Demos

- Every CSS component must have a styleguide demo. New components need one; changed components
  update theirs. A change without demo coverage is incomplete unless the patch documents why.
- Demos live under `typo3/sysext/styleguide/Resources/Private/Templates/Backend/Components/` and
  must cover relevant variants, states, sizes, icons, empty/disabled/interaction states, light and
  dark mode, and RTL-sensitive layout where applicable.

## Design Tokens

- Use existing `--typo3-*` custom properties for colors, spacing, borders, shadows, transitions, and
  states. Never use Bootstrap CSS variables (`--bs-*`) — they are deprecated; use the `:root`
  `--typo3-*` tokens (via `component/root`) instead.
- **Never hard-code color values.** Every color must come from a semantic `--typo3-*` token, or be
  derived from one with `color-mix(...)`. No raw hex, RGB, or HSL literals in component CSS — raw
  color literals belong only where palette tokens are defined.
- **All colors must be light/dark compatible.** Use semantic tokens that resolve per scheme, or
  `light-dark(...)` when a value genuinely needs distinct variants. Never introduce a color that
  works in only one color scheme.
- Avoid hard-coded spacing, z-index, or breakpoints when a token exists. Avoid one-off local tokens
  unless the value repeats or carries clear semantics.
- Use custom properties as a component's API for colors, spacing, borders, shadows, and transitions:
  define them at the component root with intent-revealing names (`--upload-queue-item-bg`), consume
  them in children, prefer fallback-free `var(...)` for global tokens, and avoid deep alias chains.
- Variants set property values while the base component holds structure. Add variant-specific
  declarations only when layout, structure, or behavior cannot be expressed through properties.
  Expose a small, named property contract when variants, host context, or states must tune the
  component.

## Color and Surface Tokens

- `--token-color-*` are low-level palette tokens — never use them directly in component CSS. They
  only define semantic tokens, with explicit light and dark values via `light-dark(...)`.
- Roles: `--typo3-text-color-*` for text and icons; `--typo3-surface-*` for large backgrounds;
  `--typo3-surface-container-*` for nested component backgrounds; `--typo3-component-*` for generic
  containers; `--typo3-state-*` for interactive or semantic states (default, primary, success, info,
  warning, danger, notice, hover, focus, active, disabled).
- Follow the surface elevation sequence deliberately: `lowest`, `low`, `base`, `high`, `highest`. Do
  not jump to a higher surface just for contrast — use borders, shadows, or state tokens. Keep
  parent and child layers readable in both schemes.
- Pair only foreground/background tokens meant to work together, and use the matching `*-text` token
  on a stateful surface (`--typo3-surface-container-warning-text`). Derive subtle borders with
  `color-mix(...)` and `--typo3-border-mix`.
- Use component-local aliases for named internal roles, for example `--panel-header-bg:
  var(--typo3-surface-container-low)`.

## Shadows and Elevation

- Shadows follow the same layering model as surfaces. Use semantic shadow tokens
  (`--typo3-component-box-shadow` and its `-strong`, `-tooltip`, `-flyout`, `-dialog`, `-window`
  variants), never raw `box-shadow`.
- Use stronger shadows only for higher layers (flyouts, dialogs, popovers, tooltips, overlays,
  windows). Keep shadow strength aligned with surface elevation — a high shadow on a low surface
  is the wrong token. No decorative shadows on non-elevated components.

## Light and Dark Mode

- Every change must work in both schemes. Prefer semantic tokens (surfaces, text, component, input,
  state) over fixed values; use `light-dark(...)` only when a value genuinely differs.
- Mind `color-scheme` and inherited custom properties. Check contrast for text, icons, borders,
  focus outlines, and state backgrounds in both schemes. Make no light-only assumptions.

## Web Components and Element Styles

- Style custom elements from the host selector (`typo3-backend-combobox`) and keep their Sass under
  `element/`. Use custom properties as the styling API, and `::part(...)`, slots, and host
  attributes as stable boundaries — never style arbitrary internal DOM depth. Keep CSS aligned
  with the TypeScript component API and update generated assets together.

## State, Selectors, and Specificity

- Drive state from semantic attributes and explicit APIs (`[disabled]`, `[aria-selected="true"]`,
  `[aria-expanded]`, `[aria-current]`, component-owned `data-*`), mapped to component properties —
  not ad hoc visual classes. ARIA must reflect real state, never serve only as a CSS hook. Keep
  JavaScript, markup, CSS, and tests in sync when state changes.
- Keep specificity low and predictable. Avoid `!important` except to override unavoidable external
  or generated styles. Do not style by brittle DOM depth or visual position when a class, state
  class, or attribute expresses the intent.

## Z-Index and Layering

- Use existing z-index tokens (`--typo3-zindex-dropdown`, `-modal-backdrop`, `-modal`, `-header`);
  never raw numbers when a token fits. Place overlays, flyouts, menus, sticky headers, dialogs, and
  popovers deliberately in the layering model. Never use extreme z-index to win a conflict — fix
  the owning stacking context. Keep z-index, shadow strength, and surface elevation aligned.

## Motion and Transitions

- Prefer existing transition tokens such as `--typo3-transition-color`. New motion that moves,
  resizes, fades large regions, or affects focus/navigation must honor `@media
  (prefers-reduced-motion: reduce)`. Avoid `transition: all` unless already established and
  intentionally constrained. Keep motion subtle and functional, clarifying state rather than
  decorating.

## Container Queries and Responsiveness

- Prefer component-local responsiveness over viewport breakpoints when a component appears in
  different containers: use `container-type: inline-size` plus container queries, scoped to the
  component. Do not add global breakpoints for a component-specific problem. Verify in narrow module
  columns, sidebars, dialogs, and full-width content.

## RTL and Logical Properties

- Check every component for RTL. Use logical properties (`margin-inline-*`, `padding-inline-*`,
  `inset-inline-*`, `border-inline-*`) over physical left/right; avoid new physical-side rules
  unless the side is intentional and documented.
- Use TYPO3 position tokens (`--typo3-position-start`, `-end`, `-start-percent`, `-end-percent`,
  `-modifier`) for direction-aware calculations and transforms. Give explicit RTL review to icons,
  carets, flyouts, drag and resize handles, trees, lists, tables, toolbars, and form controls.

## Accessibility and States

- WCAG contrast must hold in both schemes for text, icons, borders, focus outlines, state
  backgrounds, disabled states, and helper text. Never rely on color alone to communicate state.
- Every interactive element is keyboard-reachable and operable, with visible focus — prefer
  `:focus-visible`, and never remove an outline without an equal-or-better indicator. Focus order
  stays logical; focus traps are intentional and provide a safe escape.
- Hoverable interactive elements have hover styles. Check hover, focus, active, disabled, loading,
  empty, error, selected, expanded, and collapsed states where applicable.
- Use existing TYPO3 focus, outline, state, and border tokens (or component-local properties mapping
  to `--typo3-state-*`). Give drag-and-drop, modal, toolbar, navigation, and form changes extra
  keyboard and screen-reader attention.

## Icons, Text, and Layout Stability

- Components combining icons, labels, badges, carets, and actions must keep layout stable across
  hover, focus, active, loading, and disabled states. Hide decorative icons with
  `aria-hidden="true"`; give icon-only controls an accessible name. Use logical gaps
  (`margin-inline-*`, `gap`) and define overflow behavior for long labels. Allow no avoidable layout
  shift on hover or focus.

## Bootstrap

- Bootstrap is legacy infrastructure being phased out — not the target architecture for new TYPO3
  UI. Prefer TYPO3 component styles and custom properties. Reuse Bootstrap conventions only where
  the surrounding code already depends on them. Add no new high-specificity Bootstrap overrides.
  Many Bootstrap utilities are intentionally disabled, left as explicit commented-out imports so the
  choice stays visible and is never mistaken for an oversight — do not depend on or recreate them.

## Definition of Done

Every item must hold before a CSS change is complete.

- Lives in the correct partial, reuses an existing component where possible, scopes selectors, and
  follows neighboring naming.
- All colors come from semantic `--typo3-*` tokens — no raw hex/RGB/HSL, no `--bs-*`, no direct
  `--token-color-*` — and are light/dark compatible; surfaces and shadows follow the elevation
  sequence with valid foreground/background pairs.
- Variants tune through custom properties; `t3js-*` is used only as a hook; ARIA and `data-*`
  selectors are backed by real state.
- WCAG contrast, visible keyboard focus, logical focus order, hover, and disabled states are
  verified in both schemes; RTL is checked with logical properties.
- New overlays use z-index tokens; new motion respects reduced motion; custom elements are styled
  via host, `::part(...)`, slots, or properties.
- The feature fits the LTS evergreen target (and any backport baseline); a styleguide demo covers
  states, variants, light/dark, and RTL; generated assets are in sync; `build` and `lintScss` were
  run.

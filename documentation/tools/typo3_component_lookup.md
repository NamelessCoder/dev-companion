# `typo3_component_lookup`

Look up TYPO3 backend UI components by name or topic. Where the target is the
active installation, its backend CSS, JavaScript, and installed styleguide
templates supply the component contract; the curated catalog supplies the
searchable names and fallback markup. Without usable installed sources, the
bundled version-bound snapshot answers. Returns markup, classes, custom
properties, and every source used. Answers from: packages, knowledge.

`readOnlyHint: true` · `destructiveHint: false` · `idempotentHint: true` · `openWorldHint: false`

Answers from [`packages`](answer-sources.md#packages),
[`knowledge`](answer-sources.md#knowledge).

## Takes

```yaml
# Component name, class, or topic, for example badge, card, search box, or
# input-group. Omit to list the catalog.
query: string  # optional
# The TYPO3 version the markup has to hold for, for example "13.4" or "14".
# Components not verified there are withheld. Defaults to the version of the
# installation this server was started in; where there is none, the whole
# catalog is returned and every entry carries the versions it was verified on.
targetVersion: string  # optional
```

## Answers with

```yaml
query: string or null  # optional
# The TYPO3 major the answer was composed for — stated by the caller, or read
# from the installation. Null means nothing was withheld and every entry carries
# the versions it was verified on.
targetVersion: integer or null  # optional
# How many components hold on the target version. Ones withheld for it are in
# withheld, not here.
matchCount: integer
components:
  - name: string
    title: string
    summary: string  # optional
    rootClass: string
    variants: [string]  # optional
    modifiers: [string]  # optional
    subComponents: [string]  # optional
    customProperties: [string]  # optional
    # Canonical markup of the component.
    markup: string  # optional
    examples: [string]  # optional
    # Primary Sass source in the core checkout; null for a web component that
    # carries its own styles.
    sassPath: string or null
    # Every Sass source the component spans. A component can be split across
    # several files.
    sassPaths: [string]  # optional
    # Styleguide demo in the core checkout, if there is one.
    demoPath: string or null
    # Where the query matched: name, keywords, sub-component classes,
    # description.
    matchedIn: [string]  # optional
    # Every class of this component found in the installed backend CSS. Empty
    # for a bundled fallback or a custom element without external CSS.
    classes: [string]
    # Installed package files consulted for the component contract. Empty for
    # the bundled fallback.
    sourceFiles: [string]
    # One of: installation, catalog. Whether markup came from an installed
    # styleguide example or the bundled curated fallback.
    markupSource: string
    # TYPO3 version whose classes and custom properties this entry describes.
    contractVersion: string
    # TYPO3 version whose markup this entry describes. It can differ from
    # contractVersion when the installed styleguide has no matching example and
    # bundled markup is the fallback.
    describesVersion: string
    # The TYPO3 major this entry starts holding at, or null when it holds on
    # every covered version.
    since: integer or null  # optional
    # The TYPO3 major it stops holding after, or null when nothing has replaced
    # it.
    until: integer or null  # optional
    # The same range as a sentence, empty when the entry holds on every covered
    # version.
    verifiedOn: string
# Components this catalog has but was never verified on the target version. Left
# out of components rather than handed over — an empty answer here means "not
# verified where you are", not "does not exist".
withheld:
  - name: string
    title: string
    # What to verify the entry against on the target version.
    sassPaths: [string]  # optional
    demoPath: string or null  # optional
    # The TYPO3 major this entry starts holding at, or null when it holds on
    # every covered version.
    since: integer or null  # optional
    # The TYPO3 major it stops holding after, or null when nothing has replaced
    # it.
    until: integer or null  # optional
    # The same range as a sentence, empty when the entry holds on every covered
    # version.
    verifiedOn: string
checklist:  # optional
  title: string
  intro: string  # optional
  items: [string]
# One of: installation, catalog. installation when the class and custom-property
# contract was read from the active TYPO3 packages; catalog when the bundled
# snapshot answered.
componentSource: string
# The core revision behind catalog answers, and how it relates to the
# installation being read. A miss means "not in this snapshot".
catalog:
  repository: string  # optional
  branch: string
  # TYPO3 version of the snapshot.
  version: string
  # Core revision the catalogs were taken from.
  commit: string
  verifiedAt: string
  # TYPO3 version of the installation this server was started in, where there is
  # one. Null means there was nothing to compare the snapshot with.
  installedVersion: string or null  # optional
  # Set when that installation and the snapshot are different TYPO3 majors, and
  # what to do about it. Null when they agree or nothing is known.
  skew: string or null  # optional
```

## Answered

Recorded on 2026-08-03 by `bin/cli tools:record`. Answered against
core-checkout, TYPO3 14.3.6-dev, the 14.3 core checkout below .checkouts/,
whose console could not be reached: <installation> has no TYPO3 console —
none of bin/typo3, vendor/bin/typo3 exists. Nothing checks what is below this
heading; everything above it is derived from the class that answers the call,
and `bin/cli tools:check` holds it.

### components: list

Called with:

```json
{}
```

Text:

```
TYPO3 backend component catalog:
- alert — Flash Messages / Alerts
- avatar — Avatar
- badge — Badges
- breadcrumb — Breadcrumbs
- button — Buttons
- button-group — Button Groups
- callout — Callout
- card — Cards
- dropdown — Dropdown
- dropzone — Dropzone
- form-check — Checkboxes, Radios, Switches
- infobox — Infobox (ViewHelper)
- input — Form Inputs
- list-group — List Groups
- modal — Modal
- nav — Navs / Tabs
- note — Notes
- pagination — Pagination
- panel — Panels
- popover — Popover
- progress-bar — Progress Indicators
- recordsearchbox — Record Search Box
- status-indicator — Status Indicator
- table — Tables
- tree — Trees

Component contract: installed TYPO3 14.3.6-dev packages. Names, summaries, keywords, and fallback markup come from the curated catalog; classes and custom properties come from EXT:backend/Resources/Public/Css/backend.css, and an installed styleguide example replaces the fallback markup where available.
```

Data:

```json
{
    "query": null,
    "targetVersion": 14,
    "matchCount": 25,
    "components": [
        {
            "name": "alert",
            "title": "Flash Messages / Alerts",
            "summary": "Dismissible status message, used for backend flash messages. Variants map to TYPO3 state tokens.",
            "rootClass": "alert",
            "sassPath": "Build/Sources/Sass/component/_alert.scss",
            "sassPaths": [
                "Build/Sources/Sass/component/_alert.scss"
            ],
            "demoPath": "typo3/sysext/styleguide/Resources/Private/Templates/Backend/Components/FlashMessages.fluid.html",
            "classes": [
                "alert",
                "alert-actions",
                "alert-body",
                "alert-content",
                "alert-danger",
                "alert-default",
                "alert-dismissible",
                "alert-info",
                "alert-inner",
                "alert-list",
                "alert-message",
                "alert-notice",
                "alert-primary",
                "alert-secondary",
                "alert-success",
                "alert-title",
                "alert-warning"
            ],
            "sourceFiles": [
                "EXT:backend/Resources/Public/Css/backend.css",
                "Build/Sources/Sass/component/_alert.scss",
                "EXT:styleguide/Resources/Private/Templates/Backend/Components/FlashMessages.fluid.html"
            ],
            "markupSource": "installation",
            "contractVersion": "14.3.6-dev",
            "describesVersion": "14.3.6-dev",
            "since": 13,
            "until": null,
            "verifiedOn": "TYPO3 v13 and newer"
        },
        {
            "name": "avatar",
            "title": "Avatar",
            "summary": "User/record avatar image with an optional icon overlay. Rendered via the backend avatar ViewHelper; sizes are modifier classes.",
            "rootClass": "avatar",
            "sassPath": "Build/Sources/Sass/component/_avatar.scss",
            "sassPaths": [
                "Build/Sources/Sass/component/_avatar.scss"
            ],
            "demoPath": "typo3/sysext/styleguide/Resources/Private/Templates/Backend/Components/Avatar.fluid.html",
            "classes": [
                "avatar",
                "avatar-icon",
                "avatar-image",
                "avatar-size-large",
                "avatar-size-medium",
                "avatar-size-mega",
                "avatar-size-small"
            ],
            "sourceFiles": [
                "EXT:backend/Resources/Public/Css/backend.css",
                "Build/Sources/Sass/component/_avatar.scss",
                "EXT:styleguide/Resources/Private/Templates/Backend/Components/Avatar.fluid.html"
            ],
            "markupSource": "installation",
            "contractVersion": "14.3.6-dev",
            "describesVersion": "14.3.6-dev",
            "since": null,
            "until": null,
            "verifiedOn": ""
        },
        {
            "name": "badge",
            "title": "Badges",
            "summary": "Small inline status, label, or count indicator. Variants map to TYPO3 state tokens.",
            "rootClass": "badge",
            "sassPath": "Build/Sources/Sass/component/_badges.scss",
            "sassPaths": [
                "Build/Sources/Sass/component/_badges.scss"
            ],
            "demoPath": "typo3/sysext/styleguide/Resources/Private/Templates/Backend/Components/Badges.fluid.html",
            "classes": [
                "badge",
                "badge-alpha",
                "badge-beta",
                "badge-danger",
                "badge-default",
                "badge-deprecated",
                "badge-experimental",
                "badge-info",
                "badge-list",
                "badge-notice",
                "badge-pill",
                "badge-primary",
                "badge-secondary",
                "badge-space-end",
                "badge-space-start",
                "badge-stable",
                "badge-success",
                "badge-warning"
            ],
            "sourceFiles": [
                "EXT:backend/Resources/Public/Css/backend.css",
                "Build/Sources/Sass/component/_badges.scss",
                "EXT:styleguide/Resources/Private/Templates/Backend/Components/Badges.fluid.html"
            ],
            "markupSource": "installation",
            "contractVersion": "14.3.6-dev",
            "describesVersion": "14.3.6-dev",
            "since": 13,
            "until": null,
            "verifiedOn": "TYPO3 v13 and newer"
        },
        {
            "name": "breadcrumb",
            "title": "Breadcrumbs",
            "summary": "Hierarchical navigation trail. Mark the current page item with .active and aria-current=\"page\".",
            "rootClass": "breadcrumb",
            "sassPath": "Build/Sources/Sass/component/_breadcrumb.scss",
            "sassPaths": [
                "Build/Sources/Sass/component/_breadcrumb.scss"
            ],
            "demoPath": "typo3/sysext/styleguide/Resources/Private/Templates/Backend/Components/Breadcrumbs.fluid.html",
            "classes": [
                "breadcrumb",
                "breadcrumb-collapsible",
                "breadcrumb-condensed",
                "breadcrumb-element",
                "breadcrumb-element-label",
                "breadcrumb-item",
                "breadcrumb-item-last",
                "breadcrumb-measurement",
                "breadcrumb-right"
            ],
            "sourceFiles": [
                "EXT:backend/Resources/Public/Css/backend.css",
                "Build/Sources/Sass/component/_breadcrumb.scss",
                "EXT:styleguide/Resources/Private/Templates/Backend/Components/Breadcrumbs.fluid.html"
            ],
            "markupSource": "installation",
            "contractVersion": "14.3.6-dev",
            "describesVersion": "14.3.6-dev",
            "since": 14,
            "until": null,
            "verifiedOn": "TYPO3 v14 and newer"
        },
        {
            "name": "button",
            "title": "Buttons",
            "summary": "Standard backend button. Variants map to TYPO3 state tokens; combine with icons via the core icon ViewHelper.",
            "rootClass": "btn",
            "sassPath": "Build/Sources/Sass/component/_buttons.scss",
            "sassPaths": [
                "Build/Sources/Sass/component/_buttons.scss"
            ],
            "demoPath": "typo3/sysext/styleguide/Resources/Private/Templates/Backend/Components/Buttons.fluid.html",
            "classes": [
                "btn",
                "btn-align-end",
                "btn-align-start",
                "btn-block",
                "btn-block-vertical",
                "btn-borderless",
                "btn-check",
                "btn-configuration-map-add",
                "btn-danger",
                "btn-default",
                "btn-group",
                "btn-group-sm",
                "btn-group-vertical",
                "btn-icon",
                "btn-info",
                "btn-link",
                "btn-login",
                "btn-notice",
                "btn-primary",
                "btn-secondary",
                "btn-sm",
                "btn-success",
                "btn-toolbar",
                "btn-toolbar-nowrap",
                "btn-warning"
            ],
            "sourceFiles": [
                "EXT:backend/Resources/Public/Css/backend.css",
                "Build/Sources/Sass/component/_buttons.scss",
                "EXT:styleguide/Resources/Private/Templates/Backend/Components/Buttons.fluid.html"
            ],
            "markupSource": "installation",
            "contractVersion": "14.3.6-dev",
            "describesVersion": "14.3.6-dev",
            "since": 13,
            "until": null,
            "verifiedOn": "TYPO3 v13 and newer"
        },
        {
            "name": "button-group",
            "title": "Button Groups",
            "summary": "Groups buttons into a single segmented control or a spaced toolbar.",
            "rootClass": "btn-group",
            "sassPath": "Build/Sources/Sass/component/_button-group.scss",
            "sassPaths": [
                "Build/Sources/Sass/component/_button-group.scss"
            ],
            "demoPath": "typo3/sysext/styleguide/Resources/Private/Templates/Backend/Components/Buttons.fluid.html",
            "classes": [
                "btn-group",
                "btn-group-sm",
                "btn-group-vertical",
                "btn-toolbar",
                "btn-toolbar-nowrap"
            ],
            "sourceFiles": [
                "EXT:backend/Resources/Public/Css/backend.css",
                "Build/Sources/Sass/component/_button-group.scss"
            ],
            "markupSource": "catalog",
            "contractVersion": "14.3.6-dev",
            "describesVersion": "15.0",
            "since": 14,
            "until": null,
            "verifiedOn": "TYPO3 v14 and newer"
        },
        {
            "name": "callout",
            "title": "Callout",
            "summary": "Inline contextual message block with a leading icon. Backing component for the be.infobox ViewHelper. Variants map to TYPO3 state tokens.",
            "rootClass": "callout",
            "sassPath": "Build/Sources/Sass/component/_callout.scss",
            "sassPaths": [
                "Build/Sources/Sass/component/_callout.scss"
            ],
            "demoPath": "typo3/sysext/styleguide/Resources/Private/Templates/Backend/Components/Infobox.fluid.html",
            "classes": [
                "callout",
                "callout-body",
                "callout-content",
                "callout-danger",
                "callout-default",
                "callout-icon",
                "callout-info",
                "callout-notice",
                "callout-primary",
                "callout-secondary",
                "callout-sm",
                "callout-success",
                "callout-title",
                "callout-warning"
            ],
            "sourceFiles": [
                "EXT:backend/Resources/Public/Css/backend.css",
                "Build/Sources/Sass/component/_callout.scss",
                "EXT:styleguide/Resources/Private/Templates/Backend/Components/Infobox.fluid.html"
            ],
            "markupSource": "installation",
            "contractVersion": "14.3.6-dev",
            "describesVersion": "14.3.6-dev",
            "since": 13,
            "until": null,
            "verifiedOn": "TYPO3 v13 and newer"
        },
        {
            "name": "card",
            "title": "Cards",
            "summary": "Flexible content container for grouping elements. Compose the container with optional header, body, and footer child elements.",
            "rootClass": "card",
            "sassPath": "Build/Sources/Sass/component/_card.scss",
            "sassPaths": [
                "Build/Sources/Sass/component/_card.scss"
            ],
            "demoPath": "typo3/sysext/styleguide/Resources/Private/Templates/Backend/Components/Cards.fluid.html",
            "classes": [
                "card",
                "card-body",
                "card-container",
                "card-danger",
                "card-default",
                "card-disabled",
                "card-footer",
                "card-header",
                "card-header-body",
                "card-heading",
                "card-icon",
                "card-image",
                "card-image-badge",
                "card-info",
                "card-login",
                "card-longdesc",
                "card-mfa",
                "card-notice",
                "card-primary",
                "card-secondary",
                "card-size-large",
                "card-size-medium",
                "card-subtitle",
                "card-success",
                "card-title",
                "card-warning"
            ],
            "sourceFiles": [
                "EXT:backend/Resources/Public/Css/backend.css",
                "Build/Sources/Sass/component/_card.scss",
                "EXT:styleguide/Resources/Private/Templates/Backend/Components/Cards.fluid.html"
            ],
            "markupSource": "installation",
            "contractVersion": "14.3.6-dev",
            "describesVersion": "14.3.6-dev",
            "since": 13,
            "until": null,
            "verifiedOn": "TYPO3 v13 and newer"
        },
        {
            "name": "dropdown",
            "title": "Dropdown",
            "summary": "Toggleable menu, built on the native Popover API (popovertarget/popover). Use dropdown-toggle-no-chevron or dropdown-toggle-link for variants.",
            "rootClass": "dropdown",
            "sassPath": "Build/Sources/Sass/component/_dropdown.scss",
            "sassPaths": [
                "Build/Sources/Sass/component/_dropdown.scss"
            ],
            "demoPath": "typo3/sysext/styleguide/Resources/Private/Templates/Backend/Components/Dropdown.fluid.html",
            "classes": [
                "dropdown",
                "dropdown-divider",
                "dropdown-header",
                "dropdown-headline",
                "dropdown-item",
                "dropdown-item-action",
                "dropdown-item-column-icon",
                "dropdown-item-column-text",
                "dropdown-item-column-title",
                "dropdown-item-column-title-info",
                "dropdown-item-column-value",
                "dropdown-item-columns",
                "dropdown-item-spaced",
                "dropdown-item-status",
                "dropdown-item-text",
                "dropdown-list",
                "dropdown-menu",
                "dropdown-row",
                "dropdown-table",
                "dropdown-toggle",
                "dropdown-toggle-link",
                "dropdown-toggle-no-chevron",
                "dropdown-toggle-split"
            ],
            "sourceFiles": [
                "EXT:backend/Resources/Public/Css/backend.css",
                "Build/Sources/Sass/component/_dropdown.scss"
            ],
            "markupSource": "catalog",
            "contractVersion": "14.3.6-dev",
            "describesVersion": "15.0",
            "since": 13,
            "until": null,
            "verifiedOn": "TYPO3 v13 and newer"
        },
        {
            "name": "dropzone",
            "title": "Dropzone",
            "summary": "Drop target for file uploads. The .dropzone box holds a .dropzone-hint with icon, title and message, a .dropzone-mask covering the drop area, and a .dropzone-close button. Upload state is switched by adding .drop-status-ok or .drop-in-progress.",
            "rootClass": "dropzone",
            "sassPath": "Build/Sources/Sass/component/_dropzone.scss",
            "sassPaths": [
                "Build/Sources/Sass/component/_dropzone.scss"
            ],
            "demoPath": null,
            "classes": [
                "drop-in-progress",
                "drop-status-ok",
                "dropzone",
                "dropzone-close",
                "dropzone-hint",
                "dropzone-hint-body",
                "dropzone-hint-icon",
                "dropzone-hint-title",
                "dropzone-mask"
            ],
            "sourceFiles": [
                "EXT:backend/Resources/Public/Css/backend.css",
                "Build/Sources/Sass/component/_dropzone.scss"
            ],
            "markupSource": "catalog",
            "contractVersion": "14.3.6-dev",
            "describesVersion": "15.0",
            "since": 13,
            "until": null,
            "verifiedOn": "TYPO3 v13 and newer"
        },
        {
            "name": "form-check",
            "title": "Checkboxes, Radios, Switches",
            "summary": "Checkbox/radio control with several presentation types: default, switch, toggle, icon-toggle, and card. Pair .form-check-input with .form-check-label.",
            "rootClass": "form-check",
            "sassPath": "Build/Sources/Sass/component/forms/_form-check.scss",
            "sassPaths": [
                "Build/Sources/Sass/component/forms/_form-check.scss"
            ],
            "demoPath": "typo3/sysext/styleguide/Resources/Private/Templates/Backend/Components/Checkboxes.fluid.html",
            "classes": [
                "form-check",
                "form-check-card-container",
                "form-check-card-container-headline",
                "form-check-card-container-small",
                "form-check-inline",
                "form-check-input",
                "form-check-label",
                "form-check-label-body",
                "form-check-label-header",
                "form-check-label-header-inherit",
                "form-check-label-icon",
                "form-check-label-icon-checked",
                "form-check-label-icon-indeterminate",
                "form-check-label-icon-unchecked",
                "form-check-label-label-body",
                "form-check-size-input",
                "form-check-type-card",
                "form-check-type-icon-toggle",
                "form-check-type-labeled-toggle",
                "form-check-type-toggle",
                "form-switch"
            ],
            "sourceFiles": [
                "EXT:backend/Resources/Public/Css/backend.css",
                "Build/Sources/Sass/component/forms/_form-check.scss",
                "EXT:styleguide/Resources/Private/Templates/Backend/Components/Checkboxes.fluid.html"
            ],
            "markupSource": "installation",
            "contractVersion": "14.3.6-dev",
            "describesVersion": "14.3.6-dev",
            "since": 14,
            "until": null,
            "verifiedOn": "TYPO3 v14 and newer"
        },
        {
            "name": "infobox",
            "title": "Infobox (ViewHelper)",
            "summary": "Fluid ViewHelper that renders a callout. Use the state argument with a ContextualFeedbackSeverity enum (NOTICE->notice, INFO->info, OK->success, WARNING->warning, ERROR->danger).",
            "rootClass": "callout",
            "sassPath": "Build/Sources/Sass/component/_callout.scss",
            "sassPaths": [
                "Build/Sources/Sass/component/_callout.scss"
            ],
            "demoPath": "typo3/sysext/styleguide/Resources/Private/Templates/Backend/Components/Infobox.fluid.html",
            "classes": [
                "callout",
                "callout-body",
                "callout-content",
                "callout-danger",
                "callout-default",
                "callout-icon",
                "callout-info",
                "callout-notice",
                "callout-primary",
                "callout-secondary",
                "callout-sm",
                "callout-success",
                "callout-title",
                "callout-warning"
            ],
            "sourceFiles": [
                "EXT:backend/Resources/Public/Css/backend.css",
                "Build/Sources/Sass/component/_callout.scss",
                "EXT:styleguide/Resources/Private/Templates/Backend/Components/Infobox.fluid.html"
            ],
            "markupSource": "installation",
            "contractVersion": "14.3.6-dev",
            "describesVersion": "14.3.6-dev",
            "since": null,
            "until": null,
            "verifiedOn": ""
        },
        {
            "name": "input",
            "title": "Form Inputs",
            "summary": "Text/select/textarea form controls. Use the .form-control class; backend forms render via the Backend/Form Fluid partials (Input, Select, Textarea, Combobox).",
            "rootClass": "form-control",
            "sassPath": "Build/Sources/Sass/component/forms/_form-control.scss",
            "sassPaths": [
                "Build/Sources/Sass/component/forms/_form-control.scss",
                "Build/Sources/Sass/component/forms/_form-label.scss",
                "Build/Sources/Sass/component/forms/_form-text.scss",
                "Build/Sources/Sass/component/forms/_input-group.scss"
            ],
            "demoPath": "typo3/sysext/styleguide/Resources/Private/Templates/Backend/Components/Input.fluid.html",
            "classes": [
                "form-control",
                "form-control-adapt",
                "form-control-clearable",
                "form-control-clearable-wrapper",
                "form-control-explanation",
                "form-control-holder",
                "form-control-sm",
                "form-control-wrap",
                "form-label",
                "form-text",
                "input-group",
                "input-group-icon",
                "input-group-sm",
                "input-group-text",
                "input-grouped",
                "input-login"
            ],
            "sourceFiles": [
                "EXT:backend/Resources/Public/Css/backend.css",
                "Build/Sources/Sass/component/forms/_form-control.scss",
                "Build/Sources/Sass/component/forms/_form-label.scss",
                "Build/Sources/Sass/component/forms/_form-text.scss",
                "Build/Sources/Sass/component/forms/_input-group.scss"
            ],
            "markupSource": "catalog",
            "contractVersion": "14.3.6-dev",
            "describesVersion": "15.0",
            "since": 13,
            "until": null,
            "verifiedOn": "TYPO3 v13 and newer"
        },
        {
            "name": "list-group",
            "title": "List Groups",
            "summary": "Vertical list of items with optional action/link, active, and disabled states.",
            "rootClass": "list-group",
            "sassPath": "Build/Sources/Sass/component/_list-group.scss",
            "sassPaths": [
                "Build/Sources/Sass/component/_list-group.scss"
            ],
            "demoPath": "typo3/sysext/styleguide/Resources/Private/Templates/Backend/Components/ListGroups.fluid.html",
            "classes": [
                "list-group",
                "list-group-button",
                "list-group-flush",
                "list-group-item",
                "list-group-item-action"
            ],
            "sourceFiles": [
                "EXT:backend/Resources/Public/Css/backend.css",
                "Build/Sources/Sass/component/_list-group.scss",
                "EXT:styleguide/Resources/Private/Templates/Backend/Components/ListGroups.fluid.html"
            ],
            "markupSource": "installation",
            "contractVersion": "14.3.6-dev",
            "describesVersion": "14.3.6-dev",
            "since": 14,
            "until": null,
            "verifiedOn": "TYPO3 v14 and newer"
        },
        {
            "name": "modal",
            "title": "Modal",
            "summary": "Dialog overlay, usually opened programmatically via the t3js-modal-trigger hook and the Modal module. Sizes and positions are set with modifier classes / data attributes.",
            "rootClass": "modal",
            "sassPath": "Build/Sources/Sass/component/_modal.scss",
            "sassPaths": [
                "Build/Sources/Sass/component/_modal.scss"
            ],
            "demoPath": "typo3/sysext/styleguide/Resources/Private/Templates/Backend/Components/Modal.fluid.html",
            "classes": [
                "modal",
                "modal-body",
                "modal-closing",
                "modal-footer",
                "modal-header",
                "modal-header-close",
                "modal-height-default",
                "modal-height-full",
                "modal-height-large",
                "modal-height-medium",
                "modal-height-small",
                "modal-iframe",
                "modal-image-manipulation",
                "modal-loading",
                "modal-multi-step-wizard",
                "modal-panel-main",
                "modal-panel-sidebar",
                "modal-position-bottom",
                "modal-position-center",
                "modal-position-end",
                "modal-position-sheet",
                "modal-position-start",
                "modal-position-top",
                "modal-progress",
                "modal-severity-danger",
                "modal-severity-default",
                "modal-severity-info",
                "modal-severity-notice",
                "modal-severity-primary",
                "modal-severity-secondary",
                "modal-severity-success",
                "modal-severity-warning",
                "modal-size-expand",
                "modal-size-full",
                "modal-size-large",
                "modal-size-medium",
                "modal-size-small",
                "modal-style-dark",
                "modal-style-light",
                "modal-type-iframe",
                "modal-width-default",
                "modal-width-full",
                "modal-width-large",
                "modal-width-medium",
                "modal-width-small"
            ],
            "sourceFiles": [
                "EXT:backend/Resources/Public/Css/backend.css",
                "Build/Sources/Sass/component/_modal.scss",
                "EXT:styleguide/Resources/Private/Templates/Backend/Components/Modal.fluid.html"
            ],
            "markupSource": "installation",
            "contractVersion": "14.3.6-dev",
            "describesVersion": "14.3.6-dev",
            "since": 14,
            "until": null,
            "verifiedOn": "TYPO3 v14 and newer"
        },
        {
            "name": "nav",
            "title": "Navs / Tabs",
            "summary": "Navigation list, with .nav-tabs and .nav-pills variants. The tab pattern adds role=\"tablist\"/role=\"tab\", aria-selected, and data-typo3-tab wiring.",
            "rootClass": "nav",
            "sassPath": "Build/Sources/Sass/component/_nav.scss",
            "sassPaths": [
                "Build/Sources/Sass/component/_nav.scss"
            ],
            "demoPath": "typo3/sysext/styleguide/Resources/Private/Templates/Backend/Components/Navs.fluid.html",
            "classes": [
                "nav",
                "nav-item",
                "nav-link",
                "nav-pills",
                "nav-tabs",
                "nav-tabs-scroll",
                "nav-tabs-scroll-end",
                "nav-tabs-scroll-start",
                "tab-content"
            ],
            "sourceFiles": [
                "EXT:backend/Resources/Public/Css/backend.css",
                "Build/Sources/Sass/component/_nav.scss",
                "EXT:styleguide/Resources/Private/Templates/Backend/Components/Navs.fluid.html"
            ],
            "markupSource": "installation",
            "contractVersion": "14.3.6-dev",
            "describesVersion": "14.3.6-dev",
            "since": 14,
            "until": null,
            "verifiedOn": "TYPO3 v14 and newer"
        },
        {
            "name": "note",
            "title": "Notes",
            "summary": "Editor-facing note box with a header bar and a body. Variants follow the TYPO3 state tokens; the note-category-<n> classes map the sys_note record categories onto them.",
            "rootClass": "note",
            "sassPath": "Build/Sources/Sass/component/_note.scss",
            "sassPaths": [
                "Build/Sources/Sass/component/_note.scss"
            ],
            "demoPath": null,
            "classes": [
                "note",
                "note-actions",
                "note-body",
                "note-category-0",
                "note-category-1",
                "note-category-2",
                "note-category-3",
                "note-category-4",
                "note-danger",
                "note-default",
                "note-header",
                "note-header-bar",
                "note-info",
                "note-list",
                "note-notice",
                "note-primary",
                "note-secondary",
                "note-success",
                "note-warning"
            ],
            "sourceFiles": [
                "EXT:backend/Resources/Public/Css/backend.css",
                "Build/Sources/Sass/component/_note.scss"
            ],
            "markupSource": "catalog",
            "contractVersion": "14.3.6-dev",
            "describesVersion": "15.0",
            "since": 13,
            "until": null,
            "verifiedOn": "TYPO3 v13 and newer"
        },
        {
            "name": "pagination",
            "title": "Pagination",
            "summary": "Page navigation for paged record lists. Mark the current page with .active and unavailable controls with .disabled.",
            "rootClass": "pagination",
            "sassPath": "Build/Sources/Sass/component/_pagination.scss",
            "sassPaths": [
                "Build/Sources/Sass/component/_pagination.scss"
            ],
            "demoPath": "typo3/sysext/styleguide/Resources/Private/Templates/Backend/Components/Pagination.fluid.html",
            "classes": [
                "page-item",
                "page-link",
                "pagination",
                "paginator-input"
            ],
            "sourceFiles": [
                "EXT:backend/Resources/Public/Css/backend.css",
                "Build/Sources/Sass/component/_pagination.scss",
                "EXT:styleguide/Resources/Private/Templates/Backend/Components/Pagination.fluid.html"
            ],
            "markupSource": "installation",
            "contractVersion": "14.3.6-dev",
            "describesVersion": "14.3.6-dev",
            "since": 13,
            "until": null,
            "verifiedOn": "TYPO3 v13 and newer"
        },
        {
            "name": "panel",
            "title": "Panels",
            "summary": "Collapsible titled container with header, body, footer. Type variants (feature, important, breaking, deprecation) carry semantic emphasis.",
            "rootClass": "panel",
            "sassPath": "Build/Sources/Sass/component/_panel.scss",
            "sassPaths": [
                "Build/Sources/Sass/component/_panel.scss"
            ],
            "demoPath": "typo3/sysext/styleguide/Resources/Private/Templates/Backend/Components/Panels.fluid.html",
            "classes": [
                "panel",
                "panel-actions",
                "panel-active",
                "panel-badge",
                "panel-body",
                "panel-body-overflow",
                "panel-breaking",
                "panel-button",
                "panel-collapse",
                "panel-condensed",
                "panel-danger",
                "panel-default",
                "panel-deprecation",
                "panel-feature",
                "panel-footer",
                "panel-group",
                "panel-has-progress",
                "panel-heading",
                "panel-heading-column",
                "panel-heading-row",
                "panel-heading-row-spread",
                "panel-hidden",
                "panel-icon",
                "panel-important",
                "panel-info",
                "panel-list",
                "panel-loader",
                "panel-meta",
                "panel-notice",
                "panel-placeholder",
                "panel-primary",
                "panel-progress",
                "panel-progress-bar",
                "panel-rst",
                "panel-secondary",
                "panel-success",
                "panel-thumbnail",
                "panel-title",
                "panel-version",
                "panel-warning"
            ],
            "sourceFiles": [
                "EXT:backend/Resources/Public/Css/backend.css",
                "Build/Sources/Sass/component/_panel.scss",
                "EXT:styleguide/Resources/Private/Templates/Backend/Components/Panels.fluid.html"
            ],
            "markupSource": "catalog",
            "contractVersion": "14.3.6-dev",
            "describesVersion": "15.0",
            "since": 14,
            "until": null,
            "verifiedOn": "TYPO3 v14 and newer"
        },
        {
            "name": "popover",
            "title": "Popover",
            "summary": "Small overlay attached to a trigger element, used for contextual help. Elevated with the flyout shadow token.",
            "rootClass": "popover",
            "sassPath": "Build/Sources/Sass/component/_popover.scss",
            "sassPaths": [
                "Build/Sources/Sass/component/_popover.scss"
            ],
            "demoPath": null,
            "classes": [
                "popover",
                "popover-arrow",
                "popover-body",
                "popover-header"
            ],
            "sourceFiles": [
                "EXT:backend/Resources/Public/Css/backend.css",
                "Build/Sources/Sass/component/_popover.scss"
            ],
            "markupSource": "catalog",
            "contractVersion": "14.3.6-dev",
            "describesVersion": "15.0",
            "since": 13,
            "until": null,
            "verifiedOn": "TYPO3 v13 and newer"
        },
        {
            "name": "progress-bar",
            "title": "Progress Indicators",
            "summary": "Backend progress bar web component. Set value/max and an accessible label; severity (-1 info, 0 ok, 1 warning, 2 error) colors the bar.",
            "rootClass": "typo3-backend-progress-bar",
            "sassPath": null,
            "sassPaths": [],
            "demoPath": "typo3/sysext/styleguide/Resources/Private/Templates/Backend/Components/ProgressIndicators.fluid.html",
            "classes": [],
            "sourceFiles": [
                "EXT:backend/Resources/Public/Css/backend.css",
                "EXT:backend/Resources/Public/JavaScript/drag-uploader.js",
                "EXT:styleguide/Resources/Private/Templates/Backend/Components/ProgressIndicators.fluid.html"
            ],
            "markupSource": "installation",
            "contractVersion": "14.3.6-dev",
            "describesVersion": "14.3.6-dev",
            "since": 13,
            "until": null,
            "verifiedOn": "TYPO3 v13 and newer"
        },
        {
            "name": "recordsearchbox",
            "title": "Record Search Box",
            "summary": "Backend search input with an optional levels selector, built on the Bootstrap input-group. Used to filter record listings; not a styleguide component.",
            "rootClass": "recordsearchbox-container",
            "sassPath": "Build/Sources/Sass/component/_recordsearchbox.scss",
            "sassPaths": [
                "Build/Sources/Sass/component/_recordsearchbox.scss"
            ],
            "demoPath": "typo3/sysext/backend/Resources/Private/Templates/RecordSearchBox.fluid.html",
            "classes": [
                "form-control",
                "input-group",
                "recordsearchbox-container"
            ],
            "sourceFiles": [
                "EXT:backend/Resources/Public/Css/backend.css",
                "Build/Sources/Sass/component/_recordsearchbox.scss",
                "EXT:backend/Resources/Private/Templates/RecordSearchBox.fluid.html"
            ],
            "markupSource": "catalog",
            "contractVersion": "14.3.6-dev",
            "describesVersion": "15.0",
            "since": null,
            "until": null,
            "verifiedOn": ""
        },
        {
            "name": "status-indicator",
            "title": "Status Indicator",
            "summary": "Small dot flagging the state of a record, task, or interface element. It is the <typo3-backend-status-indicator> custom element from @typo3/backend/element/status-indicator-element; the state attribute picks the colour, live and loading add the animated ring.",
            "rootClass": "typo3-backend-status-indicator",
            "sassPath": "Build/Sources/Sass/component/_status-indicator.scss",
            "sassPaths": [
                "Build/Sources/Sass/component/_status-indicator.scss"
            ],
            "demoPath": "typo3/sysext/styleguide/Resources/Private/Templates/Backend/Components/StatusIndicators.fluid.html",
            "classes": [
                "status-indicator",
                "status-indicator-active",
                "status-indicator-danger",
                "status-indicator-default",
                "status-indicator-disabled",
                "status-indicator-info",
                "status-indicator-live",
                "status-indicator-loading",
                "status-indicator-notice",
                "status-indicator-online",
                "status-indicator-primary",
                "status-indicator-running",
                "status-indicator-secondary",
                "status-indicator-success",
                "status-indicator-warning"
            ],
            "sourceFiles": [
                "EXT:backend/Resources/Public/Css/backend.css",
                "EXT:backend/Resources/Public/JavaScript/element/status-indicator-element.js",
                "Build/Sources/Sass/component/_status-indicator.scss"
            ],
            "markupSource": "catalog",
            "contractVersion": "14.3.6-dev",
            "describesVersion": "15.0",
            "since": 14,
            "until": null,
            "verifiedOn": "TYPO3 v14 and newer"
        },
        {
            "name": "table",
            "title": "Tables",
            "summary": "Data table. Wrap in .table-fit for horizontal overflow. Modifier classes add striping, borders, hover, and compact sizing; row state classes mark active/selected rows.",
            "rootClass": "table",
            "sassPath": "Build/Sources/Sass/component/_table.scss",
            "sassPaths": [
                "Build/Sources/Sass/component/_table.scss"
            ],
            "demoPath": "typo3/sysext/styleguide/Resources/Private/Templates/Backend/Components/Tables.fluid.html",
            "classes": [
                "table",
                "table-active",
                "table-available",
                "table-bordered",
                "table-center",
                "table-danger",
                "table-default",
                "table-fit",
                "table-fit-inline-block",
                "table-fit-wrap",
                "table-hover",
                "table-info",
                "table-insecure",
                "table-installed",
                "table-notice",
                "table-outdated",
                "table-primary",
                "table-secondary",
                "table-selected",
                "table-sm",
                "table-sorting-button",
                "table-sorting-button-active",
                "table-sorting-icon",
                "table-striped",
                "table-striped-columns",
                "table-success",
                "table-transparent",
                "table-vertical-top",
                "table-warning"
            ],
            "sourceFiles": [
                "EXT:backend/Resources/Public/Css/backend.css",
                "Build/Sources/Sass/component/_table.scss",
                "EXT:styleguide/Resources/Private/Templates/Backend/Components/Tables.fluid.html"
            ],
            "markupSource": "installation",
            "contractVersion": "14.3.6-dev",
            "describesVersion": "14.3.6-dev",
            "since": 14,
            "until": null,
            "verifiedOn": "TYPO3 v14 and newer"
        },
        {
            "name": "tree",
            "title": "Trees",
            "summary": "SVG-based hierarchical tree (page tree, file tree) rendered by the backend tree web components. Node classes style the rows; styling is mostly internal to the component.",
            "rootClass": "node",
            "sassPath": "Build/Sources/Sass/component/_tree.scss",
            "sassPaths": [
                "Build/Sources/Sass/component/_tree.scss"
            ],
            "demoPath": "typo3/sysext/styleguide/Resources/Private/Templates/Backend/Components/Trees.fluid.html",
            "classes": [
                "node",
                "node-action",
                "node-active",
                "node-content",
                "node-contentlabel",
                "node-disabled",
                "node-dragging",
                "node-dragging-after",
                "node-dragging-before",
                "node-dropzone-delete",
                "node-edit",
                "node-focus",
                "node-highlight-text",
                "node-hover",
                "node-icon",
                "node-information",
                "node-information-danger",
                "node-information-info",
                "node-information-success",
                "node-information-warning",
                "node-label",
                "node-loading",
                "node-mount-point",
                "node-mount-point__icon",
                "node-mount-point__text",
                "node-name",
                "node-note",
                "node-selected",
                "node-stop",
                "node-toggle",
                "node-treeline",
                "node-treeline--connect",
                "node-treeline--last",
                "node-treeline--line",
                "node-treelines",
                "nodes-container",
                "nodes-list",
                "tree-element",
                "tree-toolbar",
                "tree-toolbar__buttons",
                "tree-toolbar__drag-node",
                "tree-toolbar__menu",
                "tree-toolbar__menuitem",
                "tree-toolbar__search",
                "tree-toolbar__submenu",
                "tree-toolbar__submenu-items",
                "tree-toolbar__submenu-items--expanded",
                "tree-toolbar__submenu-toggle"
            ],
            "sourceFiles": [
                "EXT:backend/Resources/Public/Css/backend.css",
                "Build/Sources/Sass/component/_tree.scss",
                "EXT:styleguide/Resources/Private/Templates/Backend/Components/Trees.fluid.html"
            ],
            "markupSource": "installation",
            "contractVersion": "14.3.6-dev",
            "describesVersion": "14.3.6-dev",
            "since": 13,
            "until": null,
            "verifiedOn": "TYPO3 v13 and newer"
        }
    ],
    "withheld": [],
    "componentSource": "installation",
    "catalog": {
        "repository": "https://github.com/TYPO3/typo3",
        "branch": "main",
        "version": "15.0",
        "commit": "4c8b38b2dd07856c3e2666fbdfd77beead87ffe0",
        "verifiedAt": "2026-07-28",
        "installedVersion": "14.3.6-dev",
        "skew": null
    }
}
```

### components: hit

Called with:

```json
{
    "query": "badge"
}
```

Text:

````
## Badges (`badge`)
Matched in: name
Small inline status, label, or count indicator. Variants map to TYPO3 state tokens.

Markup:
```html
<span class="badge badge-default">default badge</span>
```
Variants: badge-primary, badge-secondary, badge-info, badge-success, badge-warning, badge-danger, badge-notice, badge-default
Modifiers: badge-pill, badge-space-start, badge-space-end, badge-stable, badge-experimental, badge-beta, badge-alpha, badge-deprecated
Sub-components: badge-list
Custom properties: --typo3-badge-bg, --typo3-badge-border-color, --typo3-badge-border-radius, --typo3-badge-color, --typo3-badge-font-size, --typo3-badge-link-focus-bg, --typo3-badge-link-focus-border-color, --typo3-badge-link-focus-color, --typo3-badge-link-hover-bg, --typo3-badge-link-hover-border-color, --typo3-badge-link-hover-color, --typo3-badge-padding-x, --typo3-badge-padding-y
Curated Sass source path: Build/Sources/Sass/component/_badges.scss
Styleguide demo: typo3/sysext/styleguide/Resources/Private/Templates/Backend/Components/Badges.fluid.html
Installed sources: EXT:backend/Resources/Public/Css/backend.css, Build/Sources/Sass/component/_badges.scss, EXT:styleguide/Resources/Private/Templates/Backend/Components/Badges.fluid.html
Markup source: installed styleguide template.
Verified on: TYPO3 v13 and newer.

Examples:
```html
<span class="badge badge-pill badge-default">pill shaped badge</span>
<span class="badge badge-pill badge-default">1</span>
```
```html
<div class="example-container">
    <f:for each="{variants}" as="variant">
        <div class="example-item">
            <span class="badge badge-{variant}">{variant}</span>
        </div>
    </f:for>
</div>
```
```html
<h1>h1. Example heading <span class="badge badge-primary">New</span> <span class="badge badge-pill badge-danger">1</span></h1>
<h2>h2. Example heading <span class="badge badge-primary">New</span> <span class="badge badge-pill badge-danger">2</span></h2>
<h3>h3. Example heading <span class="badge badge-primary">New</span> <span class="badge badge-pill badge-danger">3</span></h3>
<h4>h4. Example heading <span class="badge badge-primary">New</span> <span class="badge badge-pill badge-danger">4</span></h4>
<h5>h5. Example heading <span class="badge badge-primary">New</span> <span class="badge badge-pill badge-danger">5</span></h5>
<h6>h6. Example heading <span class="badge badge-primary">New</span> <span class="badge badge-pill badge-danger">6</span></h6>
```

## Component Definition of Done
Applies to every backend component. Verify each item before a component change is complete.
- [ ] All relevant variants, sizes, and modifiers are implemented and demoed.
- [ ] All applicable states are covered: hover, focus, active, disabled, loading, empty, error, selected, expanded, collapsed.
- [ ] Colors come only from semantic --typo3-* tokens (no raw hex/RGB/HSL, no --bs-* Bootstrap variables) and work in light and dark mode.
- [ ] Surfaces and shadows follow the elevation sequence; foreground/background token pairs are valid in both schemes.
- [ ] Variants tune the base component through custom properties; structure stays in the base component.
- [ ] Keyboard reachable and operable, with visible :focus-visible, logical focus order, and WCAG contrast in both schemes.
- [ ] Interactive icon-only controls have an accessible name; decorative icons are aria-hidden; ARIA and data-* reflect real state.
- [ ] RTL checked with logical properties; layout stays stable across states (no shift on hover or focus).
- [ ] New overlays use z-index tokens; new motion respects prefers-reduced-motion.
- [ ] Custom elements are styled via the host selector, ::part(), slots, or custom properties.
- [ ] t3js-* classes are used only as JavaScript hooks, separate from styling selectors.
- [ ] A styleguide demo exists or is updated, covering variants, states, light/dark, and RTL.
- [ ] build and lintScss were run; generated assets are in sync.

Answered from installed TYPO3 v14 package evidence; an indexed component absent there is withheld.

Component contract: installed TYPO3 14.3.6-dev packages. Names, summaries, keywords, and fallback markup come from the curated catalog; classes and custom properties come from EXT:backend/Resources/Public/Css/backend.css, and an installed styleguide example replaces the fallback markup where available.
````

Data:

```json
{
    "query": "badge",
    "targetVersion": 14,
    "matchCount": 1,
    "components": [
        {
            "name": "badge",
            "title": "Badges",
            "summary": "Small inline status, label, or count indicator. Variants map to TYPO3 state tokens.",
            "rootClass": "badge",
            "variants": [
                "badge-primary",
                "badge-secondary",
                "badge-info",
                "badge-success",
                "badge-warning",
                "badge-danger",
                "badge-notice",
                "badge-default"
            ],
            "modifiers": [
                "badge-pill",
                "badge-space-start",
                "badge-space-end",
                "badge-stable",
                "badge-experimental",
                "badge-beta",
                "badge-alpha",
                "badge-deprecated"
            ],
            "subComponents": [
                "badge-list"
            ],
            "customProperties": [
                "--typo3-badge-bg",
                "--typo3-badge-border-color",
                "--typo3-badge-border-radius",
                "--typo3-badge-color",
                "--typo3-badge-font-size",
                "--typo3-badge-link-focus-bg",
                "--typo3-badge-link-focus-border-color",
                "--typo3-badge-link-focus-color",
                "--typo3-badge-link-hover-bg",
                "--typo3-badge-link-hover-border-color",
                "--typo3-badge-link-hover-color",
                "--typo3-badge-padding-x",
                "--typo3-badge-padding-y"
            ],
            "markup": "<span class=\"badge badge-default\">default badge</span>",
            "examples": [
                "<span class=\"badge badge-pill badge-default\">pill shaped badge</span>\n<span class=\"badge badge-pill badge-default\">1</span>",
                "<div class=\"example-container\">\n    <f:for each=\"{variants}\" as=\"variant\">\n        <div class=\"example-item\">\n            <span class=\"badge badge-{variant}\">{variant}</span>\n        </div>\n    </f:for>\n</div>",
                "<h1>h1. Example heading <span class=\"badge badge-primary\">New</span> <span class=\"badge badge-pill badge-danger\">1</span></h1>\n<h2>h2. Example heading <span class=\"badge badge-primary\">New</span> <span class=\"badge badge-pill badge-danger\">2</span></h2>\n<h3>h3. Example heading <span class=\"badge badge-primary\">New</span> <span class=\"badge badge-pill badge-danger\">3</span></h3>\n<h4>h4. Example heading <span class=\"badge badge-primary\">New</span> <span class=\"badge badge-pill badge-danger\">4</span></h4>\n<h5>h5. Example heading <span class=\"badge badge-primary\">New</span> <span class=\"badge badge-pill badge-danger\">5</span></h5>\n<h6>h6. Example heading <span class=\"badge badge-primary\">New</span> <span class=\"badge badge-pill badge-danger\">6</span></h6>"
            ],
            "sassPath": "Build/Sources/Sass/component/_badges.scss",
            "sassPaths": [
                "Build/Sources/Sass/component/_badges.scss"
            ],
            "demoPath": "typo3/sysext/styleguide/Resources/Private/Templates/Backend/Components/Badges.fluid.html",
            "matchedIn": [
                "name"
            ],
            "classes": [
                "badge",
                "badge-alpha",
                "badge-beta",
                "badge-danger",
                "badge-default",
                "badge-deprecated",
                "badge-experimental",
                "badge-info",
                "badge-list",
                "badge-notice",
                "badge-pill",
                "badge-primary",
                "badge-secondary",
                "badge-space-end",
                "badge-space-start",
                "badge-stable",
                "badge-success",
                "badge-warning"
            ],
            "sourceFiles": [
                "EXT:backend/Resources/Public/Css/backend.css",
                "Build/Sources/Sass/component/_badges.scss",
                "EXT:styleguide/Resources/Private/Templates/Backend/Components/Badges.fluid.html"
            ],
            "markupSource": "installation",
            "contractVersion": "14.3.6-dev",
            "describesVersion": "14.3.6-dev",
            "since": 13,
            "until": null,
            "verifiedOn": "TYPO3 v13 and newer"
        }
    ],
    "withheld": [],
    "checklist": {
        "title": "Component Definition of Done",
        "intro": "Applies to every backend component. Verify each item before a component change is complete.",
        "items": [
            "All relevant variants, sizes, and modifiers are implemented and demoed.",
            "All applicable states are covered: hover, focus, active, disabled, loading, empty, error, selected, expanded, collapsed.",
            "Colors come only from semantic --typo3-* tokens (no raw hex/RGB/HSL, no --bs-* Bootstrap variables) and work in light and dark mode.",
            "Surfaces and shadows follow the elevation sequence; foreground/background token pairs are valid in both schemes.",
            "Variants tune the base component through custom properties; structure stays in the base component.",
            "Keyboard reachable and operable, with visible :focus-visible, logical focus order, and WCAG contrast in both schemes.",
            "Interactive icon-only controls have an accessible name; decorative icons are aria-hidden; ARIA and data-* reflect real state.",
            "RTL checked with logical properties; layout stays stable across states (no shift on hover or focus).",
            "New overlays use z-index tokens; new motion respects prefers-reduced-motion.",
            "Custom elements are styled via the host selector, ::part(), slots, or custom properties.",
            "t3js-* classes are used only as JavaScript hooks, separate from styling selectors.",
            "A styleguide demo exists or is updated, covering variants, states, light/dark, and RTL.",
            "build and lintScss were run; generated assets are in sync."
        ]
    },
    "componentSource": "installation",
    "catalog": {
        "repository": "https://github.com/TYPO3/typo3",
        "branch": "main",
        "version": "15.0",
        "commit": "4c8b38b2dd07856c3e2666fbdfd77beead87ffe0",
        "verifiedAt": "2026-07-28",
        "installedVersion": "14.3.6-dev",
        "skew": null
    }
}
```

### components: miss

Called with:

```json
{
    "query": "quantumflux"
}
```

Text:

```
No TYPO3 component matched "quantumflux". Try a component name (badge, card), a class (input-group), or a topic (search box). The installed packages were checked, but the searchable component index remains curated; inspect the installed backend CSS for an uncatalogued class.
Component contract: installed TYPO3 14.3.6-dev packages. Names, summaries, keywords, and fallback markup come from the curated catalog; classes and custom properties come from EXT:backend/Resources/Public/Css/backend.css, and an installed styleguide example replaces the fallback markup where available.
```

Data:

```json
{
    "query": "quantumflux",
    "targetVersion": 14,
    "matchCount": 0,
    "components": [],
    "withheld": [],
    "componentSource": "installation",
    "catalog": {
        "repository": "https://github.com/TYPO3/typo3",
        "branch": "main",
        "version": "15.0",
        "commit": "4c8b38b2dd07856c3e2666fbdfd77beead87ffe0",
        "verifiedAt": "2026-07-28",
        "installedVersion": "14.3.6-dev",
        "skew": null
    }
}
```

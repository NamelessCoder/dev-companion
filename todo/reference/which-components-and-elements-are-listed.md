# Which components and elements this server lists

The rule is the maintainer's, 2026-08-24: **what the styleguide lists is public
API, and what it does not list is not to be used or suggested.** Below is every
candidate on both sides of it, generated from `knowledge/catalog/` and
`.checkouts/14.3` so the names are exact. What comes out of the reading goes
into `components.json`, and this page is consumed by that commit.

The counts are what the derivation reports today. A class count of zero means
the entry names a custom element rather than a class, or names classes the
stylesheet does not carry.

## A. Entries the styleguide lists

Sixteen of the twenty-six map to an action by name. Nothing here is in doubt
unless a mapping is wrong.

| entry            | styleguide action | listed since | classes |
| ---------------- | ----------------- | ------------ | ------- |
| avatar           | avatar            | 13           | 6       |
| badge            | badges            | 13           | 17      |
| breadcrumb       | breadcrumbs       | 14           | 4       |
| button           | buttons           | 13           | 16      |
| card             | cards             | 13           | 16      |
| dropdown         | dropdown          | 13           | 11      |
| infobox          | infobox           | 13           | 0       |
| input            | input             | 13           | 4       |
| list-group       | listGroups        | 14           | 4       |
| modal            | modal             | 13           | 14      |
| nav              | navs              | 13           | 6       |
| pagination       | pagination        | 13           | 3       |
| panel            | panels            | 13           | 22      |
| status-indicator | statusIndicators  | 14           | 15      |
| table            | tables            | 13           | 10      |
| tree             | trees             | 13           | 11      |

## B. Entries no action matches

Ten. Five have a plausible action under another name, and five have none at all
— which by the rule makes them internal and takes them out.

| entry           | rootClass                  | classes | the reading to confirm                                 |
| --------------- | -------------------------- | ------- | ------------------------------------------------------ |
| alert           | alert                      | 12      | flashMessages?                                         |
| button-group    | btn-group                  | 4       | buttons?                                               |
| callout         | callout                    | 11      | infobox, which is a second entry on the same rootClass |
| dropzone        | dropzone                   | 10      | no action anywhere — drop as internal                  |
| form-check      | form-check                 | 14      | checkboxes and radio?                                  |
| module          | module                     | 11      | no action anywhere — drop as internal                  |
| note            | note                       | 19      | no action anywhere — drop as internal                  |
| popover         | popover                    | 0       | no action anywhere — drop as internal                  |
| progress-bar    | typo3-backend-progress-bar | 0       | progressIndicators?                                    |
| recordsearchbox | recordsearchbox-container  | 2       | no action anywhere — drop as internal                  |

## C. Listed components with no entry

Fifteen. Public API by the rule, and this server answers nothing about any of
them.

- `checkboxes` — listed since 13
- `comboboxes` — listed since 14
- `contentNavigation` — listed since 14
- `datetime` — listed since 14
- `developerTools` — listed since 13
- `exception` — listed since 14
- `flashMessages` — listed since 13
- `form` — listed since 13
- `notifications` — listed since 13
- `progressIndicators` — listed since 13
- `progressTrackers` — listed since 13
- `radio` — listed since 14
- `select` — listed since 13
- `tab` — listed since 13
- `textarea` — listed since 13

## D. Custom elements the styleguide demonstrates

Thirteen of a hundred and thirty-seven. A tag is written literally in a
template, so unlike a class name this is readable without rendering anything.

- `typo3-backend-content-navigation` — since 14
- `typo3-backend-content-navigation-toggle` — since 14
- `typo3-backend-datetime` — since 14
- `typo3-backend-icon` — since 12
- `typo3-backend-navigation-component-filestorage-tree` — since 12
- `typo3-backend-navigation-component-pagetree` — since 12
- `typo3-backend-navigation-component-pagetree-tree` — since 12
- `typo3-backend-progress-bar` — since 13
- `typo3-backend-progress-tracker` — since 13
- `typo3-backend-spinner` — since 12
- `typo3-backend-status-indicator` — since 14
- `typo3-backend-tree-node-toggle` — since 12
- `typo3-breadcrumb` — since 14

## E. Custom elements it does not demonstrate

A hundred and twenty-four, internal by the rule. This is where a wrong call
would hide, so they are listed rather than counted.

### backend (84)

- `typo3-backend-alert` — since 13
- `typo3-backend-bookmark-button` — since 14
- `typo3-backend-bookmark-manager-button` — since 14
- `typo3-backend-bookmark-manager-content` — since 14
- `typo3-backend-bookmark-menu` — since 14
- `typo3-backend-clipboard-panel`
- `typo3-backend-color-picker` — since 13
- `typo3-backend-color-scheme-switch` — since 13
- `typo3-backend-column-selector-button`
- `typo3-backend-combobox` — since 14
- `typo3-backend-combobox-choice` — since 14
- `typo3-backend-component-filestorage-browser`
- `typo3-backend-component-filestorage-browser-tree`
- `typo3-backend-component-page-browser`
- `typo3-backend-component-page-browser-tree`
- `typo3-backend-component-page-position-select` — since 14
- `typo3-backend-component-page-position-select-tree` — since 14
- `typo3-backend-context-menu` — since 13
- `typo3-backend-contextual-record-edit-trigger` — since 14
- `typo3-backend-dispatch-modal-button` — since 13
- `typo3-backend-drag-tooltip` — since 13
- `typo3-backend-draggable-resizable`
- `typo3-backend-editable-page-title`
- `typo3-backend-editable-setting` — since 13
- `typo3-backend-form-selecttree`
- `typo3-backend-form-selecttree-toolbar`
- `typo3-backend-formengine-char-counter` — since 13
- `typo3-backend-formengine-online-media-form` — since 13
- `typo3-backend-formengine-suggest-result-container`
- `typo3-backend-formengine-suggest-result-item`
- `typo3-backend-formengine-suggest-result-list`
- `typo3-backend-grid-editor` — since 13
- `typo3-backend-link-browser-download` — since 14
- `typo3-backend-live-search`
- `typo3-backend-live-search-hint` — since 13
- `typo3-backend-live-search-option-item`
- `typo3-backend-live-search-result-action-list`
- `typo3-backend-live-search-result-container`
- `typo3-backend-live-search-result-item`
- `typo3-backend-live-search-result-item-action`
- `typo3-backend-live-search-result-item-action-container`
- `typo3-backend-live-search-result-item-container`
- `typo3-backend-live-search-result-item-default`
- `typo3-backend-live-search-result-item-detail-container`
- `typo3-backend-live-search-result-item-page-provider`
- `typo3-backend-live-search-result-list`
- `typo3-backend-live-search-result-page`
- `typo3-backend-live-search-result-pagination`
- `typo3-backend-localization-button` — since 14
- `typo3-backend-localization-wizard` — since 14
- `typo3-backend-modal`
- `typo3-backend-module-router`
- `typo3-backend-navigation-component-filestoragetree`
- `typo3-backend-navigation-component-pagetree-toolbar`
- `typo3-backend-navigation-switcher` — since 12, until 13
- `typo3-backend-new-content-element-wizard` — since 12, until 12
- `typo3-backend-new-content-element-wizard-button`
- `typo3-backend-new-page-wizard-button` — since 14
- `typo3-backend-new-record-wizard` — since 13
- `typo3-backend-page-layout-sticky-language-header` — since 14
- `typo3-backend-page-layout-toggle-hidden` — since 14
- `typo3-backend-page-wizard` — since 14
- `typo3-backend-pagination` — since 13
- `typo3-backend-security-csp-reports`
- `typo3-backend-security-sudo-mode`
- `typo3-backend-security-sudo-mode-form`
- `typo3-backend-settings-editor` — since 13
- `typo3-backend-sidebar-toggle` — since 14
- `typo3-backend-switch-user`
- `typo3-backend-tab-scroller` — since 14
- `typo3-backend-thumbnail`
- `typo3-backend-tree-toolbar`
- `typo3-backend-wizard` — since 14
- `typo3-copy-to-clipboard`
- `typo3-formengine-table-wizard`
- `typo3-formengine-updater` — since 12, until 12
- `typo3-iframe-module`
- `typo3-login-refresh-progress-bar`
- `typo3-notification-clear-all` — since 13
- `typo3-notification-message`
- `typo3-qrcode` — since 14
- `typo3-qrcode-modal-button` — since 14
- `typo3-recordlist-record-download-button`
- `typo3-t3editor-codemirror`

### core (1)

- `typo3-mfa-totp-url-info-button`

### dashboard (5)

- `typo3-dashboard` — since 13
- `typo3-dashboard-bookmarks-widget` — since 14
- `typo3-dashboard-widget` — since 13
- `typo3-dashboard-widget-refresh` — since 12, until 12
- `typo3-dashboard-wizard` — since 14

### extensionmanager (3)

- `typo3-extension-toggle-button` — since 14
- `typo3-extensionmanager-dependency-check` — since 14
- `typo3-extensionmanager-distribution-image`

### form (9)

- `typo3-backend-form-wizard` — since 14
- `typo3-backend-navigation-component-formeditor-tree` — since 14
- `typo3-backend-navigation-component-formeditortree` — since 14
- `typo3-form-date-editor` — since 14
- `typo3-form-element-selector` — since 14
- `typo3-form-form-element-stage-item` — since 14
- `typo3-form-form-element-stage-item-toolbar` — since 14
- `typo3-form-page-stage-item` — since 14
- `typo3-form-property-grid-editor` — since 13

### install (6)

- `typo3-install-extension-matrix` — since 13
- `typo3-install-flashmessage` — since 13
- `typo3-install-infobox` — since 13
- `typo3-install-language-matrix` — since 13
- `typo3-install-offset-group` — since 13
- `typo3-install-wrap-group` — since 13

### opendocs (2)

- `typo3-open-document-menu` — since 14
- `typo3-opendocs-recent-documents-widget` — since 14

### rte-ckeditor (1)

- `typo3-rte-ckeditor-ckeditor5`

### scheduler (3)

- `typo3-scheduler-editable-group-name` — since 12, until 13
- `typo3-scheduler-new-task-wizard-button` — since 14
- `typo3-scheduler-setup-check-button` — since 14

### styleguide (1)

- `typo3-styleguide-theme-switcher` — since 13

### sys-note (1)

- `typo3-sysnote-delete-button` — since 13

### workspaces (8)

- `typo3-backend-workspace-selector` — since 14
- `typo3-backend-workspace-top-indicator` — since 14
- `typo3-workspaces-comment-view` — since 13
- `typo3-workspaces-diff-view` — since 13
- `typo3-workspaces-history-view` — since 13
- `typo3-workspaces-record-information` — since 13
- `typo3-workspaces-record-table` — since 13
- `typo3-workspaces-send-to-stage-form` — since 13


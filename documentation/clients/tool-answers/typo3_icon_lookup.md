# What `typo3_icon_lookup` answered

Recorded on 2026-08-02 by `bin/cli tools:record`. Of two working directories,
because what this server answers depends on which one a client is standing in,
and neither fills the whole surface. Answered against core-checkout, TYPO3
14.3.6-dev, the 14.3 core checkout below .checkouts/, whose console could not
be reached: <installation> has no TYPO3 console — none of bin/typo3,
vendor/bin/typo3 exists. Answered against composer-project, TYPO3 14.3.5, the
E-SITE this repository makes below .environments/, whose console answers. The
tools that declare `answeredBy` carry an answer from each, under a heading
naming which; every other answer is from the first alone, because nothing in it
would differ. Nothing checks this page; [tools.md](../tools.md) is where the
current shape of an answer is, and [readme.md](readme.md) is what the recording
as a whole is of.

## icons: hit

Called with:

```json
{
    "query": "actions-open"
}
```

### From the 14.3 core checkout below .checkouts/, whose console could not be reached

Text:

```
These identifiers address the backend icon registry. They are resolved by IconFactory and rendered by the backend <core:icon> ViewHelper; frontend rendering reaches neither, and needs its own inline SVG or asset file. This list is read from the package files rather than from the booted installation — <installation> has no TYPO3 console — none of bin/typo3, vendor/bin/typo3 exists. Identifiers a package builds in a loop or registers from ext_localconf.php, and the ones TYPO3 derives from TCA, are not in it.

"actions-open" is registered in <installation>; 22 related identifier(s) follow as suggestions:
- actions-open
  matched: name part "open", exact identifier
- actions-document-history-open
  alias of actions-history
  matched: name part "open"
- actions-document-open
  alias of actions-document-edit
  matched: name part "open"
- actions-document-open-read-only
  alias of actions-document-readonly
  matched: name part "open"
- actions-envelope-open
  matched: name part "open"
- actions-envelope-open-text
  matched: name part "open"
- actions-page-open
  alias of actions-file-edit
  matched: name part "open"
- actions-system-help-open
  alias of actions-question
  matched: name part "open"
- actions-system-list-open
  alias of actions-list-alternative
  matched: name part "open"
- actions-system-pagemodule-open
  alias of actions-file-search
  matched: name part "open"
- actions-system-tree-search-open
  alias of actions-filter
  matched: name part "open"
- actions-system-typoscript-documentation-open
  alias of actions-notebook-typoscript
  matched: name part "open"
- actions-version-page-open
  alias of actions-file-edit
  matched: name part "open"
- actions-window-open
  matched: name part "open"
- mimetypes-open-document-database
  matched: name part "open"
- mimetypes-open-document-drawing
  matched: name part "open"
- mimetypes-open-document-formula
  matched: name part "open"
- mimetypes-open-document-presentation
  matched: name part "open"
- mimetypes-open-document-spreadsheet
  matched: name part "open"
- mimetypes-open-document-text
  matched: name part "open"
- actions-file-openoffice
  matched: substring "open"
- apps-filetree-folder-opened
  matched: substring "open"
- apps-toolbar-menu-opendocs
  alias of actions-file
  matched: substring "open"
```

Data:

```json
{
    "query": "actions-open",
    "matchCount": 1,
    "suggestionCount": 22,
    "exactMatch": true,
    "icons": [
        {
            "identifier": "actions-open",
            "category": "actions",
            "aliasOf": null,
            "source": "t3icons",
            "matched": 1,
            "score": 1004,
            "why": [
                "name part \"open\"",
                "exact identifier"
            ]
        },
        {
            "identifier": "actions-document-history-open",
            "category": "actions",
            "aliasOf": "actions-history",
            "source": "t3icons",
            "matched": 1,
            "score": 4,
            "why": [
                "name part \"open\""
            ]
        },
        {
            "identifier": "actions-document-open",
            "category": "actions",
            "aliasOf": "actions-document-edit",
            "source": "t3icons",
            "matched": 1,
            "score": 4,
            "why": [
                "name part \"open\""
            ]
        },
        {
            "identifier": "actions-document-open-read-only",
            "category": "actions",
            "aliasOf": "actions-document-readonly",
            "source": "t3icons",
            "matched": 1,
            "score": 4,
            "why": [
                "name part \"open\""
            ]
        },
        {
            "identifier": "actions-envelope-open",
            "category": "actions",
            "aliasOf": null,
            "source": "t3icons",
            "matched": 1,
            "score": 4,
            "why": [
                "name part \"open\""
            ]
        },
        {
            "identifier": "actions-envelope-open-text",
            "category": "actions",
            "aliasOf": null,
            "source": "t3icons",
            "matched": 1,
            "score": 4,
            "why": [
                "name part \"open\""
            ]
        },
        {
            "identifier": "actions-page-open",
            "category": "actions",
            "aliasOf": "actions-file-edit",
            "source": "t3icons",
            "matched": 1,
            "score": 4,
            "why": [
                "name part \"open\""
            ]
        },
        {
            "identifier": "actions-system-help-open",
            "category": "actions",
            "aliasOf": "actions-question",
            "source": "t3icons",
            "matched": 1,
            "score": 4,
            "why": [
                "name part \"open\""
            ]
        },
        {
            "identifier": "actions-system-list-open",
            "category": "actions",
            "aliasOf": "actions-list-alternative",
            "source": "t3icons",
            "matched": 1,
            "score": 4,
            "why": [
                "name part \"open\""
            ]
        },
        {
            "identifier": "actions-system-pagemodule-open",
            "category": "actions",
            "aliasOf": "actions-file-search",
            "source": "t3icons",
            "matched": 1,
            "score": 4,
            "why": [
                "name part \"open\""
            ]
        },
        {
            "identifier": "actions-system-tree-search-open",
            "category": "actions",
            "aliasOf": "actions-filter",
            "source": "t3icons",
            "matched": 1,
            "score": 4,
            "why": [
                "name part \"open\""
            ]
        },
        {
            "identifier": "actions-system-typoscript-documentation-open",
            "category": "actions",
            "aliasOf": "actions-notebook-typoscript",
            "source": "t3icons",
            "matched": 1,
            "score": 4,
            "why": [
                "name part \"open\""
            ]
        },
        {
            "identifier": "actions-version-page-open",
            "category": "actions",
            "aliasOf": "actions-file-edit",
            "source": "t3icons",
            "matched": 1,
            "score": 4,
            "why": [
                "name part \"open\""
            ]
        },
        {
            "identifier": "actions-window-open",
            "category": "actions",
            "aliasOf": null,
            "source": "t3icons",
            "matched": 1,
            "score": 4,
            "why": [
                "name part \"open\""
            ]
        },
        {
            "identifier": "mimetypes-open-document-database",
            "category": "mimetypes",
            "aliasOf": null,
            "source": "t3icons",
            "matched": 1,
            "score": 4,
            "why": [
                "name part \"open\""
            ]
        },
        {
            "identifier": "mimetypes-open-document-drawing",
            "category": "mimetypes",
            "aliasOf": null,
            "source": "t3icons",
            "matched": 1,
            "score": 4,
            "why": [
                "name part \"open\""
            ]
        },
        {
            "identifier": "mimetypes-open-document-formula",
            "category": "mimetypes",
            "aliasOf": null,
            "source": "t3icons",
            "matched": 1,
            "score": 4,
            "why": [
                "name part \"open\""
            ]
        },
        {
            "identifier": "mimetypes-open-document-presentation",
            "category": "mimetypes",
            "aliasOf": null,
            "source": "t3icons",
            "matched": 1,
            "score": 4,
            "why": [
                "name part \"open\""
            ]
        },
        {
            "identifier": "mimetypes-open-document-spreadsheet",
            "category": "mimetypes",
            "aliasOf": null,
            "source": "t3icons",
            "matched": 1,
            "score": 4,
            "why": [
                "name part \"open\""
            ]
        },
        {
            "identifier": "mimetypes-open-document-text",
            "category": "mimetypes",
            "aliasOf": null,
            "source": "t3icons",
            "matched": 1,
            "score": 4,
            "why": [
                "name part \"open\""
            ]
        },
        {
            "identifier": "actions-file-openoffice",
            "category": "actions",
            "aliasOf": null,
            "source": "t3icons",
            "matched": 1,
            "score": 2,
            "why": [
                "substring \"open\""
            ]
        },
        {
            "identifier": "apps-filetree-folder-opened",
            "category": "apps",
            "aliasOf": null,
            "source": "t3icons",
            "matched": 1,
            "score": 2,
            "why": [
                "substring \"open\""
            ]
        },
        {
            "identifier": "apps-toolbar-menu-opendocs",
            "category": "apps",
            "aliasOf": "actions-file",
            "source": "t3icons",
            "matched": 1,
            "score": 2,
            "why": [
                "substring \"open\""
            ]
        }
    ],
    "scope": "These identifiers address the backend icon registry. They are resolved by IconFactory and rendered by the backend <core:icon> ViewHelper; frontend rendering reaches neither, and needs its own inline SVG or asset file. This list is read from the package files rather than from the booted installation — <installation> has no TYPO3 console — none of bin/typo3, vendor/bin/typo3 exists. Identifiers a package builds in a loop or registers from ext_localconf.php, and the ones TYPO3 derives from TCA, are not in it.",
    "answeredBy": "packages"
}
```

### From the E-SITE this repository makes below .environments/, whose console answers

Text:

```
These identifiers address the backend icon registry. They are resolved by IconFactory and rendered by the backend <core:icon> ViewHelper; frontend rendering reaches neither, and needs its own inline SVG or asset file.

"actions-open" is registered in <installation>; 22 related identifier(s) follow as suggestions:
- actions-open
  matched: name part "open", exact identifier
- actions-document-history-open
  alias of actions-history
  matched: name part "open"
- actions-document-open
  alias of actions-document-edit
  matched: name part "open"
- actions-document-open-read-only
  alias of actions-document-readonly
  matched: name part "open"
- actions-envelope-open
  matched: name part "open"
- actions-envelope-open-text
  matched: name part "open"
- actions-page-open
  alias of actions-file-edit
  matched: name part "open"
- actions-system-help-open
  alias of actions-question
  matched: name part "open"
- actions-system-list-open
  alias of actions-list-alternative
  matched: name part "open"
- actions-system-pagemodule-open
  alias of actions-file-search
  matched: name part "open"
- actions-system-tree-search-open
  alias of actions-filter
  matched: name part "open"
- actions-system-typoscript-documentation-open
  alias of actions-notebook-typoscript
  matched: name part "open"
- actions-version-page-open
  alias of actions-file-edit
  matched: name part "open"
- actions-window-open
  matched: name part "open"
- mimetypes-open-document-database
  matched: name part "open"
- mimetypes-open-document-drawing
  matched: name part "open"
- mimetypes-open-document-formula
  matched: name part "open"
- mimetypes-open-document-presentation
  matched: name part "open"
- mimetypes-open-document-spreadsheet
  matched: name part "open"
- mimetypes-open-document-text
  matched: name part "open"
- actions-file-openoffice
  matched: substring "open"
- apps-filetree-folder-opened
  matched: substring "open"
- apps-toolbar-menu-opendocs
  alias of actions-file
  matched: substring "open"
```

Data:

```json
{
    "query": "actions-open",
    "matchCount": 1,
    "suggestionCount": 22,
    "exactMatch": true,
    "icons": [
        {
            "identifier": "actions-open",
            "category": "actions",
            "aliasOf": null,
            "source": "t3icons",
            "matched": 1,
            "score": 1004,
            "why": [
                "name part \"open\"",
                "exact identifier"
            ]
        },
        {
            "identifier": "actions-document-history-open",
            "category": "actions",
            "aliasOf": "actions-history",
            "source": "t3icons",
            "matched": 1,
            "score": 4,
            "why": [
                "name part \"open\""
            ]
        },
        {
            "identifier": "actions-document-open",
            "category": "actions",
            "aliasOf": "actions-document-edit",
            "source": "t3icons",
            "matched": 1,
            "score": 4,
            "why": [
                "name part \"open\""
            ]
        },
        {
            "identifier": "actions-document-open-read-only",
            "category": "actions",
            "aliasOf": "actions-document-readonly",
            "source": "t3icons",
            "matched": 1,
            "score": 4,
            "why": [
                "name part \"open\""
            ]
        },
        {
            "identifier": "actions-envelope-open",
            "category": "actions",
            "aliasOf": null,
            "source": "t3icons",
            "matched": 1,
            "score": 4,
            "why": [
                "name part \"open\""
            ]
        },
        {
            "identifier": "actions-envelope-open-text",
            "category": "actions",
            "aliasOf": null,
            "source": "t3icons",
            "matched": 1,
            "score": 4,
            "why": [
                "name part \"open\""
            ]
        },
        {
            "identifier": "actions-page-open",
            "category": "actions",
            "aliasOf": "actions-file-edit",
            "source": "t3icons",
            "matched": 1,
            "score": 4,
            "why": [
                "name part \"open\""
            ]
        },
        {
            "identifier": "actions-system-help-open",
            "category": "actions",
            "aliasOf": "actions-question",
            "source": "t3icons",
            "matched": 1,
            "score": 4,
            "why": [
                "name part \"open\""
            ]
        },
        {
            "identifier": "actions-system-list-open",
            "category": "actions",
            "aliasOf": "actions-list-alternative",
            "source": "t3icons",
            "matched": 1,
            "score": 4,
            "why": [
                "name part \"open\""
            ]
        },
        {
            "identifier": "actions-system-pagemodule-open",
            "category": "actions",
            "aliasOf": "actions-file-search",
            "source": "t3icons",
            "matched": 1,
            "score": 4,
            "why": [
                "name part \"open\""
            ]
        },
        {
            "identifier": "actions-system-tree-search-open",
            "category": "actions",
            "aliasOf": "actions-filter",
            "source": "t3icons",
            "matched": 1,
            "score": 4,
            "why": [
                "name part \"open\""
            ]
        },
        {
            "identifier": "actions-system-typoscript-documentation-open",
            "category": "actions",
            "aliasOf": "actions-notebook-typoscript",
            "source": "t3icons",
            "matched": 1,
            "score": 4,
            "why": [
                "name part \"open\""
            ]
        },
        {
            "identifier": "actions-version-page-open",
            "category": "actions",
            "aliasOf": "actions-file-edit",
            "source": "t3icons",
            "matched": 1,
            "score": 4,
            "why": [
                "name part \"open\""
            ]
        },
        {
            "identifier": "actions-window-open",
            "category": "actions",
            "aliasOf": null,
            "source": "t3icons",
            "matched": 1,
            "score": 4,
            "why": [
                "name part \"open\""
            ]
        },
        {
            "identifier": "mimetypes-open-document-database",
            "category": "mimetypes",
            "aliasOf": null,
            "source": "t3icons",
            "matched": 1,
            "score": 4,
            "why": [
                "name part \"open\""
            ]
        },
        {
            "identifier": "mimetypes-open-document-drawing",
            "category": "mimetypes",
            "aliasOf": null,
            "source": "t3icons",
            "matched": 1,
            "score": 4,
            "why": [
                "name part \"open\""
            ]
        },
        {
            "identifier": "mimetypes-open-document-formula",
            "category": "mimetypes",
            "aliasOf": null,
            "source": "t3icons",
            "matched": 1,
            "score": 4,
            "why": [
                "name part \"open\""
            ]
        },
        {
            "identifier": "mimetypes-open-document-presentation",
            "category": "mimetypes",
            "aliasOf": null,
            "source": "t3icons",
            "matched": 1,
            "score": 4,
            "why": [
                "name part \"open\""
            ]
        },
        {
            "identifier": "mimetypes-open-document-spreadsheet",
            "category": "mimetypes",
            "aliasOf": null,
            "source": "t3icons",
            "matched": 1,
            "score": 4,
            "why": [
                "name part \"open\""
            ]
        },
        {
            "identifier": "mimetypes-open-document-text",
            "category": "mimetypes",
            "aliasOf": null,
            "source": "t3icons",
            "matched": 1,
            "score": 4,
            "why": [
                "name part \"open\""
            ]
        },
        {
            "identifier": "actions-file-openoffice",
            "category": "actions",
            "aliasOf": null,
            "source": "t3icons",
            "matched": 1,
            "score": 2,
            "why": [
                "substring \"open\""
            ]
        },
        {
            "identifier": "apps-filetree-folder-opened",
            "category": "apps",
            "aliasOf": null,
            "source": "t3icons",
            "matched": 1,
            "score": 2,
            "why": [
                "substring \"open\""
            ]
        },
        {
            "identifier": "apps-toolbar-menu-opendocs",
            "category": "apps",
            "aliasOf": "actions-file",
            "source": "t3icons",
            "matched": 1,
            "score": 2,
            "why": [
                "substring \"open\""
            ]
        }
    ],
    "scope": "These identifiers address the backend icon registry. They are resolved by IconFactory and rendered by the backend <core:icon> ViewHelper; frontend rendering reaches neither, and needs its own inline SVG or asset file.",
    "answeredBy": "installation"
}
```

## icons: everything

Called with:

```json
{}
```

### From the 14.3 core checkout below .checkouts/, whose console could not be reached

Text:

```
These identifiers address the backend icon registry. They are resolved by IconFactory and rendered by the backend <core:icon> ViewHelper; frontend rendering reaches neither, and needs its own inline SVG or asset file. This list is read from the package files rather than from the booted installation — <installation> has no TYPO3 console — none of bin/typo3, vendor/bin/typo3 exists. Identifiers a package builds in a loop or registers from ext_localconf.php, and the ones TYPO3 derives from TCA, are not in it.

Icon categories in this installation: actions, apps, avatar, content, default, empty, files, flags, form, information, install, mimetypes, miscellaneous, module, modulegroup, overlay, provider, share, spinner, status, sysnote, tcarecords, theme.

Concept words that map to a shape: warning, caution, error, danger, info, notice, help, success, confirm, add, new, create, edit, delete, remove, save, search, filter, settings, configuration, user, permission, lock, hidden, visibility, preview, view, upload, download, refresh, reload, sort, close, cancel, copy, duplicate, move, link, translation, localization, language, folder, page, record, history, undo, import, export, message, notification, mail, calendar, time, list, menu, workspace, cache, bookmark, extension.
```

Data:

```json
{
    "query": "",
    "matchCount": 0,
    "suggestionCount": 0,
    "exactMatch": false,
    "icons": [],
    "categories": [
        "actions",
        "apps",
        "avatar",
        "content",
        "default",
        "empty",
        "files",
        "flags",
        "form",
        "information",
        "install",
        "mimetypes",
        "miscellaneous",
        "module",
        "modulegroup",
        "overlay",
        "provider",
        "share",
        "spinner",
        "status",
        "sysnote",
        "tcarecords",
        "theme"
    ],
    "concepts": [
        "warning",
        "caution",
        "error",
        "danger",
        "info",
        "notice",
        "help",
        "success",
        "confirm",
        "add",
        "new",
        "create",
        "edit",
        "delete",
        "remove",
        "save",
        "search",
        "filter",
        "settings",
        "configuration",
        "user",
        "permission",
        "lock",
        "hidden",
        "visibility",
        "preview",
        "view",
        "upload",
        "download",
        "refresh",
        "reload",
        "sort",
        "close",
        "cancel",
        "copy",
        "duplicate",
        "move",
        "link",
        "translation",
        "localization",
        "language",
        "folder",
        "page",
        "record",
        "history",
        "undo",
        "import",
        "export",
        "message",
        "notification",
        "mail",
        "calendar",
        "time",
        "list",
        "menu",
        "workspace",
        "cache",
        "bookmark",
        "extension"
    ],
    "scope": "These identifiers address the backend icon registry. They are resolved by IconFactory and rendered by the backend <core:icon> ViewHelper; frontend rendering reaches neither, and needs its own inline SVG or asset file. This list is read from the package files rather than from the booted installation — <installation> has no TYPO3 console — none of bin/typo3, vendor/bin/typo3 exists. Identifiers a package builds in a loop or registers from ext_localconf.php, and the ones TYPO3 derives from TCA, are not in it.",
    "answeredBy": "packages"
}
```

### From the E-SITE this repository makes below .environments/, whose console answers

Text:

```
These identifiers address the backend icon registry. They are resolved by IconFactory and rendered by the backend <core:icon> ViewHelper; frontend rendering reaches neither, and needs its own inline SVG or asset file.

Icon categories in this installation: actions, apps, avatar, content, default, empty, files, flags, form, information, install, mimetypes, miscellaneous, module, modulegroup, overlay, share, spinner, status, sysnote.

Concept words that map to a shape: warning, caution, error, danger, info, notice, help, success, confirm, add, new, create, edit, delete, remove, save, search, filter, settings, configuration, user, permission, lock, hidden, visibility, preview, view, upload, download, refresh, reload, sort, close, cancel, copy, duplicate, move, link, translation, localization, language, folder, page, record, history, undo, import, export, message, notification, mail, calendar, time, list, menu, workspace, cache, bookmark, extension.
```

Data:

```json
{
    "query": "",
    "matchCount": 0,
    "suggestionCount": 0,
    "exactMatch": false,
    "icons": [],
    "categories": [
        "actions",
        "apps",
        "avatar",
        "content",
        "default",
        "empty",
        "files",
        "flags",
        "form",
        "information",
        "install",
        "mimetypes",
        "miscellaneous",
        "module",
        "modulegroup",
        "overlay",
        "share",
        "spinner",
        "status",
        "sysnote"
    ],
    "concepts": [
        "warning",
        "caution",
        "error",
        "danger",
        "info",
        "notice",
        "help",
        "success",
        "confirm",
        "add",
        "new",
        "create",
        "edit",
        "delete",
        "remove",
        "save",
        "search",
        "filter",
        "settings",
        "configuration",
        "user",
        "permission",
        "lock",
        "hidden",
        "visibility",
        "preview",
        "view",
        "upload",
        "download",
        "refresh",
        "reload",
        "sort",
        "close",
        "cancel",
        "copy",
        "duplicate",
        "move",
        "link",
        "translation",
        "localization",
        "language",
        "folder",
        "page",
        "record",
        "history",
        "undo",
        "import",
        "export",
        "message",
        "notification",
        "mail",
        "calendar",
        "time",
        "list",
        "menu",
        "workspace",
        "cache",
        "bookmark",
        "extension"
    ],
    "scope": "These identifiers address the backend icon registry. They are resolved by IconFactory and rendered by the backend <core:icon> ViewHelper; frontend rendering reaches neither, and needs its own inline SVG or asset file.",
    "answeredBy": "installation"
}
```

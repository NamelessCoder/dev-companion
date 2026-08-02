# What `typo3_extension_scope` answered

Recorded on 2026-08-02 by `bin/cli tools:record`. Answered against
core-checkout, TYPO3 14.3.6-dev, the 14.3 core checkout below .checkouts/,
whose console could not be reached: <installation> has no TYPO3 console —
none of bin/typo3, vendor/bin/typo3 exists. Nothing checks this page;
[tools.md](../tools.md) is where the current shape of an answer is, and
[readme.md](readme.md) is what the recording as a whole is of.

## extension

Called with:

```json
{
    "extension": "backend"
}
```

Text:

```
backend (system) — <installation>/typo3/sysext/backend
TYPO3 CMS Backend

TCA tables it extends: be_users

Backend modules: web_layout, records, content_status, site_configuration, link_management, about, pagetsconfig, pagetsconfig_pages, pagetsconfig_active, pagetsconfig_includes, content_security_policy, user_setup

Backend routes: login, main, state-tracker, logout, password_forget, password_forget_initiate_reset, password_reset_validate, password_reset_finish, sudo_mode_module, sudo_mode_apply, sudo_mode_error, login_frameset, login_request_token, auth_mfa, setup_mfa, mfa, wizard_add, wizard_list, wizard_edit, wizard_element_browser, wizard_link, online_media, record_download, record_history, db_new, db_new_pages, pages_sort, pages_new, new_content_element_wizard, move_page, move_element, show_item, dummy, tce_db, tce_file, record_edit, record_edit_contextual, image_processing, clipboard_process, resource_request_thumbnail, language_domain, resource_rename, resource_gather, resource_replace, link_resource, file_process, file_exists, file_reference_details, file_reference_create, file_reference_synchronizelocalize, file_reference_expandcollapse, record_inline_details, record_inline_create, record_inline_synchronizelocalize, record_inline_expandcollapse, site_configuration_inline_create, record_slug_suggest, site_configuration_inline_details, record_flex_container_add, record_suggest, record_tree_data, page_tree_data, page_tree_rootline, page_tree_filter, page_tree_configuration, page_tree_browser_configuration, page_tree_set_temporary_mount_point, filestorage_tree_data, filestorage_tree_rootline, filestorage_tree_filter, bookmark_list, bookmark_create, bookmark_update, bookmark_delete, bookmark_reorder, bookmark_delete_multiple, bookmark_move, bookmark_group_create, bookmark_group_update, bookmark_group_delete, bookmark_group_reorder, clearcache_group_pages, clearcache_group_all, clearcache_page, systeminformation_render, modulemenu, topbar, login, logout, login_preflight, login_refresh, login_timedout, switch_user, switch_user_exit, mfa, contextmenu, contextmenu_clipboard, record_process, usersettings_process, wizard_image_manipulation, livesearch, livesearch_form, online_media_create, icons, link_browser_encodetypolink, wizard_localization_get_record, wizard_localization_get_targets, wizard_localization_get_sources, wizard_localization_get_modes, wizard_localization_get_handlers, wizard_localization_get_content, wizard_localization_localize, show_columns, show_columns_selector, record_download_settings, record_toggle_visibility, password_generate, security_csp_control, sudo_mode_control, codeeditor_tsref, codeeditor_codecompletion_loadtemplates, color_scheme_update, qrcode_generator, qrcode_download, wizard_page_get_doktypes, wizard_page_get_page_detail, wizard_page_get_processed_value, wizard_config, wizard_submit

Middlewares: typo3/cms-core/normalized-params-attribute, typo3/cms-backend/locked-backend, typo3/cms-backend/https-redirector, typo3/cms-backend/csp-report, typo3/cms-backend/backend-routing, typo3/cms-core/request-token-middleware, typo3/cms-backend/authentication, typo3/cms-backend/backend-module-validator, typo3/cms-backend/sudo-mode-interceptor, typo3/cms-backend/site-resolver, typo3/cms-backend/page-context, typo3/cms-backend/csp-headers, typo3/cms-backend/js-label-importmap-resolver, typo3/cms-backend/response-headers, typo3/cms-core/response-propagation

Fluid roots: Resources/Private/Templates/, Resources/Private/Partials/, Resources/Private/Layouts/

Registration files: ext_localconf.php, ext_tables.sql, Configuration/page.tsconfig, Configuration/user.tsconfig, Configuration/RequestMiddlewares.php, Configuration/Services.yaml, Configuration/JavaScriptModules.php

Classes: Command (8), Controller (90), Domain (5), Event (1), EventListener (4), Form (201), Hooks (2), Middleware (12), Service (2), Upgrades (3), ViewHelpers (15)
Each count is every PHP file below that directory, its own subdirectories included.

Requires: ext-intl *, ext-libxml *, psr/event-dispatcher ^1.0, typo3/cms-core 14.3.*@dev

Ships: manual none, readme README.rst, tests Functional+Unit
- Resources/Private/Language/Modules/about.xlf — source-language en, no translations beside it
- Resources/Private/Language/Modules/content-security-policy.xlf — source-language en, no translations beside it
- Resources/Private/Language/Modules/layout.xlf — source-language en, no translations beside it
- Resources/Private/Language/Modules/link_management.xlf — source-language en, no translations beside it
- Resources/Private/Language/Modules/list.xlf — source-language en, no translations beside it
- Resources/Private/Language/Modules/pagetsconfig.xlf — source-language en, no translations beside it
- Resources/Private/Language/Modules/pagetsconfig_active.xlf — source-language en, no translations beside it
- Resources/Private/Language/Modules/pagetsconfig_includes.xlf — source-language en, no translations beside it
- Resources/Private/Language/Modules/pagetsconfig_pages.xlf — source-language en, no translations beside it
- Resources/Private/Language/Modules/site_configuration.xlf — source-language en, no translations beside it
- Resources/Private/Language/Modules/site_settings.xlf — source-language en, no translations beside it
- Resources/Private/Language/Modules/status.xlf — source-language not declared, no translations beside it
- Resources/Private/Language/Modules/user_settings.xlf — source-language en, no translations beside it
- Resources/Private/Language/SudoMode.xlf — source-language en, no translations beside it
- Resources/Private/Language/Wizards/general.xlf — source-language en, no translations beside it
- Resources/Private/Language/Wizards/localization.xlf — source-language en, no translations beside it
- Resources/Private/Language/Wizards/move_content_elements.xlf — source-language en, no translations beside it
- Resources/Private/Language/Wizards/move_page.xlf — source-language en, no translations beside it
- Resources/Private/Language/Wizards/page.xlf — source-language en, no translations beside it
- Resources/Private/Language/links.xlf — source-language not declared, no translations beside it
- Resources/Private/Language/locallang.xlf — source-language en, no translations beside it
- Resources/Private/Language/locallang_alt_doc.xlf — source-language en, no translations beside it
- Resources/Private/Language/locallang_browse_links.xlf — source-language en, no translations beside it
- Resources/Private/Language/locallang_codeeditor.xlf — source-language en, no translations beside it
- Resources/Private/Language/locallang_column_selector.xlf — source-language en, no translations beside it
- Resources/Private/Language/locallang_copytoclipboard.xlf — source-language en, no translations beside it
- Resources/Private/Language/locallang_download.xlf — source-language en, no translations beside it
- Resources/Private/Language/locallang_layout.xlf — source-language en, no translations beside it
- Resources/Private/Language/locallang_login.xlf — source-language en, no translations beside it
- Resources/Private/Language/locallang_mfa.xlf — source-language en, no translations beside it
- Resources/Private/Language/locallang_mod.xlf — source-language en, no translations beside it
- Resources/Private/Language/locallang_pages_new.xlf — source-language en, no translations beside it
- Resources/Private/Language/locallang_pages_sort.xlf — source-language en, no translations beside it
- Resources/Private/Language/locallang_pagetsconfig.xlf — source-language en, no translations beside it
- Resources/Private/Language/locallang_reset_password.xlf — source-language en, no translations beside it
- Resources/Private/Language/locallang_resource.xlf — source-language en, no translations beside it
- Resources/Private/Language/locallang_settingseditor.xlf — source-language en, no translations beside it
- Resources/Private/Language/locallang_show_rechis.xlf — source-language en, no translations beside it
- Resources/Private/Language/locallang_siteconfiguration.xlf — source-language en, no translations beside it
- Resources/Private/Language/locallang_siteconfiguration_module.xlf — source-language en, no translations beside it
- Resources/Private/Language/locallang_siteconfiguration_tca.xlf — source-language en, no translations beside it
- Resources/Private/Language/locallang_sitesettings.xlf — source-language en, no translations beside it
- Resources/Private/Language/locallang_sitesettings_module.xlf — source-language en, no translations beside it
- Resources/Private/Language/locallang_toolbar.xlf — source-language en, no translations beside it
- Resources/Private/Language/locallang_view_help.xlf — source-language en, no translations beside it
- Resources/Private/Language/pages/messages.xlf — source-language not declared, no translations beside it
- Resources/Private/Language/qrcode.xlf — source-language en, no translations beside it
- Resources/Private/Language/siteconfiguration_fieldinformation.xlf — source-language en, no translations beside it
- Resources/Private/Language/user_profile.xlf — source-language en, no translations beside it
The source language is what each file declares, not what it should declare — typo3_architecture_lookup owns that rule.

Read from the files, so this is what the extension declares — not what it does at runtime. Registrations made in ext_localconf.php with a PHP call, a table or an icon list built in a loop, and anything a hook or an event listener changes, are not in this list; the files that could hold them are named above. The installation itself was not asked: <installation> has no TYPO3 console — none of bin/typo3, vendor/bin/typo3 exists.
```

Data:

```json
{
    "key": "backend",
    "path": "<installation>/typo3/sysext/backend",
    "origin": "system",
    "composerName": "typo3/cms-backend",
    "description": "TYPO3 CMS Backend",
    "requires": [
        {
            "package": "ext-intl",
            "constraint": "*"
        },
        {
            "package": "ext-libxml",
            "constraint": "*"
        },
        {
            "package": "psr/event-dispatcher",
            "constraint": "^1.0"
        },
        {
            "package": "typo3/cms-core",
            "constraint": "14.3.*@dev"
        }
    ],
    "tcaTables": [],
    "tcaOverrides": [
        "be_users"
    ],
    "contentElements": [],
    "backendModules": [
        "web_layout",
        "records",
        "content_status",
        "site_configuration",
        "link_management",
        "about",
        "pagetsconfig",
        "pagetsconfig_pages",
        "pagetsconfig_active",
        "pagetsconfig_includes",
        "content_security_policy",
        "user_setup"
    ],
    "backendRoutes": [
        "login",
        "main",
        "state-tracker",
        "logout",
        "password_forget",
        "password_forget_initiate_reset",
        "password_reset_validate",
        "password_reset_finish",
        "sudo_mode_module",
        "sudo_mode_apply",
        "sudo_mode_error",
        "login_frameset",
        "login_request_token",
        "auth_mfa",
        "setup_mfa",
        "mfa",
        "wizard_add",
        "wizard_list",
        "wizard_edit",
        "wizard_element_browser",
        "wizard_link",
        "online_media",
        "record_download",
        "record_history",
        "db_new",
        "db_new_pages",
        "pages_sort",
        "pages_new",
        "new_content_element_wizard",
        "move_page",
        "move_element",
        "show_item",
        "dummy",
        "tce_db",
        "tce_file",
        "record_edit",
        "record_edit_contextual",
        "image_processing",
        "clipboard_process",
        "resource_request_thumbnail",
        "language_domain",
        "resource_rename",
        "resource_gather",
        "resource_replace",
        "link_resource",
        "file_process",
        "file_exists",
        "file_reference_details",
        "file_reference_create",
        "file_reference_synchronizelocalize",
        "file_reference_expandcollapse",
        "record_inline_details",
        "record_inline_create",
        "record_inline_synchronizelocalize",
        "record_inline_expandcollapse",
        "site_configuration_inline_create",
        "record_slug_suggest",
        "site_configuration_inline_details",
        "record_flex_container_add",
        "record_suggest",
        "record_tree_data",
        "page_tree_data",
        "page_tree_rootline",
        "page_tree_filter",
        "page_tree_configuration",
        "page_tree_browser_configuration",
        "page_tree_set_temporary_mount_point",
        "filestorage_tree_data",
        "filestorage_tree_rootline",
        "filestorage_tree_filter",
        "bookmark_list",
        "bookmark_create",
        "bookmark_update",
        "bookmark_delete",
        "bookmark_reorder",
        "bookmark_delete_multiple",
        "bookmark_move",
        "bookmark_group_create",
        "bookmark_group_update",
        "bookmark_group_delete",
        "bookmark_group_reorder",
        "clearcache_group_pages",
        "clearcache_group_all",
        "clearcache_page",
        "systeminformation_render",
        "modulemenu",
        "topbar",
        "login",
        "logout",
        "login_preflight",
        "login_refresh",
        "login_timedout",
        "switch_user",
        "switch_user_exit",
        "mfa",
        "contextmenu",
        "contextmenu_clipboard",
        "record_process",
        "usersettings_process",
        "wizard_image_manipulation",
        "livesearch",
        "livesearch_form",
        "online_media_create",
        "icons",
        "link_browser_encodetypolink",
        "wizard_localization_get_record",
        "wizard_localization_get_targets",
        "wizard_localization_get_sources",
        "wizard_localization_get_modes",
        "wizard_localization_get_handlers",
        "wizard_localization_get_content",
        "wizard_localization_localize",
        "show_columns",
        "show_columns_selector",
        "record_download_settings",
        "record_toggle_visibility",
        "password_generate",
        "security_csp_control",
        "sudo_mode_control",
        "codeeditor_tsref",
        "codeeditor_codecompletion_loadtemplates",
        "color_scheme_update",
        "qrcode_generator",
        "qrcode_download",
        "wizard_page_get_doktypes",
        "wizard_page_get_page_detail",
        "wizard_page_get_processed_value",
        "wizard_config",
        "wizard_submit"
    ],
    "icons": [],
    "siteSets": [],
    "middlewares": [
        "typo3/cms-core/normalized-params-attribute",
        "typo3/cms-backend/locked-backend",
        "typo3/cms-backend/https-redirector",
        "typo3/cms-backend/csp-report",
        "typo3/cms-backend/backend-routing",
        "typo3/cms-core/request-token-middleware",
        "typo3/cms-backend/authentication",
        "typo3/cms-backend/backend-module-validator",
        "typo3/cms-backend/sudo-mode-interceptor",
        "typo3/cms-backend/site-resolver",
        "typo3/cms-backend/page-context",
        "typo3/cms-backend/csp-headers",
        "typo3/cms-backend/js-label-importmap-resolver",
        "typo3/cms-backend/response-headers",
        "typo3/cms-core/response-propagation"
    ],
    "serviceTags": [],
    "fluidRoots": [
        "Resources/Private/Templates/",
        "Resources/Private/Partials/",
        "Resources/Private/Layouts/"
    ],
    "fluidNamespaces": [],
    "typoScript": [],
    "classes": [
        {
            "kind": "Command",
            "files": 8
        },
        {
            "kind": "Controller",
            "files": 90
        },
        {
            "kind": "Domain",
            "files": 5
        },
        {
            "kind": "Event",
            "files": 1
        },
        {
            "kind": "EventListener",
            "files": 4
        },
        {
            "kind": "Form",
            "files": 201
        },
        {
            "kind": "Hooks",
            "files": 2
        },
        {
            "kind": "Middleware",
            "files": 12
        },
        {
            "kind": "Service",
            "files": 2
        },
        {
            "kind": "Upgrades",
            "files": 3
        },
        {
            "kind": "ViewHelpers",
            "files": 15
        }
    ],
    "files": [
        "ext_localconf.php",
        "ext_tables.sql",
        "Configuration/page.tsconfig",
        "Configuration/user.tsconfig",
        "Configuration/RequestMiddlewares.php",
        "Configuration/Services.yaml",
        "Configuration/JavaScriptModules.php"
    ],
    "notReadStatically": [],
    "artifacts": {
        "manual": null,
        "readme": "README.rst",
        "tests": [
            "Functional",
            "Unit"
        ],
        "languageFiles": [
            {
                "path": "Resources/Private/Language/Modules/about.xlf",
                "sourceLanguage": "en",
                "translations": []
            },
            {
                "path": "Resources/Private/Language/Modules/content-security-policy.xlf",
                "sourceLanguage": "en",
                "translations": []
            },
            {
                "path": "Resources/Private/Language/Modules/layout.xlf",
                "sourceLanguage": "en",
                "translations": []
            },
            {
                "path": "Resources/Private/Language/Modules/link_management.xlf",
                "sourceLanguage": "en",
                "translations": []
            },
            {
                "path": "Resources/Private/Language/Modules/list.xlf",
                "sourceLanguage": "en",
                "translations": []
            },
            {
                "path": "Resources/Private/Language/Modules/pagetsconfig.xlf",
                "sourceLanguage": "en",
                "translations": []
            },
            {
                "path": "Resources/Private/Language/Modules/pagetsconfig_active.xlf",
                "sourceLanguage": "en",
                "translations": []
            },
            {
                "path": "Resources/Private/Language/Modules/pagetsconfig_includes.xlf",
                "sourceLanguage": "en",
                "translations": []
            },
            {
                "path": "Resources/Private/Language/Modules/pagetsconfig_pages.xlf",
                "sourceLanguage": "en",
                "translations": []
            },
            {
                "path": "Resources/Private/Language/Modules/site_configuration.xlf",
                "sourceLanguage": "en",
                "translations": []
            },
            {
                "path": "Resources/Private/Language/Modules/site_settings.xlf",
                "sourceLanguage": "en",
                "translations": []
            },
            {
                "path": "Resources/Private/Language/Modules/status.xlf",
                "sourceLanguage": null,
                "translations": []
            },
            {
                "path": "Resources/Private/Language/Modules/user_settings.xlf",
                "sourceLanguage": "en",
                "translations": []
            },
            {
                "path": "Resources/Private/Language/SudoMode.xlf",
                "sourceLanguage": "en",
                "translations": []
            },
            {
                "path": "Resources/Private/Language/Wizards/general.xlf",
                "sourceLanguage": "en",
                "translations": []
            },
            {
                "path": "Resources/Private/Language/Wizards/localization.xlf",
                "sourceLanguage": "en",
                "translations": []
            },
            {
                "path": "Resources/Private/Language/Wizards/move_content_elements.xlf",
                "sourceLanguage": "en",
                "translations": []
            },
            {
                "path": "Resources/Private/Language/Wizards/move_page.xlf",
                "sourceLanguage": "en",
                "translations": []
            },
            {
                "path": "Resources/Private/Language/Wizards/page.xlf",
                "sourceLanguage": "en",
                "translations": []
            },
            {
                "path": "Resources/Private/Language/links.xlf",
                "sourceLanguage": null,
                "translations": []
            },
            {
                "path": "Resources/Private/Language/locallang.xlf",
                "sourceLanguage": "en",
                "translations": []
            },
            {
                "path": "Resources/Private/Language/locallang_alt_doc.xlf",
                "sourceLanguage": "en",
                "translations": []
            },
            {
                "path": "Resources/Private/Language/locallang_browse_links.xlf",
                "sourceLanguage": "en",
                "translations": []
            },
            {
                "path": "Resources/Private/Language/locallang_codeeditor.xlf",
                "sourceLanguage": "en",
                "translations": []
            },
            {
                "path": "Resources/Private/Language/locallang_column_selector.xlf",
                "sourceLanguage": "en",
                "translations": []
            },
            {
                "path": "Resources/Private/Language/locallang_copytoclipboard.xlf",
                "sourceLanguage": "en",
                "translations": []
            },
            {
                "path": "Resources/Private/Language/locallang_download.xlf",
                "sourceLanguage": "en",
                "translations": []
            },
            {
                "path": "Resources/Private/Language/locallang_layout.xlf",
                "sourceLanguage": "en",
                "translations": []
            },
            {
                "path": "Resources/Private/Language/locallang_login.xlf",
                "sourceLanguage": "en",
                "translations": []
            },
            {
                "path": "Resources/Private/Language/locallang_mfa.xlf",
                "sourceLanguage": "en",
                "translations": []
            },
            {
                "path": "Resources/Private/Language/locallang_mod.xlf",
                "sourceLanguage": "en",
                "translations": []
            },
            {
                "path": "Resources/Private/Language/locallang_pages_new.xlf",
                "sourceLanguage": "en",
                "translations": []
            },
            {
                "path": "Resources/Private/Language/locallang_pages_sort.xlf",
                "sourceLanguage": "en",
                "translations": []
            },
            {
                "path": "Resources/Private/Language/locallang_pagetsconfig.xlf",
                "sourceLanguage": "en",
                "translations": []
            },
            {
                "path": "Resources/Private/Language/locallang_reset_password.xlf",
                "sourceLanguage": "en",
                "translations": []
            },
            {
                "path": "Resources/Private/Language/locallang_resource.xlf",
                "sourceLanguage": "en",
                "translations": []
            },
            {
                "path": "Resources/Private/Language/locallang_settingseditor.xlf",
                "sourceLanguage": "en",
                "translations": []
            },
            {
                "path": "Resources/Private/Language/locallang_show_rechis.xlf",
                "sourceLanguage": "en",
                "translations": []
            },
            {
                "path": "Resources/Private/Language/locallang_siteconfiguration.xlf",
                "sourceLanguage": "en",
                "translations": []
            },
            {
                "path": "Resources/Private/Language/locallang_siteconfiguration_module.xlf",
                "sourceLanguage": "en",
                "translations": []
            },
            {
                "path": "Resources/Private/Language/locallang_siteconfiguration_tca.xlf",
                "sourceLanguage": "en",
                "translations": []
            },
            {
                "path": "Resources/Private/Language/locallang_sitesettings.xlf",
                "sourceLanguage": "en",
                "translations": []
            },
            {
                "path": "Resources/Private/Language/locallang_sitesettings_module.xlf",
                "sourceLanguage": "en",
                "translations": []
            },
            {
                "path": "Resources/Private/Language/locallang_toolbar.xlf",
                "sourceLanguage": "en",
                "translations": []
            },
            {
                "path": "Resources/Private/Language/locallang_view_help.xlf",
                "sourceLanguage": "en",
                "translations": []
            },
            {
                "path": "Resources/Private/Language/pages/messages.xlf",
                "sourceLanguage": null,
                "translations": []
            },
            {
                "path": "Resources/Private/Language/qrcode.xlf",
                "sourceLanguage": "en",
                "translations": []
            },
            {
                "path": "Resources/Private/Language/siteconfiguration_fieldinformation.xlf",
                "sourceLanguage": "en",
                "translations": []
            },
            {
                "path": "Resources/Private/Language/user_profile.xlf",
                "sourceLanguage": "en",
                "translations": []
            }
        ]
    },
    "answeredBy": "packages"
}
```

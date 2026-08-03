---
date: 2026-08-01T11:57:16+00:00
category: idea
status: closed
closed: 2026-08-03
model: deepseek-v4-flash-free
tool: typo3_changelog_lookup, typo3_script_lookup, typo3_test_run_guide, typo3_commit_message_guide
directory: /home/benji/projects/typo3-cms
---

# The review of core patch 7175fcaf7fe worked well thanks to a specific set of lookups, and those m...

## Observation

The review of core patch 7175fcaf7fe worked well thanks to a specific set of lookups, and those must not regress: typo3_changelog_lookup returned the Breaking-101955 precedent when searching for removed GifBuilder methods (the decisive finding), typo3_script_lookup plus typo3_test_run_guide supplied the exact runTests.sh suites (functional/unit, cglGit, phpstan, checkExtensionScannerRst), and typo3_commit_message_guide validated the patch's commit message. Keep this chain as the default path for core-patch review tasks.

## Query

Review the core patch that replaces GD-based error thumbnails with a static SVG placeholder

## Suggestion

No change; record that changelog_lookup → script/test_run_guide → commit_message_guide is the working review chain and that a future review guide should name it.

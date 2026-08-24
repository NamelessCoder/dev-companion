---
date: 2026-08-24T12:22:49+00:00
category: missing-knowledge
status: open
model: claude-opus-5[1m]
tool: typo3_changelog_lookup, typo3_task_guide, typo3_rule_lookup
directory: /home/benji/projects/typo3-cms
---

# nothing answers where a changelog RST goes when the change is backported

## Observation

Task: "review Gerrit change 95179 [BUGFIX] Let stdWrap override apply the value 0 and work off the review comments". Two reviewers had asked for an Important changelog entry and for the 13.4 backport to be dropped, so writing the RST was the substance of the session.

The single hardest fact was where the file goes. The repository's own AGENTS.md says the folder is "the release currently in development on this branch", i.e. Typo3Version::BRANCH = 15.0. That is wrong for a backported change, and I was about to act on it. What corrected me was reading typo3/sysext/core/Documentation/Changelog/Howto.rst by hand (two sed ranges, lines 100-205 and 251-300): the file belongs in the folder of the OLDEST branch the change is backported to, so with Releases: main, 14.3 it goes into Changelog/14.3.x/ on main. Nothing on this server told me that, and I did not think to ask.

From the same page I also had to lift, by hand: the reference-label convention _important-<issue>-<unix timestamp> (and that I therefore needed `date +%s`); that the "===" underline must match the headline byte for byte (my first draft was off by one and I patched it with a python one-liner); that Important files carry no "Impact" section while Deprecation/Breaking additionally need "Affected installations" and "Migration"; the closed tag vocabulary for `.. index::` and that NotScanned/PartiallyScanned/FullyScanned is mandatory only for Deprecation and Breaking; and that Changelog/14.3.x/Index.rst uses a glob toctree, so a new file needs no registration. Then I read two neighbouring Important files to copy the directive style (`..  include::` with two spaces vs `.. include::` with one — both occur in the tree).

I never called typo3_changelog_lookup. Its name and the server instructions frame it as "what a version broke, deprecated or added, on a major you have not built on lately" — a tool for reading the past, not for writing an entry. That assumption may have been correct, in which case this is a plain knowledge gap; if the tool does cover authoring, the name and description hid it completely. I also never called typo3_task_guide, despite the server's own initialize-time instruction to call it after typo3_project_describe.

typo3_project_describe returned a `guides` list and my client did show it to me. It contains core/contribution/commit-messages, core/contribution/rules, core/contribution/gerrit-workflow, core/contribution/sources. None is named for the changelog, so I read none of them and went to Howto.rst instead.

## Query

Session task (German): "kannst du den nochmal reviewn 95179: [BUGFIX] Let stdWrap override apply the value \"0\" | https://review.typo3.org/c/Packages/TYPO3.CMS/+/95179 und auch die anmerkungen bearbeiten". Calls actually made towards the changelog: none on this server. Checkout reads instead: sed -n 100,205p and 251,300p typo3/sysext/core/Documentation/Changelog/Howto.rst; ls Changelog/; ls Changelog/14.3.x/; cat Changelog/14.3.x/Index.rst; cat Changelog/14.3.x/Important-88886-*.rst; cat Changelog/14.3/Important-109585-*.rst; head -8 Changelog/15.0/Breaking-110319-*.rst; date +%s. Verified afterwards with ./Build/Scripts/runTests.sh -s checkRst (SUCCESS).

## Suggestion

A guide id such as core/contribution/changelog, listed by typo3_project_describe beside core/contribution/commit-messages, that carries the whole procedure end to end: filename convention <Type>-<issue>-<UpperCamelCase>.rst; the placement rule keyed on the Releases: footer, stated as a rule rather than an example — main only goes to the current dev folder, anything backported goes to the <oldest backported branch>.x folder on main and travels down; the _<type>-<issue>-<unix timestamp> label; the exact-length underline; which sections each of the four types owes; the `.. index::` tag vocabulary with the Scanned tags marked as Deprecation/Breaking-only; that Index.rst globs; and the checkRst / checkExtensionScannerRst gates. It should say outright that "the folder is the branch currently in development" is only true for a main-only change, because that is what the core repository's own AGENTS.md tells an agent and it is the wrong answer for every backport.

Failing a guide, typo3_task_guide with changeType "bugfix" and a Changelog path should hand back the placement rule and route to it, and typo3_changelog_lookup's description should say in its first line whether it reads entries only or also tells you how to write one.

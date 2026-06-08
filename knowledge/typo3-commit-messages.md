# TYPO3 Core Commit Message Rules

Source: https://docs.typo3.org/m/typo3/guide-contributionworkflow/main/en-us/Appendix/CommitMessage.html

TYPO3 core commit messages are part of the contribution workflow and are checked by tooling. Keep this document aligned with the official TYPO3 Core Contribution Guide.

## Summary Line

- Start with one of `[BUGFIX]`, `[FEATURE]`, `[TASK]`, or `[DOCS]`.
- Add `[!!!]` before the keyword for breaking changes.
- Do not use `[SECURITY]` unless this is handled by the TYPO3 Security Team.
- Keep the summary below 52 characters if possible and below 72 characters in any case.
- Use imperative present tense, for example `Fix`, `Add`, `Improve`, or `Remove`.
- Describe what the patch changes, not what used to be broken.
- Start the summary text after the keyword with a capital letter.
- Avoid `EXT:some_extension` in the subject when the changed files already make the extension context clear.

## Body

- Separate the summary and body with a blank line.
- Keep the body brief and focused on what changed and why.
- Do not repeat full reproduction instructions from the Forge issue.
- Wrap body lines manually after 72 characters.

## Relationships

- `Resolves: #12345` is required.
- Multiple resolved issues need one `Resolves:` line per issue.
- `Related: #12345` is optional and cannot replace `Resolves:`.
- `Releases: main, 13.4` lists target versions.
- Do not create or change `Change-Id:` manually. The commit hook creates it. Keep it when amending an existing Gerrit patch set.

## Breaking Changes

- Breaking changes must use `[!!!]` before the keyword.
- Breaking changes must be documented with a changelog RST file.
- Breaking changes should usually target `main`.

## Deprecations

- Deprecations must not use `[!!!]`.
- Deprecations may only use `[TASK]` or `[FEATURE]`.
- Deprecations must be documented with a changelog RST file.
- Deprecations need migration guidance and may need extension scanner considerations.

## Changelog Files

- Changelog entries live below `typo3/sysext/core/Documentation/Changelog/`.
- Common filename prefixes include `Breaking-`, `Deprecation-`, `Feature-`, `Important-`, and `Task-`.
- Include the Forge issue number in changelog filenames when possible.
- Run `./Build/Scripts/runTests.sh -s checkRst` for ReST changes.

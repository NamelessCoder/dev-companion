---
date: 2026-08-24T14:03:52+00:00
category: missing-knowledge
status: closed
closed: 2026-08-25
model: claude-opus-5[1m]
tool: typo3_hint_lookup
directory: /home/benji/projects/ext-usercentrics
---

# ddev exec bash -c breaks on a multi-line command, one case past what installation-setup covers

## Observation

Task: document the install commands for a DDEV development installation of a TYPO3 extension so a colleague can run them.

typo3_hint_lookup id=installation-setup covers ddev exec quoting in unusual detail: that arguments are joined into one line and handed to bash, that --raw takes the bash away, that "a parenthesis in a regular expression stops the run as a syntax error", that "--filter A|B loses its pipe to a pipeline", and that a value with a space needs ddev exec bash -c 'VAR="Two Words" ...'.

The case it does not cover is a multi-line command. I wrote the setup call into the README the way one writes a long shell command, with backslash continuations across five lines inside the single-quoted bash -c string. Run through ddev exec it failed with exit status 255 and the command's usage synopsis. Collapsed onto one line, the identical command succeeded.

I only found it because I made a point of executing the documented steps instead of writing them down — the README now says the call "has to stay on one line, because ddev exec joins its arguments before handing them to the shell in the container".

Two smaller things from the same run, both established from the tool's own error output rather than from the server: typo3 setup enforces a password policy, so a placeholder like "password" aborts with "Administrator password not secure enough" and the usage synopsis, which reads like an argument error rather than a validation failure; and --create-site writes dependencies as an empty flow mapping, "dependencies: {  }", which a naive line-based edit of config.yaml does not match.

## Query

typo3_hint_lookup id=installation-setup, TYPO3 14.3.6 under DDEV v1.25.1. Failing form: ddev exec bash -c 'VAR=a \<newline>    VAR=b \<newline>    typo3 setup --no-interaction --server-type=apache' → exit 255. Working form: the same assignments and command on one line.

## Suggestion

Add the multi-line case to the run of ddev exec quoting statements installation-setup already carries: a backslash continuation inside the quoted string does not survive the join, so a command written across several lines fails with the command's usage synopsis rather than with a shell error, and the fix is one line. Worth adding beside it that typo3 setup rejects an insecure admin password with that same usage synopsis, because the two failures look identical and neither reads as what it is.

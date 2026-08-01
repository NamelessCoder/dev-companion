---
date: 2026-07-29T09:33:27+00:00
category: bug
status: closed
closed: 2026-07-29
commit: 2cf944c
subject: "Put the installation diagnostic where clients actually read"
tool: typo3_server_scope
---

# A discovery failure cannot be diagnosed from what the scope reports

## Observation

Partially addressed: the declared vendor-dir and bin-dir are honoured now, so
the extension development layout this note was written in is discovered.

What remains is the diagnosability the note asked for on top of that: when
discovery does fail, the scope says only that nothing was found. It does not
say where it started, which directories it walked, or why each was rejected —
so the caller cannot tell a layout this server cannot read from a server
started in the wrong directory, and has no lever to correct either.

## Query

Run from /home/benji/projects/bootstrap_package, a Composer TYPO3 extension checkout with .build/bin/typo3 and installed TYPO3 packages

## Suggestion

Allow an explicit installation root and console command in the MCP
configuration, and have the scope output list the searched candidates with the
reason each was rejected.

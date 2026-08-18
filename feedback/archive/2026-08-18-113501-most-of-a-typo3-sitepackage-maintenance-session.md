---
date: 2026-08-18T11:35:01+00:00
category: idea
status: closed
closed: 2026-08-18
model: claude-opus-5[1m]
tool: typo3_project_describe, typo3_task_guide
directory: /home/benji/projects/bootstrap_package
---

# most of a TYPO3 sitepackage maintenance session was Node, npm and GitHub Actions work with no TYP...

## Observation

Task: review PR #1627 against the bootstrap_package sitepackage — then, after the review, "bitte behebe den fehler erst und schau das die ci wieder grün wird" about an unrelated red CI job.

Reporting this because the debrief did not ask for it and the server has no other way to see it. The TYPO3 part of this session — the TCA palette review — was roughly the first sixth of it. The remaining five sixths had no TYPO3 surface whatsoever, and the server correctly had nothing to offer. The bug was that Node 24.19 raised Buffer.poolSize from 8 KiB to 64 KiB, which made fantasticon's EOT generator (`Buffer.from(font.buffer)` instead of the view ttf2eot returns) write 65536 bytes instead of 5828 — the real font data followed by ~59 KiB of recycled process heap, different on every run, which broke the repository's working-tree-clean check and had been shipping heap memory inside a published asset. Finding that took reading node_modules sources, a clean-clone reproduction, a `node:24` container to reproduce what the local Node 24.16 would not, and a Buffer.poolSize comparison across four Node majors.

The point for this server: a sitepackage's build pipeline — Grunt, fantasticon, sass, sharp, svgo, and the GitHub Actions workflow that asserts the build is reproducible — is part of maintaining a TYPO3 extension in practice, and is where this session's real defect lived. It is not obviously in scope, and I am not arguing it should be. I am recording that if the boundary is drawn at TYPO3 APIs, then a tool answering "what does this repository's frontend build consist of and what asserts it" is outside it, and the majority of a real maintenance session goes unassisted. That is a scoping decision for the maintainers rather than a gap I am claiming.

## Query

Whole session after the initial review. Reproducing and fixing a non-reproducible icon-font build in bootstrap_package: Build/Gruntfile.js, Build/package.json, fantasticon 3.0.0, ttf2eot 3.1.0, .github/workflows/ci.yml, Node 24.16 local vs 24.19 in CI.

## Suggestion

If the boundary is meant to include a project package's build and CI surface, then typo3_project_describe reporting what the repository's frontend build consists of, which of its outputs are committed artifacts, and what its CI asserts about them, would have been worth a call at the start of that phase. If the boundary excludes it, no change is needed — but the decision is worth making explicitly, because a maintainer asking "get CI green on my sitepackage" will land here regardless of where the line is drawn.

---
id: D-KNW-061
title: The manual scaffold is a document and the hint keeps the policy
date: 2026-08-04
status: open
---

# D-KNW-061 — The manual scaffold is a document and the hint keeps the policy

**What an extension's `Documentation/` directory consists of, and the command
that renders it, is written as a document in `knowledge/documents/`, while
`extension-documentation` goes on stating the policy.**

A session told an extension needs a manual read four installed packages to find
out what to put in the directory.

## Evidence

- `feedback/2026-08-04-175804`. The hint says a manual lives in `Documentation/`
  with `Index.rst` as entry point and `guides.xml` as renderer configuration,
  and that it ships with the package. The official page says `Documentation/` is
  recommended over a single README and why. Neither says what has to be in the
  directory.
- What the session read instead:
  `vendor/typo3fluid/fluid/Documentation/guides.xml` and
  `vendor/typo3/cms-dashboard/Documentation/guides.xml` for the file shape, plus
  `Includes.rst.txt` and the `Index.rst` header block with its toctree. It found
  the renderer and the flag that makes a warning fail a build without help from
  here.
- The same batch carries the other half of this domain:
  `feedback/2026-08-04-175935` is an audit that never asked the convention at
  all — `D-SKL-019`. Two sessions of one day arrived at the manual from
  different directions, and neither got a scaffold out of this server.
- [`D-FBK-043`](../feedback/fbk-043-a-structure-is-answered-with-a-document-rather-than-with-a-rule.md)
  is the shape: a session that found a *structure* unclear is answered with a
  document, because a hint states one thing and a file inventory is not one
  thing. `knowledge/documents/extension/testing/phpunit.md` is the neighbour it
  is written beside.

## Decided

- The judgement is **step 1a**, the knowledge is missing, and it is **taken
  on**: a document below `knowledge/documents/extension/documentation/`,
  declaring what it is and when to reach for it as
  [`D-KNW-057`](knw-057-a-document-declares-what-it-is-and-when-to-reach-for-it.md)
  requires.
- The hint is not grown into the scaffold. It keeps the policy — where a manual
  lives, that it ships with the package, what a breaking change owes its readers
  — and the document names it in its own `hints:` front matter, which is how the
  two are already tied.
- Not closed on the spot. What a `guides.xml` has to contain is a fact about the
  renderer, and it is read from the renderer's own documentation and from
  packages that ship one, never copied out of the report.

## Assumed

- That the scaffold is fixed enough to write down. Two packages agreed on it in
  the session's reading, and the todo's first step is whether a third and the
  renderer's own documentation do.

## Wrong if

- The file shape turns out to move with the docs theme or the TYPO3 major, and
  the document carries no binding for it. Then this is a statement with a
  `since` rather than a page.
- A session that read the document still opens a vendor package to copy a file.
  Then what was missing is a template the server hands over, not a description
  of one.

## Since then

On 2026-08-04, the scaffold was established and written as
`knowledge/documents/extension/documentation/manual.md`.

The minimal set is two files, and the official reference says so: an entry point
`Documentation/Index.rst` and a `Documentation/guides.xml`. `Includes.rst.txt`
is optional and conventional, `Sitemap.rst` is filled by the renderer. What the
renderer needs of the configuration is the namespace, the theme class on
`extension` and `title` on `project`; the rest of the attributes produce the
edit link and the project header.

Both were verified by rendering rather than by reading. A directory holding only
those two files rendered, and the measurement that made the page worth writing
is the exit code: a tree whose `Index.rst` includes a missing `Includes.rst.txt`
logs the failed directive, prints "Successfully placed" and exits **0**; the
same run with `--fail-on-log` exits **1**. So the flag is what turns the
renderer into a check, which is what the reporting session said and what neither
the official page nor the renderer's README states.

Two files that look like the template are named as not being it: a core system
extension's `guides.xml`, which points `edit-on-github` at `typo3/typo3` and
carries `typo3-core-preferred`, and the `Settings.cfg` an unmigrated manual
still ships — `/home/benji/projects/bootstrap_package` is one, so a session
copying from a real published extension can land on the replaced renderer.

The document is announced in `knowledge/server-scope.json`, which
`ScopeTest::everyKnowledgeDocumentIsAnnouncedByTheScope` requires.

---
id: D-KNW-058
date: 2026-08-04
status: open
---

# D-KNW-058 — The document namespace is scope first and derived from the file

**A document sits below a directory named for the scope it answers for, its path
below `knowledge/documents/` is its id, and its resource URI is that path under
`typo3://guides/`.**

`typo3://core/` was true of every document while every document was the core's
own process. The first one that answers for a package alone is announced to
every client under a prefix that says the opposite.

## Evidence

- `typo3://core/typo3-extension-phpunit-setup` is what a client is offered
  today. The page is about setting a package up, and the core's own harness is a
  different one — the prefix states the scope and states it wrongly.
- The scope was already declared twice before that. `Documents::isCoreOnly()`
  reads it from the `covers` row, and the file name carries it as a word
  somebody chose to include.
- Nothing in the resource layer resists a path. `ResourceHandler` takes
  everything after `typo3://core/` as the id, so a segmented one already
  resolves; what stops it is `depth(0)` in `Documents::documents()` and the
  `[a-z0-9-]+` in `ScopeTest`.
- That `depth(0)` is a guard rather than an oversight. `Paths::documents()`
  records why: lying in the directory is what publishes a file, and a readme
  laid beside the corpus became `typo3://core/readme` without anybody deciding
  it.
- The rename reaches prose rather than code. The URIs are written into the
  corpus, into skills and into `server-scope.json`, and `bin/cli links:check`
  and `ToolNamingTest` are what find the ones a sweep misses.

## Decided

- The first segment is the scope, spelled as `Knowledge\Scope` spells it, and it
  is the scope: moving a document between those directories is how it is
  rescoped, so no field repeats it and the two cannot drift apart.
- The segments below it are the subject, as deep as the subject needs.
  `extension/testing/phpunit` is one document about one thing rather than a
  short name carrying three words of context.
- The prefix is `typo3://guides/`. `core` cannot stay: it names one of the
  scopes below it now, and a prefix that repeats a segment says nothing.
- `depth(0)` is replaced rather than dropped. A file publishes itself only from
  a directory named for a scope, so a readme laid anywhere else in the tree is
  not a resource, which is the property the depth limit was standing in for.
- The five existing documents move and their URIs change with them. A URI a
  client holds in a note goes stale, and the alternative is a prefix that lies
  about every document that is not the core's.

## Assumed

- Clients read the resource list per session, so a changed URI costs a stale
  note rather than a broken integration. Nothing here measures what a client
  keeps.
- The scopes make a usable first segment. `any` reads oddly as a directory and
  is the honest name for a document that holds everywhere.

## Wrong if

- A document lands in a directory that is not a scope, so nothing publishes it
  and nothing says it was meant to be published.
- A file that is not a document is published because it was laid inside a scope
  directory.
- The scope in the path and the scope a `covers` row states for the same
  document disagree.
- A URI written in prose survives the move and resolves to nothing.

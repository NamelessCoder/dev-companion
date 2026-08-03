---
id: D-DOC-011
date: 2026-08-03
status: open
---

# D-DOC-011 — A schema is written as the shape it validates

**The tool surface renders a schema as YAML — a key per field, nested where the
schema nests, the type as the value — and not as a bullet list.**

A bullet list of fields could not say where a field sits, which is the one thing
a caller reads a schema for.

## Evidence

- Every field came out at the same level however deep it sat. In
  `typo3_server_scope`, `covers` and the `topic` of one of its entries were two
  bullets in one list, so the answer read as though it carried a top-level
  `topic` — and eleven of that tool's twelve nested fields read the same way.
- The renderer computed an indent and passed it to `Wrap`, which trims what it
  is handed. The nesting was written and thrown away on the same line, which is
  why nothing looked broken in the code.
- The list cost more characters than the shape does: `- ` plus a code span plus
  an italic type plus `— ` per field, against `name: type`.

## Decided

- One key per field, the fields of an object or of a list entry nested under it,
  and the value is the type. `[string]` is a list of strings; a `- ` opens a list
  of objects.
- The description is a comment above the key rather than behind it. Behind it,
  a sentence pushes the shape off the right of the block and the reader loses the
  column the keys line up in.
- A field carries `# optional` where it may be absent, and nothing where it is
  required. Required is the promise a client validates against, so it is the
  default and the exception is what is marked.
- A closed set is written into the comment as `One of: a, b, c.`, before the
  description, because it is what a caller has to pass rather than something to
  understand.
- The block is fenced `yaml` and is not valid YAML in the sense of parsing to
  the answer. It is the shape of one, and a reader who pastes it gets the field
  names and the nesting, which is what they came for.

## Assumed

- A reader takes `name: string` as a type rather than as a value. Nothing was
  measured about that; the alternative is a second column of `(string,
  required)` annotations, which is the list this replaced.

## Wrong if

- Somebody validates against a page instead of against the `outputSchema` the
  tool declares, and files a defect because the page does not parse. Then the
  block needs a language nothing renders as data — but the schema itself is what
  a client should be reading, and the page says so.

## Covered by

- `ToolSurfaceTest::everyPageIsWhatTheRegistryDeclares`

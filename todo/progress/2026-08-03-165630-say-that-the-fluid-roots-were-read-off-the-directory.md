# Say that the Fluid roots were read off the directory

**Serves:** R-ANS-020, feedback/2026-08-03-164651-task-full-conformance-audit-of-the-project.md
**Priority:** normal
**Branch:** todo/say-that-the-fluid-roots-were-read-off-the-directory
**Claimed:** 2026-08-03

`Extension::fluidRoots()` at `src/Installation/Extension.php:1140` is `is_dir()`
over Templates, Partials and Layouts, and the schema at
`src/Tool/ExtensionScope.php:122` says so. Two other places do not: the rendered
line at `:176` reads `Fluid roots:` with no qualifier, and
`ExtensionScope::description()` at `:65` lists them among the service tags and
the middlewares. An extension that appends its layout root while an event runs
gets the same line as one that declares it. Qualify both so a directory that
exists is not read as a declaration, and check whether `typoScript`, `files` and
`artifacts` stand in the same position before settling the wording.

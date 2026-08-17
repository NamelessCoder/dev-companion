# Correct what the impexp-artifact hint says about the export command

**Serves:** feedback/2026-08-17-204536-impexp-export-needs-include-related-sys-file.md, feedback/2026-08-17-211418-impexp-export-ignores-an-ext-filename-and.md
**Priority:** normal

Step 1a on both, judged in `D-KNW-080`: two sentences of `impexp-artifact` in
`knowledge/hints/distribution.json` describe the console and were read off
`Export` rather than off `ExportCommand`, and both are wrong on all four covered
versions. Export a page tree with images from an installation —
`bin/cli environment:create` makes one — with and without
`--include-related=sys_file`, and establish what the minimal correct invocation
is and what a correct artifact looks like from the outside; then rewrite the
images sentence around `--include-related`, say that the export takes the
basename of the filename argument and writes into the default import-export
folder while `EXT:<key>/…` holds for `impexp:import`, and give the procedure the
discriminator both feedback ask for. Nothing here is bound: `inclRelation()` and
the `basename()` call read the same on 12.4, 13.4, 14.3 and `main`.

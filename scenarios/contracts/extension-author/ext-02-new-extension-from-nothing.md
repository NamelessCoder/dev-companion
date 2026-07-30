# EXT-02 — A new extension from nothing

**Environment:** `E-SITE`, in a project where the new extension will live
under `packages/` · **Contract:** `held` — `R-SCO-1`, `R-SCO-2`

> I need a new extension `acme_events` in this project: a record type for events
> with dates and a location, editable in the backend, listed in the frontend by a
> plugin. Set it up from scratch.

**What the agent needs from this server**

- The file layout of an extension and what each required file is for.
- TCA conventions for a new table, and how the schema is declared.
- Services and dependency injection, so the plugin is not built with
  `GeneralUtility::makeInstance` everywhere.
- Where labels go, what the file is called, and what domain it resolves to.
- How an icon for the record type is registered, and which identifier to use.
- Fluid conventions for the plugin template, and the namespaces available
  without declaring them.

**What has to come out of it**

- The answer is right for an extension, not for a system extension: paths under
  the package, no core-only steps, no checkout discovery instructions for a
  checkout that is not there.
- The label domain for a file that does not exist yet is computed, not guessed.
- The registered icon identifier is verified against the installation.

**How it fails**

- The task recognised as core work because it is about TCA and Fluid, and
  answered with core paths (`R-SCO-1`).
- The extension's own not-yet-installed state confusing the installation-backed
  lookups into reporting "nothing registered".

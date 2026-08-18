---
id: D-VER-006
date: 2026-08-18
status: confirmed
---

# D-VER-006 — A narrowed statement is split before it is bound

**A statement whose subject survived its boundary under a new condition is
split, so `until` binds only the half that stopped holding.**

A range is read as the whole truth set of the sentence it sits on, which is what
`D-VER-001` chose it for. Put on a sentence carrying two claims, it expires
both, and the caller reads the surviving half as removed.

## Evidence

- `installation-setup` carried "--create-site <url> and TYPO3_SETUP_CREATE_SITE
  create the root page and its site configuration under config/sites/, and
  nothing suppresses them" at `until: 13`, beside a `since: 14` statement about
  `--distribution`. A session installing on 14.3.6 read the pair as
  "--create-site is gone on v14" and recovered the true reading only from a
  clause in the middle of the neighbouring paragraph —
  `feedback/2026-08-18-070632`.
- `.checkouts/13.4` reaches `getSiteSetup()` and `createSiteConfiguration()`
  with no distribution branch in `SetupCommand` at all. `.checkouts/14.3` and
  `.checkouts/main` guard the same call with `$distributions['active'] === []`
  and warn where it does not hold. The option was narrowed at 14, not removed;
  the clause that expired is "nothing suppresses them".
- The cost runs both ways. Believing it removed leaves the installation with
  what the same hint calls no way to be told its own URL; believing it
  unconditional produces the doubled root page the operations checklist warns
  about.

## Decided

- The binding goes on the claim, so a compound statement is split before either
  half is bound.
- `installation-setup` is rewritten that way: what `--create-site` writes is
  unbound across the covered majors, and the condition that arrived becomes a
  `since: 14` statement of its own, standing in front of `--distribution` rather
  than inside it. The second copy of the condition goes with it.
- Rejected: keeping the tag and repairing the prose, which puts version words
  back into a sentence — `HintsTest` forbids that and `D-VER-001` is why.

## Assumed

- The reader takes the range as the statement's whole truth set rather than
  reading the neighbouring statements against it. That is what the answer
  renders, and `D-VER-001` chose filtering over qualifying so a caller need not
  read around it.

## Wrong if

- A split leaves the unbound half general enough that a caller acts on it where
  the condition applies — the doubled root page, rather than the missing site
  configuration this one produced.
- Another `until`-bound statement is found whose subject survived its boundary.
  `D-VER-001`'s reading of 2026-08-02 checked every range for holes and could
  not see this: a compound statement bound whole has a contiguous range.

## Confirmed on 2026-08-18

The second **Wrong if** happened twice, and the rule absorbed both. All 44
`until`-bound statements in `knowledge/hints/` were read against
`.checkouts/12.4`, `13.4`, `14.3` and `main`, and two carried a claim that
outlived the boundary the sentence was bound at. `css-color-surface-tokens` said
at `until: 12` that there is no semantic layer **and** that the backend's
properties are component-scoped over Bootstrap variables; the
`--typo3-component-*` and `--typo3-input-*` families and the Bootstrap import in
`Build/Sources/Sass/_minimal.scss` are on all four branches, so only the first
half expired. `form-framework` said at `until: 13` that a setup path is
registered under `plugin.tx_form.settings.yamlConfigurations` and
`module.tx_form.settings.yamlConfigurations` **and** that the registration is
deprecated; `Form\Mvc\Configuration\ConfigurationManager` on `14.3` still merges
a path found under either key and raises `E_USER_DEPRECATED` over it, while
`main` no longer reads the key at all — so the registration expires at 14 and
the deprecation is what arrives there. Both were split the way the **Decided**
section names.

The other 42 are predecessor and successor pairs where what expired is the whole
of what the sentence claimed, and where the surviving detail is carried by a
statement on the other side: `fluid-templates` at `until: 13` describes the
candidate chain that `since: 14` restates with the uppercase fallback in it, and
`fluid-resource-uris` at `until: 13` says there is no resource object where
`since: 14` says `f:uri.resource` still takes a plain path. Four looked like
this defect and are not: `makeShortcutButton()`, `PageDoktypeRegistry::add()`,
`addAllowedRecordTypes()` and the `event.listener` tag all survive their
boundary as deprecated routes, and a caller who reads them as removed reaches
the successor the neighbouring statement names, which is where they were being
sent anyway.

The first **Wrong if** is not measured and cannot be from here: whether a split
leaves its unbound half too general shows up in a session acting on it, not in a
checkout.

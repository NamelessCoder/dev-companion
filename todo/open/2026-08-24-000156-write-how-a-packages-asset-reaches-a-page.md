# Write how a package's asset reaches a rendered page

**Serves:** D-SKL-067
**Priority:** high

Write the document that names every way a package's own CSS and JavaScript gets
into a page, and says of each how it is checked: the TypoScript `includeCSS` and
`includeJS` keys, the AssetCollector behind `<f:asset.css>` and
`<f:asset.script>`, `PageRenderer` from PHP, the import map in
`Configuration/JavaScriptModules.php` for backend JavaScript, and the publish
step underneath all of them. Verify each against `.checkouts/` and bind what
does not hold on every covered major, the way `knowledge/` binds anything else.
`any/assets/how-an-asset-reaches-a-page.md` is the placement to confirm rather
than assume, because the path is a `Scope` declaration.

It is `high` because the queue's other todo waits on it: the frontend half of
`skills/typo3-extension-asset-build/` has nothing to route to until this exists.

## Why a document and not a hint or a rule

What the reading found missing is a **structure** — which routes there are, and
which check belongs to each — and `AGENTS.md` sends that to
`knowledge/documents/` rather than to a requirement or a sentence in a skill.
Two properties decide it further:

- A skill cannot bind a version. `since` and `until` are data in `knowledge/`
  and `HintsTest` enforces them, and this surface moves across the majors: the
  System Resource API and `Configuration/Resources.php` arrive at v14, and
  `PathUtility::getPublicResourceWebPath()` is deprecated there. A sentence in a
  `SKILL.md` would be true on some majors and silent about which.
- A document reaches a session that loads no skill. `typo3_project_describe`
  ends with the procedures this server carries, as ids, so the whole corpus is
  announced in the first step of every task. The `EXT:blog` session read the
  skill listing and called nothing, and that is the session this has to reach.

## What is already there, and what is not

Read on 2026-08-24:

- `public-assets` holds the publish step whole and per major: `_assets/<hash>/`,
  `asset:publish`, what a Composer install does, and from v14
  `Configuration/Resources.php` and `CanNotResolvePublicResourceException`. The
  document names it rather than repeating it.
- `extension-declarative-files` holds one sentence on the import map: the
  dependencies list and the map from bare specifier to an `EXT:` path.
- `fluid-layouts-sections` holds the one trap anybody has written down —
  `<f:asset.css>` outside a section registers with the AssetCollector, renders
  nothing where it stands, and produces no request and therefore no 404.
- Nothing holds the set. No hint and no document says what the routes are, so a
  reader who does not already know one cannot find it, and a session cannot
  check that the route a package uses still carries after a rebuild.
- `backend-typescript` and `css-source-build-boundaries` are the core's own
  build trees. `extension-asset-build` says outright that those paths and
  commands do not transfer to an extension, so neither answers this.

## What the reading has to settle

Each of these is a claim from recall and none is verified here:

- Which routes exist per major, and which of them a package may use in the
  backend as against the frontend.
- What a static check can establish. The import map is a file and the build
  writes files, so the backend side compares. Whether a stylesheet reaches a
  page is decided by the TypoScript that resolves for a site, so the frontend
  side needs a rendered page or the resolved TypoScript, and the document says
  which.
- Whether backend CSS has a declared route of its own or arrives through the
  module registration.

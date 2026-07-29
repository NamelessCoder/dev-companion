# Site developer

Someone who builds and maintains one installation: the site configuration, the
site package, the content structure, the upgrades, the deployment. This is the
audience furthest from what the knowledge base was written for, so most of these
are `boundary` or `gap` — and how honestly the server declines is the thing
being measured.

An installation is also the one place where this server stops answering from
its own bundles and asks the installation instead. Those scenarios are where
that has to earn its keep.

See [readme.md](readme.md) for how to run one and what the marks mean.

---

## SITE-01 — A new site from scratch

**Environment:** `E-SITE`, freshly installed · **Status today:** `boundary`,
partly `gap`

> Fresh TYPO3 here. Set up the site: a site configuration with two languages, a
> site package with our templates, and the TypoScript so the frontend renders.
> The page tree structure I will do myself.

**What the agent needs from this server**

- What a site set is, what belongs in it, and how it relates to the site
  configuration.
- Where TSconfig ends and TypoScript begins.
- The Fluid conventions for the templates, and which namespaces are available
  undeclared in this installation.
- An honest boundary where the rest — the site configuration file itself, the
  language setup, the installation steps — is not covered.

**What has to come out of it**

- Site set and TSconfig conventions come over as conventions, without core paths
  or core checks attached.
- What is not covered is named as not covered, with the documentation as the
  route, rather than filled in with something plausible.
- Nothing suggests that a `typo3/sysext/` path is where this project's files go.

**How it fails**

- The core's own TypoScript defaults presented as how a site is configured.
- The scope boundary stated in one paragraph and then contradicted by the
  checklist below it.

---

## SITE-02 — The installation has to go up a major

**Environment:** `E-SITE` on the previous major · **Status today:** `gap`

> This site is on the previous LTS and we need to be on the current one before
> support ends. Plan the upgrade, tell me what will break, and start with the
> parts that are safe.

**What the agent needs from this server**

- The order of operations: dependencies, the upgrade wizards, the extension
  scanner, the caches, what has to run in which environment.
- How to find out what breaks — the scanner, the changelog, the deprecation
  notices in the log.
- What the third-party extensions in this installation do to the plan, and which
  ones are actually installed here.
- The effective configuration as it is now, so what the upgrade changes can be
  compared against something real.

**What has to come out of it**

- The parts that are properties of this installation are read from it rather
  than assumed.
- The upgrade procedure itself is either answered or declined; what it must not
  be is guessed. A wrong order here costs a production site.

**How it fails**

- An upgrade plan assembled from the model's own memory of TYPO3 majors,
  indistinguishable in tone from the parts that came from the knowledge base.
- Version-specific claims with no branch named (`R-AUD-4`).

---

## SITE-03 — Something behaves differently than documented

**Environment:** `E-SITE` · **Status today:** `covered`

> Editors report that a field in the news form is suddenly read-only, and I
> cannot find where that comes from. Nothing in our own configuration does it.
> Find out what is actually configured in this installation.

**What the agent needs from this server**

- The effective value of the configuration involved, after every extension has
  had its say — not the shipped default.
- Which extensions are active in this installation at all.
- The conventions of the subsystem doing it, so the agent knows where such an
  override can come from: form data providers, TCA overrides, event listeners.
- Which backend module the editors are actually in, and what registers it.

**What has to come out of it**

- The configuration value comes from this installation's runtime, and the answer
  says it did.
- The subsystem conventions explain the mechanism, and the agent then finds the
  concrete override in the checkout rather than being told which one it is.
- Where the installation could not be asked, that is said — and is
  distinguishable from a value that is genuinely empty.

**How it fails**

- The core default quoted as the effective value.
- An unreachable console producing an answer shaped like "nothing is configured"
  (`R-ANS-1`, `R-ANS-2`).

---

## SITE-04 — The frontend theme

**Environment:** `E-SITE` with a site package using Bootstrap ·
**Status today:** `gap` — `R-SCO-4`

> Our site package theme is Bootstrap 5 with our own SCSS on top. The card
> component looks wrong on mobile and the spacing is inconsistent across
> breakpoints. Clean up the SCSS and make the components consistent.

**What the agent needs from this server**

- Nothing, honestly, beyond recognising that this is not the TYPO3 backend. The
  server's CSS knowledge is the backend's, and the backend left Bootstrap behind.
- The Fluid side of the templates, where it comes up.

**What has to come out of it**

- The backend CSS architecture, its design tokens and its class naming do **not**
  come back as advice for a Bootstrap frontend theme.
- If CSS knowledge is offered at all, it is marked as the core backend's and as
  not applying here.

**How it fails**

- Backend token names, backend component class naming, and "the backend has
  moved off Bootstrap" handed over as instructions for a Bootstrap theme — four
  confidently inverted hints, which is the note this scenario exists for
  (`R-SCO-4`).
- A `.scss` path being enough to trigger core CSS conventions.

---

## SITE-05 — Content elements for the editors

**Environment:** `E-SITE` · **Status today:** `partial` — `R-KNW-1`

> Editors need a "team members" content element: a list of people picked from a
> folder, rendered as cards. Build it in our site package — the element, its
> backend form, and its frontend output.

**What the agent needs from this server**

- The TCA conventions for the element and its fields.
- How the data gets to the template: the data processor, what it is registered
  as, what it may do.
- The Fluid conventions for the template, and the namespaces available
  undeclared.
- The label domain of the site package's own language file.
- The backend preview side, and the icon for the element.

**What has to come out of it**

- Conventions come over without core checks or core paths attached.
- Where the answer for data processors is thin, that is said rather than covered
  with generic Fluid advice (`R-KNW-1`).

**How it fails**

- Fluid and TCA hints returned as though they were the whole answer, with the
  data processing step silently skipped.
- An icon identifier invented for the element instead of taken from the
  registry.

---

## SITE-06 — Testing a site project

**Environment:** `E-SITE` · **Status today:** `boundary`

> Before every deployment I want a smoke test: the important pages render, the
> forms submit, the backend login works. Set that up for this project.

**What the agent needs from this server**

- The honest statement that testing a site installation is not covered, and
  where it is documented.
- Whatever testing conventions transfer at the level of what to test rather than
  how to run it.

**What has to come out of it**

- No core suite, no `runTests.sh`, and no acceptance-test setup borrowed from the
  core repository presented as this project's.
- The decline is one sentence and a route, not a paragraph of apology.

**How it fails**

- The core's own acceptance testing setup described as though a site project had
  it.
- The boundary stated and then a full invented setup delivered anyway.

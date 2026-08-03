---
id: D-KNW-043
date: 2026-08-03
status: open
---

# D-KNW-043 — A rule about what an API may be used for carries the strength of the claim and the source it was read from

**Where the corpus states what an API may be used for, it names the source the
rule was read from and how strongly that source puts it.**

Enforced in code, warned about in a docblock and advised in prose are three
different claims, and two neighbouring APIs regularly make different ones. The
reporting session accepted a maintainer's "you *must not* use `f:image` for
anything but FAL resources" and then found the checkout contradicting it. The
corpus was not the innocent party: the one sentence it held on the subject
flattened `f:image` and `f:uri.image` into a single documented rule, and only
`f:uri.image` carries it.

## Evidence

- The feedback. `feedback/2026-08-02-144814`, `claude-opus-5[1m]`, from
  `/home/benji/projects/typo3-cms`,
  `tool: typo3_task_guide, typo3_documentation_lookup`: a maintainer's tracker
  comment quoted as a rule, repeated as correct in the session's first
  assessment, and disproved by the checkout only because the user asked what it
  made of the statement.
- The corpus carried the same over-strong reading. `fluid-resource-uris` in
  `knowledge/hints/fluid.json` read "f:image and f:uri.image are not on it —
  they resolve through FAL and the Extbase ImageService, and **their** own class
  documentation sends an extension resource to f:uri.resource instead". Read in
  `.checkouts/` on 12.4, 13.4, 14.3 and `main` alike, that is true of one of the
  two:
  - `Uri/ImageViewHelper` — "This ViewHelper should only be used for images
    within FAL storages, or where graphical operations shall be performed", and
    "For extension resource files, use `<f:uri.resource>` instead".
  - `ImageViewHelper` — neither sentence, and its own first example is
    `<f:image src="EXT:myext/Resources/Public/typo3_logo.png" width="100c" />`,
    on all four.
- What both docblocks do say is weaker than a prohibition and is about
  stability: image operations on non-FAL files "may be changed in future TYPO3
  versions", because each creates a "fake" FAL record.
- The form is covered by the core's own suite.
  `typo3/sysext/fluid/Tests/Functional/ViewHelpers/SvgImageViewHelperTest.php`
  renders `<f:image src="EXT:svg_image_test/…">` in six data-provider cases —
  width, height, `crop`, `fileExtension="png"` — on 13.4, 14.3 and `main`; the
  file does not exist on 12.4.
- The route behind it is already stated correctly one file away.
  `fal-storages-drivers` in `knowledge/hints/fal.json` says uid 0 is the
  fallback, that `ResourceFactory::retrieveFileOrFolderObject()` still resolves
  an `EXT:` path through it, and that the source marks the route for removal —
  `@todo` on `ResourceFactory` lines 195 and 212 of `main`. So the *reason* for
  the discouragement was here and the *strength* of it was not.
- Nothing reached the session's question.
  `bin/cli hints:probe "must not use f:image for anything but FAL resources"`
  matched no hint, 23 returned as the index; the feedback's own `Query` line
  reached `fluid-viewhelpers`, `system-extension-boundaries`, `core-tests` and
  `fal-basics`, and neither of the two hints that bear on it.
- The instructions already draw the line this feedback found, and draw it around
  three artifacts. `knowledge/server-scope.json` sends the caller to the
  checkout for "what changed, which branch you are on, and whether a path still
  exists"; `skills/base.md` extends it to identifiers — "Verify each identifier
  that comes back in the checkout". A behavioural rule is on neither list.

## Decided

- The corpus states the strength and the source when it states such a rule, and
  the two image ViewHelpers are stated apart. `fluid-resource-uris` loses the
  flattened clause and gains a version-independent statement: an `EXT:` path in
  `f:image` works and is tested, `f:uri.image`'s docblock is what carries the
  FAL-only sentence, the shared warning is about stability rather than support,
  and the fallback-storage route marked for removal is what the discouragement
  is about.
- It is written as a statement about TYPO3, not as a rule about how a session
  reads a tracker — the boundary
  [`D-FBK-024`](../feedback/fbk-024-a-feedback-about-the-callers-conduct-toward-its-user-names-no-surface.md)
  draws. What is claimed is what the code, the tests and the docblocks say, and
  the reader draws the conclusion about the comment.
- The statement names what the over-strong version gets wrong, rather than
  quietly stating the right thing beside it —
  [`D-KNW-011`](knw-011-a-rule-that-names-a-defect-names-its-correction.md).
  A session arrives at this hint holding the "must not", and a hint that does
  not contradict it leaves it standing.
- `appliesTo` gains `f:image`, `extension resource` and
  `EXT: path in a template`, because the question is asked in the words of the
  rule rather than in the words of the API — `R-KNW-002`. The probe that matched
  nothing now reaches `fluid-resource-uris` first.
- Not decided here: the feedback's third suggestion, that guidance tell a
  session a rule quoted from a tracker or from prose docs is a claim to verify
  against the checkout. That orders a task rather than stating a fact, so it is
  a step of the core-contribution creation order
  ([`D-SKL-005`](../task-skills/skl-005-core-contribution-earns-a-skill-and-the-domain-is-the-work.md)),
  whose assessment half already comes from this cluster. The feedback stays open
  behind it, trimmed to that half, rather than gaining a card that would carry a
  quarter of an order somebody else is writing.
- Nothing on another branch was touched.

## Assumed

- That the flattening is what carried the strength, rather than the hint being
  merely silent. Nothing measured a session reading the corrected sentence; what
  is known is that the wrong clause and the reported mistake say the same thing.
- That the statement holds on 12.4 as written. The docblock example and the
  fallback route are there, the functional test is not, so "the core covers that
  form" is read off 13.4 and later. The statement names the test rather than a
  version, which is what keeps it honest if the file moves again.
- That `f:uri.resource` is the right thing to send a template to for a resource
  that needs no graphical operation. That is `f:uri.image`'s own docblock and
  the System Resource API's shape, not a preference formed here.

## Wrong if

- A session reads the statement and still reports "must not" as the rule. The
  contradiction was then not the missing part, and what is left to suspect is
  that a tracker comment outweighs a hint whatever the hint says — which is the
  skill step this entry declined to write.
- The `EXT:` branch of `ResourceFactory::retrieveFileOrFolderObject()` is
  removed, or `SvgImageViewHelperTest` stops covering `EXT:` sources. The
  statement then describes a version boundary and needs a `since`/`until` rather
  than being version-independent, and the two `@todo` markers say this is the
  likelier of the failures.
- `ImageViewHelper` gains the sentence `Uri/ImageViewHelper` carries. The
  flattening would have been early rather than wrong, and the statement becomes
  one about a boundary that closed.
- Another rule of this shape is found stated at full strength in the corpus with
  no source on it. One correction is then a fix and not a rule, and what is
  needed is a check over the corpus rather than a sentence in one hint.

## Covered by

- `HintsTest::aFluidResourceUriTaskIsAnsweredWithWhoAppliesCacheBusting`

## Since then

The half deferred above landed on 2026-08-03. "Verify in the checkout every rule
the issue quotes" is the fourth step of `typo3-core-patch-development`'s
assessment, before the reproduction, and it names the three strengths this entry
separates. `R-SKL-015` holds it, and `feedback/2026-08-02-144814` is archived on
both halves. What is left here is the first **Wrong if** — a session that reads
the corrected statement and still reports "must not" says the hint was not the
missing part.

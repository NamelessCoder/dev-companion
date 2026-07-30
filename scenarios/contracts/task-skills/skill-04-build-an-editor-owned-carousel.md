# SKILL-04 — Build an editor-owned carousel

**Environment:** `E-SITE`, in the project's site package · **Contract:** `held`
**Held by:**
`SkillTest::extensionSkillsRouteThroughTheirPrimaryEvidenceSourcesInOrder`,
`HintsTest::aRepeatableContentElementIsRoutedThroughWhatItOwns`,
`SkillTest::judgmentHeavySkillsKeepTheirChecklistBesideThem`

> Add a hero carousel content element whose slides editors can create, order,
> translate and hide directly inside the element. Keep its implementation
> maintainable and test the behavior that matters.

**What has to come out of it**

- The editor workflow and ownership model are decided before fields or
  rendering; owned repeatable slides use dedicated inline child records.
- Element-specific TCA and TypoScript are split into named files rather than
  added to generic files.
- Element-only CSS and JavaScript are loaded through the Fluid AssetCollector.
- Labels, localization, workspaces, sorting and visibility are handled for the
  full parent/child lifecycle.
- Functional tests cover persistence and rendering order; browser behavior is
  tested or explicitly reported as unverified.

**How it fails**

- Reusing arbitrary `tt_content` records without an explicit reuse requirement.
- Global page assets for code only the carousel needs.
- A generic TCA or TypoScript file accumulating the whole element.
- A unit test of mocks presented as proof that inline persistence or frontend
  interaction works.

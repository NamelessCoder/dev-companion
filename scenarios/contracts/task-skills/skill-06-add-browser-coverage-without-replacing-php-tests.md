# SKILL-06 — Add browser coverage without replacing PHP tests

**Environment:** `E-SITE`, in a project with working PHPUnit tests but no
browser runner · **Contract:** `held`
**Held by:** `SkillTest::extensionTestingLoadsOnlyTheSelectedLayerGuide`,
`HintsTest::theTestKindThatNeedsABrowserIsCovered`,
`HintsTest::theBrowserLayerIsReachedByAPromptThatNamesOnlyTheOutcome`

> A frontend regression reached production even though our PHP tests passed.
> Add browser coverage for the important page, its form, and backend login. Keep
> the PHP suite, and make the browser setup work locally and in CI.

**What has to come out of it**

- The answer verifies the existing PHP harness and keeps it; browser coverage is
  added because JavaScript, form interaction and login cannot be proved there.
- Playwright belongs to the runnable project, uses real mounted URLs and derives
  its base URL, authentication and browser execution location from the project.
- Stable project scripts exist before CI calls the same commands, and at least
  one real spec plus its report or trace runs locally.
- Credentials, developer hosts and temporary authentication state are not
  committed. Snapshot updates, if used, require an understood visual change.

**How it fails**

- Working PHP tests are replaced or described as useless.
- Browser packages or URLs are imposed from a generic DDEV recipe without
  inspecting how this project runs.
- Configuration and CI are added, but no served page is exercised.
- A screenshot-only assertion is presented as proof that the form submitted or
  login succeeded.

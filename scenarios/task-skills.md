# Installed task skills

These scenarios exercise the task skills published with the server. Run them in
the Printworks TYPO3 site project without mentioning a skill, tool, checklist,
or intended answer. See [readme.md](readme.md) for the general procedure.

---

## SKILL-01 — Find the missing tests before adding them

**Environment:** `E-SITE`, in the Printworks sitepackage ·
**Status today:** `covered`

> Review the Printworks sitepackage's test coverage. Tell me which important
> behaviors are not protected, choose the right test layer for each gap, and
> give me the exact commands we should run. Do not change files yet.

**What has to come out of it**

- The answer inspects production code, nearby tests, configuration, and declared
  project commands before recommending work.
- Each proposed test names a concrete unprotected behavior and explains why
  unit, functional, browser, or architecture coverage is the lowest useful
  layer.
- Commands exist in the checkout or are declared by the project; no TYPO3 core
  `runTests.sh` suite is offered.
- Missing infrastructure and commands not executed are clearly marked.

**How it fails**

- A generic testing pyramid without file-level evidence.
- Invented PHPUnit paths, Composer scripts, or core-only commands.
- Treating a file-presence assertion as proof of runtime behavior.

---

## SKILL-02 — Audit the sitepackage for TYPO3 conformance

**Environment:** `E-SITE`, in the Printworks sitepackage ·
**Status today:** `covered`

> Audit the Printworks sitepackage for TYPO3 conformance and upgrade readiness.
> Prioritize real findings with evidence and severity. This is a review only;
> do not fix anything.

**What has to come out of it**

- The audit covers only subsystems evidenced by the checkout and active
  installation, including output-context escaping and security boundaries.
- Every finding names a concrete location, evidence, consequence, severity,
  remediation, and relevant project check.
- Parser gaps for dynamic PHP registration are not reported as absent features;
  checkout evidence wins for what the package implements.
- The answer stops after findings and distinguishes recommendations and missing
  evidence from verified violations.

**How it fails**

- Severity based on style preference or file count rather than consequence.
- Optional subsystems reported as missing defects.
- The review silently starts changing tests, documentation, or backend code.

---

## SKILL-03 — Make the documentation match the package

**Environment:** `E-SITE`, in the Printworks sitepackage ·
**Status today:** `covered`

> Review and improve the Printworks sitepackage documentation for integrators
> and editors. Make it match what the package really implements, remove unsafe
> example data, and stop once the requested workflows are verifiable.

**What has to come out of it**

- Integrator and editor material are separated and placed in the repository's
  existing README/manual structure.
- Keys, defaults, registrations, paths, and examples come from the checkout;
  external TYPO3 claims use versioned official evidence.
- Credentials, personal data, internal hosts, and machine-specific paths are
  removed or replaced with named placeholders.
- A dynamic-registration parser miss is described as a tooling limitation, not
  as proof that the feature does not exist.
- The work stops at the completion gate and reports validations and remaining
  unknowns rather than continuing an unbounded survey.

**How it fails**

- Copying local passwords or personal addresses into polished examples.
- Replacing checkout truth with incomplete parsed installation output.
- Creating a competing documentation structure or promising internal details as
  stable API.

---

## SKILL-04 — Build an editor-owned carousel

**Environment:** `E-SITE`, in the Printworks sitepackage ·
**Status today:** `covered`

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

---

## SKILL-05 — Add coverage through the harness the behavior needs

**Environment:** `E-EXT`, in an extension that has unit tests but no working
functional suite · **Status today:** `covered`

> Our extension already has some unit tests. Add coverage for the repository's
> persistence behavior and make whatever test setup that requires work locally
> and in CI. Preserve the tests and commands we already have.

**What has to come out of it**

- The answer inspects and runs or diagnoses the existing unit harness before
  changing test infrastructure.
- Persistence is assigned to a functional test. The missing functional harness
  is established from the package constraints, installed testing framework and
  versioned documentation rather than guessed or replaced with mocks.
- Existing unit configuration and commands survive; one stable local functional
  command passes before CI calls the same command.
- Database prerequisites are derived from the declared environment, credentials
  stay untracked, and an infrastructure failure is not reported as a failed
  persistence assertion.
- The new test exercises observable repository behavior with deterministic
  fixtures. No core `runTests.sh` command or vacuous assertion appears.

**How it fails**

- The repository receives another unit test because that runner already exists.
- Setup becomes a competing test tree or replaces working scripts.
- A CI matrix is copied from another extension without resolving this package's
  supported constraints.
- Configuration files exist, but no local functional test is actually run.

---

## SKILL-06 — Add browser coverage without replacing PHP tests

**Environment:** `E-SITE`, in a project with working PHPUnit tests but no
browser runner · **Status today:** `covered`

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

---

## SKILL-07 — Build a backend module, then document the extension

**Environment:** `E-SITE`, in the Printworks sitepackage ·
**Status today:** `gap` — `R-SKL-3`

> Add a backend module for reviewing imported records, including the module
> shell, status list and refresh action. Once it works, document the public
> workflow for the extension's maintainers and editors in the right place.

**What has to come out of it**

- The backend-module workflow establishes project and extension scope and owns
  the implementation through its verification.
- Once implementation is verified, that workflow stops and the documentation
  workflow activates before documentation files are edited.
- The verified extension key, target TYPO3 version and public behavior cross the
  boundary; implementation detail that is not public does not.
- Documentation is updated in the sitepackage's existing README/manual
  structure, not in the project-level README around it.

**How it fails**

- The backend-module skill remains the only active skill and edits
  documentation itself.
- Both workflows run as one undifferentiated checklist with no observable
  ownership boundary.
- Extension functionality is documented at project level or internal controller
  details are presented as stable public behavior.

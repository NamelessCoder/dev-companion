# `typo3_task_guide`

Build a task checklist enriched with matching hints and relevant core checks.
Built from bundled conventions only: it does not read your checkout, so it also
names what you have to establish there yourself, routes to the lookups that fit
the task, and names the task skill that owns the work where a published one
does. Work that reads as a project or third-party extension is answered with
what transfers only — the core checks, checklist items and steps that name
something only the core repository has are left out rather than handed over.
Answers from: knowledge.

`readOnlyHint: true` · `destructiveHint: false` · `idempotentHint: true` · `openWorldHint: false`

Answers from [`knowledge`](answer-sources.md#knowledge).

## Takes

```yaml
# Short description of the TYPO3 core task, in English.
task: string
# The files the task is about, as they are in the repository they belong to.
# Pass them where the work touches more than one place: each is placed on its
# own, so a core path and an extension path in one call are not answered with
# one verdict. An extension key counts as a path. A subsystem no path can be
# named for belongs in task, because every entry here is answered as a file.
paths: [string]  # optional
# The TYPO3 version this task is for, for example "13.4" or "14". Conventions
# that do not hold there are left out, including those the repository needs for
# another major it declares. Defaults to every major this repository declares
# typo3/cms-core for, or to the installation this server was started in where
# there is no declaration.
targetVersion: string  # optional
# One of: bugfix, feature, cleanup, test, documentation, deprecation, audit,
# operations, unknown. What kind of change the task is. Two of them write no
# file and get a brief of their own instead of the steps a patch owes: audit
# asks for what a review needs, and operations for what running an installation
# needs — booting the environment a repository declares, importing its data,
# building its assets. A task that describes either gets that shape without
# stating the type.
changeType: string  # optional
```

## Answers with

```yaml
task: string
# The paths this brief was composed for. Empty where the call named none.
paths: [string]  # optional
# Which kind of work each path is. Where every path is outside the core, the
# core checks, the core-only checklist items and the submission route are left
# out of the whole brief.
scopes:  # optional
  - path: string
    # One of: core, uncertain, project, extension. Which kind of work this
    # answer is for: core, a patch to the TYPO3 core itself; project, the site
    # repository around an installation; extension, a package in it, whether a
    # sitepackage or a third-party one; or uncertain, which means nothing in the
    # call placed the work and what came back is the core's own.
    scope: string
changeType: string
# The TYPO3 major this repository runs — stated by the caller, or read from
# the installation. Null means nothing was filtered by version. Where the
# repository serves several majors, targetVersions is what the answer holds for.
targetVersion: integer or null  # optional
# Every TYPO3 major the answer holds for. One entry is the ordinary case.
# Several mean this repository declares typo3/cms-core for more than one of
# them, so a statement was kept when it holds on any — and where two
# statements about the same subject differ, the difference is the constraint the
# code lives under rather than drift. Empty when nothing was filtered by
# version.
targetVersions: [integer]  # optional
domains: [string]
# One of: core, uncertain, project, extension. Which kind of work the call as a
# whole reads as. Anything but core means the answer holds core conventions that
# may transfer, not a checklist for the task. Where the paths disagree, scopes
# is the answer and this is what the task text alone says.
scope: string  # optional
# The kinds of core work recognized in the task text.
intents:  # optional
  - id: string
    title: string
    # One of: strong, weak. weak: a word named the subject without naming the
    # work, or the intent is a core-only one and nothing in the task says this
    # is core work. Either way it applies only under its condition.
    confidence: string
    # When a weakly matched intent applies. Empty for a strong match.
    condition: string
# The task skills that own the recognized work, named so that a caller who
# reached this server without one can load it. A skill is a file in your own
# project rather than something this server can see, so a name here is not a
# promise that it is installed. Empty means no published skill owns what was
# recognized, which is not a statement that the work has no workflow.
skills: [string]
# What typo3_hint_lookup answers for these paths, quoted whole and carried here
# — the strongest few per group of paths, not everything it holds on them. A
# rule taken from one of these belongs to that lookup, so a report citing it
# names typo3_hint_lookup and a caller who needs more of the subject calls it
# directly. What was left is named in omittedHints.
hints:
  - id: string
    title: string
    # PHP, TypeScript, JavaScript, CSS, or General.
    category: string
    # One of: core, project, extension, null. Which kind of work the whole hint
    # obliges. "core" means it is a condition of a patch to the TYPO3 core and a
    # convention anywhere else — the backend's own design system, the
    # changelog artifact, the paths of the mono repository. "project" and
    # "extension" are the mirror: what the repository around an installation, or
    # a package distributed on its own, has to do, and what is context rather
    # than a condition inside the core. Null, the ordinary case, means it holds
    # wherever TYPO3 is written: an API that throws throws in a sitepackage too.
    scope: string or null
    hints:
      - # The statement itself. It reads the same on every version it holds for;
        # the range is beside it, never inside it.
        text: string
        # First TYPO3 major this holds on. Null means as far back as this
        # knowledge base reaches.
        since: integer or null
        # Last TYPO3 major this holds on. Null means it still holds.
        until: integer or null
        # The same range as a sentence, empty when the statement is bound to
        # nothing.
        versions: string
        # One of: core, project, extension, null. Which kind of work this
        # statement obliges. "core" means it is a condition of a patch to the
        # TYPO3 core and a convention anywhere else — the backend's own design
        # system, the changelog artifact, the paths of the mono repository.
        # "project" and "extension" are the mirror: what the repository around
        # an installation, or a package distributed on its own, has to do, and
        # what is context rather than a condition inside the core. Null, the
        # ordinary case, means it holds wherever TYPO3 is written: an API that
        # throws throws in a sitepackage too.
        scope: string or null
# What typo3_hint_lookup also holds for these paths and this brief did not
# carry, named rather than counted. Empty means what it carries is everything
# that matched. A subject listed here and not in hints is one the brief did not
# reach, so it is the gap the pointer to that lookup stands for.
omittedHints:
  - # Ask for this hint outright by passing it as id.
    id: string
    title: string
    # PHP, TypeScript, JavaScript, CSS, or General.
    category: string
# Rule sections that apply to this task.
rules:  # optional
  - documentId: string
    # Title of the knowledge document.
    title: string
    # typo3://guides resource holding the full document.
    uri: string
    # Heading of the matched section.
    heading: string
    # The section as written, formatting included.
    body: string
    # The TYPO3 majors this section holds for, in words. Empty means every
    # covered major, which is what a section that declares nothing says.
    versions: string  # optional
    # Share of the query terms the section covers, 0 to 1.
    coverage: number
    # Weighted match score; headings weigh more than body text.
    score: integer
    # Whether the body was cut; read the resource for the rest.
    truncated: boolean
# Commands to run, ready to execute from the core root.
checks: [string]
# Checks that only apply if the task really is the kind of work a weakly matched
# intent suggests.
conditionalChecks:  # optional
  - title: string
    condition: string
    checks: [string]
testSuites:  # optional
  - suite: string
    # Full command, run from the core root.
    command: string
    # Narrowed form for iterating on a single file or test.
    targeted: string or null
    description: string  # optional
    whenToUse: string  # optional
    domains: [string]  # optional
    # The TYPO3 majors whose runTests.sh has this suite, where that is not all
    # of them. Null means every covered version.
    versions: string or null
checklist: [string]
# What this server cannot see and the agent has to establish itself.
checkoutDiscovery:  # optional
  - establish: string
    how: string
nextTools:
  - tool: string
    when: string
```

## Answered

Derived by `bin/cli tools:index`, and `bin/cli tools:check` holds it — the
same as everything above this heading. This tool reads nothing an installation
contains: what reaches its answer is the bundled knowledge and which TYPO3
major the caller is on, so what comes back is written down rather than recorded
from one machine's checkout. Answered against the core checkout this repository
writes below .fixtures/, declaring TYPO3 14.3.0.

### brief: with a path

Called with:

```json
{
    "task": "Deprecate a public method",
    "paths": [
        "typo3/sysext/core/Classes/Utility/GeneralUtility.php"
    ],
    "changeType": "cleanup"
}
```

Text:

```
Task: Deprecate a public method
Change type: cleanup
Domains: php
Paths:
- typo3/sysext/core/Classes/Utility/GeneralUtility.php
Recognized as: Deprecation, Putting a repository right
Owned by: typo3-core-patch-development. Load it where this project has it installed — the skill carries the working order for this kind of work, and this brief is one call inside it.

Hints:
The hints below are typo3_hint_lookup's, matched for these paths and quoted whole. A finding that cites one of these rules is citing that lookup rather than this guide.
These are everything typo3_hint_lookup matches for these paths, so calling it again by path adds nothing; a subject it holds under another path or id is still a call away.

### PHP

## System Extension Boundaries
Hints:
- Keep changes inside the owning system extension unless a cross-extension contract really changes.
- Reuse public APIs from other system extensions instead of depending on internal implementation details.
- Check nearby extension-local tests before adding shared behavior.

## Deprecated APIs
Hints:
- Whether an API is deprecated is a property of the branch you work on, not of TYPO3 as a whole. Where you work in an installation the server reads that branch: typo3_project_describe names the TYPO3 version installed there, and typo3_changelog_lookup answers from the changelog its core package ships.
- Read the declaration itself: an @deprecated annotation together with a trigger_error(..., E_USER_DEPRECATED) call is what marks one. The core marks nothing with PHP's #[\Deprecated] attribute, so finding none says nothing about what is deprecated.
- The trigger does not have to sit in the declaring body, so a file without one settles nothing. A method, a property or a class can raise from its caller, from the __get() and __call() of PublicPropertyDeprecationTrait and PublicMethodDeprecationTrait where the member was made protected and listed there, or from whatever resolves the class.
- A class constant and an enum case are where the docblock stands alone: nothing runs when one is read, so no trigger_error can be attached to it anywhere. Such a deprecation raises nothing — no deprecation log entry, nothing for a test suite running with failOnDeprecation — and the call site turns into a fatal error in the major that removes it. What finds it is the extension scanner, through the ClassConstantMatcher entry the deprecating patch owes it, rather than anything at runtime.
- What a branch deprecated is recorded in typo3/sysext/core/Documentation/Changelog/<version>/Deprecation-<issue>-<Title>.rst and in the matchers below typo3/sysext/install/Configuration/ExtensionScanner/Php/. Take the migration path from there instead of assuming a replacement.
- An entry's Impact section is prose and can promise a deprecation warning the code does not raise. What is raised is a property of the declaration, so read that for the severity rather than the entry.
- A deprecated API keeps working until the next major release, so an existing call site is not automatically a defect. New code uses the replacement the changelog names.
- Authoring a deprecation and finding out what a version deprecated are two directions through the same files. From the reading side: the changelog directory and the extension scanner matchers ship with the core and install packages of any installation, the Extension Scanner in the Install Tool runs the matchers over an extension, and `typo3 upgrade:list` and `typo3 upgrade:run` are the console side of the migrations.
- @internal on a class or on a member says it is not public API, and both are read: a public class can carry an internal method. It is an input to whether a removal is breaking and never the answer on its own.
- What settles it is whether anything outside the core calls it. The core has removed @internal members both as a breaking change and as an Important note, and what separates the two is the call sites rather than the marker: a Breaking entry names them in its Affected installations section, and the extension scanner matchers are where they are looked for. Writing that section is the test of whether there is one. Where there is none, the removal is an ordinary [TASK] with no marker, no changelog entry and no matcher.
- An absent annotation is not a statement that something is public API. Read the changelog for the subsystem and the extension scanner matchers before concluding either way.

## Events and Extension Points
Hints:
- A listener is registered with the #[AsEventListener] attribute from TYPO3\CMS\Core\Attribute, on the class or on a single method. Its arguments are identifier, event, method, before and after; the attribute is repeatable, so one class can listen to several events. Autoconfiguration picks it up — do not add an event.listener tag to Services.yaml, no core listener is registered that way. [TYPO3 v13 and newer]
- Event classes live in Classes/Event/ of the extension that dispatches them, are final, and are readonly where the payload is immutable. A listener that may change the outcome gets setters on the event instead of a return value.
- Keep event payloads minimal and stable, and prefer a new event over a hook: a hook is only the right answer where the subsystem still has hook-based extension points.
- The surviving hooks are a subsystem fact, not a second extension-point registry. Ask the subsystem hint with the intent — for example prefilling a form field — so it can name both the remaining hook and the narrower event; the form-framework hint records EXT:form's two remaining SC_OPTIONS calls.
- A PSR-14 event is public API. A new one needs a changelog entry, careful naming, and regression coverage.

Rules that apply to this task:

A section carries the range it holds for where it has one. What is bound elsewhere: call typo3_hint_lookup with targetVersion for a convention, and typo3_test_run_guide with targetVersion for a runTests.sh command.

## Deprecations
Source: TYPO3 Core Commit Message Rules (typo3://guides/core/contribution/commit-messages) — matches 100% of the query terms

- Deprecations must not use `[!!!]`.
- Deprecations may only use `[TASK]` or `[FEATURE]`.
- Deprecations must be documented with a changelog RST file.
- Deprecations need migration guidance and may need extension scanner
  considerations.
- All of the above is the authoring side. Reading it — what a given version
  deprecated, and what that means for code that uses it — works the other way
  round: the changelog files below `Documentation/Changelog/` of the core
  package and the matchers below the install package's
  `Configuration/ExtensionScanner/Php/` are what an installation is checked
  against, by the Extension Scanner in the Install Tool. Both directories ship
  with a Composer installation.

## Breaking Changes
Source: TYPO3 Core Commit Message Rules (typo3://guides/core/contribution/commit-messages) — matches 100% of the query terms

- Breaking changes must use `[!!!]` before the keyword.
- Breaking changes must be documented with a changelog RST file.
- Breaking changes should usually target `main`.
- A removed or narrowed PHP API gets an extension scanner matcher entry in the
  same patch, below `typo3/sysext/install/Configuration/ExtensionScanner/Php/`.
  How the removed member is written where it is used decides the file:
  - `MethodCallMatcher.php` — an instance method.
  - `MethodCallStaticMatcher.php` — a static method.
  - `PropertyPublicMatcher.php` — a removed public property.
  - `PropertyProtectedMatcher.php` — a public property that became protected.
  - `ClassNameMatcher.php` — a whole class or interface.
- Visibility routes a property and never a method. The method matchers are a
  weak match on the method name where it is used, and they do not resolve the
  class, so they cannot see one. A method that is protected, or that has become
  protected, is entered where a public one is.
  `RendererRegistry->getRendererInstances` went from public to protected in
  `Breaking-110277`, and it stands in `MethodCallMatcher.php`. The list above
  has no row for a protected method because none is needed, and that absence
  says nothing about whether an entry is owed.
- An entry is keyed by the fully qualified name with `->` or `::` and carries
  `restFiles`, naming the changelog file that removed it. The method matchers
  add `numberOfMandatoryArguments` and `maximumNumberOfArguments`. A member
  deprecated before it was removed lists both changelog files.
- Every Breaking and Deprecation entry carries exactly one of `NotScanned`,
  `PartiallyScanned` and `FullyScanned` in its `.. index::` line, and that tag
  is the claim those entries have to back: `FullyScanned` says every item the
  changelog entry names can be found. The scanner reads PHP, so what an entry
  changes in TypoScript, TCA, YAML or JavaScript is what leaves it partially
  scanned.
- `./Build/Scripts/runTests.sh -s checkExtensionScannerRst` checks that the
  changelog files the matchers name exist, and nothing checks the other
  direction. A missing entry surfaces when somebody audits the matcher files
  against the changelog.

Each excerpt above is one section of a longer document. Where the task is the whole procedure rather than the fact you searched for, read the page: TYPO3 Core Commit Message Rules (typo3://guides/core/contribution/commit-messages). A client may render no resource list, so that address is how one is reached.

Relevant TYPO3 core checks:
- `CI=true ./Build/Scripts/runTests.sh -s unit`
- `CI=true ./Build/Scripts/runTests.sh -s functional`
- `CI=true ./Build/Scripts/runTests.sh -s checkRst`
- `CI=true ./Build/Scripts/runTests.sh -s checkExtensionScannerRst`
## unit
`CI=true ./Build/Scripts/runTests.sh -s unit`
Targeted: `CI=true ./Build/Scripts/runTests.sh -s unit -- --filter <methodName> <path/to/Test.php>`
Use for isolated PHP behavior, utility classes, value objects, and narrow bug fixes.
## functional
`CI=true ./Build/Scripts/runTests.sh -s functional`
Targeted: `CI=true ./Build/Scripts/runTests.sh -s functional -d sqlite -- <path/to/Test.php>`
Use for TYPO3 services, persistence, configuration, authentication, routing, and integration behavior.
## cgl
`CI=true ./Build/Scripts/runTests.sh -s cgl`
Targeted: `CI=true ./Build/Scripts/runTests.sh -s cgl -n`
Use before review when PHP formatting or file headers may be affected. Add `-n` to only report.
## cglGit
`CI=true ./Build/Scripts/runTests.sh -s cglGit`
Targeted: `CI=true ./Build/Scripts/runTests.sh -s cgl -n`
Use for a focused pre-review check after creating a commit, from a normal checkout only. Its file list comes from git inside the container, and a git worktree keeps its gitdir outside the mounted directory: git fails, the list is empty, and the suite reports SUCCESS having read nothing. Use `cgl -n` where the checkout may be a worktree — it asks git nothing.

Suggested checklist:
- Content changes, so what is delivered has to be the version that is current after the change — that is what the editor and the visitor are owed. A defect is judged by that outcome: the old version still being served is the defect, and the error it eventually throws is the symptom.
- Confirm the target TYPO3 core branch and issue context.
- Inspect nearby code, tests, and established subsystem conventions.
- Keep the patch focused on the stated task.
- Add or update the narrowest useful test coverage.
- Run targeted tests first; broaden to CGL, functional, or npm checks when relevant.
- Keep the cleanup mechanical; avoid mixing behavioural changes into the same patch.
- Annotate the member @deprecated since TYPO3 v<the version this lands in>, will be removed in TYPO3 v<the next major>. and close that line with the migration sentence. Read both versions off the branch you are on rather than off an example.
- Keep the member working and open its body with trigger_error('<Class>-><member>() is deprecated since TYPO3 v<the version this lands in> and will be removed in TYPO3 v<the next major>. <migration>', E_USER_DEPRECATED). The docblock and the message say the same three things, and the removal is a patch of its own in that major.
- Migrate every caller of the deprecated API in the same patch, and confirm none is left behind.
- Add a changelog file below typo3/sysext/core/Documentation/Changelog/<the minor this is released in>/ named Deprecation-<issue>-<UpperCamelCaseDescription>.rst. Nothing has to include it: that directory's Index.rst pulls Deprecation-* in by glob, so the filename is the inclusion.
- Open the changelog file with .. include:: /Includes.rst.txt and a unique .. _deprecation-<issue>-<unix timestamp>: anchor directly above the 'Deprecation: #<issue> - <title>' headline, with See :issue:`<issue>` under it. Then Description, Impact, Affected installations and Migration — the last two are what this type owes over a feature entry.
- End the changelog file with .. index:: carrying at least one subject tag and exactly one of FullyScanned, PartiallyScanned or NotScanned. Build/Scripts/validateRstFiles.php rejects a Deprecation file without the scanner tag, so it is owed rather than considered.
- Back a FullyScanned or PartiallyScanned tag with an extension scanner matcher: an entry below typo3/sysext/install/Configuration/ExtensionScanner/Php/, keyed by the deprecated symbol and naming the changelog file in its restFiles. NotScanned is for what no matcher can find, not for what nobody wrote.
- Use [TASK] or [FEATURE] as the commit keyword. A deprecation must never use the [!!!] breaking prefix.
- Run the audit before writing the list, and let it own the findings: what a surface is, what evidence a finding rests on, what it is worth and who fixes it are the conformance workflow's answers. A list built from a reading of the checkout instead is an impression, and the items in it are not the ones the report would have given.
- Show the list whole and let the maintainer cut items, reorder them or stop, before a single file is changed. That agreement is the one step nothing downstream recovers, and a list arriving with the changes it produced is one nobody had the chance to disagree with.
- Keep the list in the reply rather than committing it into the repository. A worklist committed into somebody's history is a file nobody asked for that has to be taken out again; what the history keeps is the commits the items produced, each saying which item it closed.
- Work an item in the workflow that owns it, and stop before editing files another owner has. An item no workflow owns is worked here only where the project's own suite, linter or static analysis proves the change — anything else goes back unassigned, because a finding nobody owns and no check covers is a hole in the workflow map and quietly filling it hides the hole.
- Hand the worked list back for the re-check rather than grading it. A cleanup that declares its own findings gone has no evidence for it, and the audit kept that responsibility when it handed each finding over.
- Report the items still open, the ones dropped with what dropped them, and the ones sent back unassigned. A finished list and an abandoned one read alike in a summary.
- Write the commit message with typo3_commit_message_guide and workflow="core": summarize the changed behavior, the affected area and the commands you ran, and it hands back a draft that carries the keyword, the trailers and the wrapping.

Establish in your checkout — this server cannot see it:
- Which files the task actually touches
  git status --short and git diff --name-only in the core checkout, then call typo3_hint_lookup with those paths for the conventions that apply to them.
- Which tests already cover them
  Core tests mirror the class path below typo3/sysext/<ext>/Tests/Unit/ and Tests/Functional/. Find the file there, then ask typo3_test_run_guide for the targeted runTests.sh invocation.
- The branch you are on and the branches the change is meant for
  git branch --show-current. In the normal case the patch targets main and the merging core team member handles the backport; push to a release branch only when the bug does not exist on main.
- Whether the paths, classes, labels, and identifiers named in an answer still exist on that branch
  Call typo3_component_lookup for curated backend components: it reads the active installation when the target matches. For uncatalogued code or another target branch, grep the checkout; typo3_catalog_scope names the fallback revision.
- Whether an icon identifier is registered, and which one spells the shape you want
  Ask typo3_icon_lookup: it reads the registry of the installation this server was started in, the T3Icons set and every installed package included. Where there is no reachable installation, the same three places can be read by hand — typo3/sysext/core/Resources/Public/Icons/T3Icons/icons.json, the Configuration/Icons.php of each package, and typo3/sysext/core/Resources/Public/Icons/Flags/ for the flags-* family.
- Whether a label for this wording already exists
  Identify the XLF resource already used at the consuming code, then ask typo3_label_lookup with that resource. It applies the installation's resource overrides, but a match from another module or package is not reusable in this context. Where the console cannot be reached it reads the installed package's XLF file instead and says so; only where there is no installation at all is there nothing to ask.

Next lookups for this task:
- typo3_commit_message_guide — with workflow="core" and isDeprecation=true, to get the keyword and prefix rules checked
- typo3_project_describe — for what the repository is before anything in it is changed
- typo3_extension_describe — for what each extension in scope registers
- typo3_changelog_lookup — for what 14 changed about this area — the first stop when you have not built on it recently, not only a lookup after the fact
- typo3_hint_lookup — with the concrete file paths, once they are known
- typo3_test_run_guide — for the targeted runTests.sh invocation
- typo3_feedback_record — when one of these answers was wrong or incomplete
```

Data:

```json
{
    "task": "Deprecate a public method",
    "paths": [
        "typo3/sysext/core/Classes/Utility/GeneralUtility.php"
    ],
    "scopes": [
        {
            "path": "typo3/sysext/core/Classes/Utility/GeneralUtility.php",
            "scope": "core"
        }
    ],
    "changeType": "cleanup",
    "targetVersion": 14,
    "targetVersions": [
        14
    ],
    "domains": [
        "php"
    ],
    "scope": "core",
    "intents": [
        {
            "id": "deprecation",
            "title": "Deprecation",
            "confidence": "strong",
            "condition": ""
        },
        {
            "id": "cleanup",
            "title": "Putting a repository right",
            "confidence": "strong",
            "condition": "only if the task asks for the repository as a whole to be changed rather than reviewed, or for the findings of a review to be worked off"
        }
    ],
    "skills": [
        "typo3-core-patch-development"
    ],
    "hints": [
        {
            "id": "system-extension-boundaries",
            "title": "System Extension Boundaries",
            "category": "PHP",
            "scope": null,
            "hints": [
                {
                    "text": "Keep changes inside the owning system extension unless a cross-extension contract really changes.",
                    "since": null,
                    "until": null,
                    "versions": "",
                    "scope": null
                },
                {
                    "text": "Reuse public APIs from other system extensions instead of depending on internal implementation details.",
                    "since": null,
                    "until": null,
                    "versions": "",
                    "scope": null
                },
                {
                    "text": "Check nearby extension-local tests before adding shared behavior.",
                    "since": null,
                    "until": null,
                    "versions": "",
                    "scope": null
                }
            ]
        },
        {
            "id": "deprecated-apis",
            "title": "Deprecated APIs",
            "category": "PHP",
            "scope": null,
            "hints": [
                {
                    "text": "Whether an API is deprecated is a property of the branch you work on, not of TYPO3 as a whole. Where you work in an installation the server reads that branch: typo3_project_describe names the TYPO3 version installed there, and typo3_changelog_lookup answers from the changelog its core package ships.",
                    "since": null,
                    "until": null,
                    "versions": "",
                    "scope": null
                },
                {
                    "text": "Read the declaration itself: an @deprecated annotation together with a trigger_error(..., E_USER_DEPRECATED) call is what marks one. The core marks nothing with PHP's #[\\Deprecated] attribute, so finding none says nothing about what is deprecated.",
                    "since": null,
                    "until": null,
                    "versions": "",
                    "scope": null
                },
                {
                    "text": "The trigger does not have to sit in the declaring body, so a file without one settles nothing. A method, a property or a class can raise from its caller, from the __get() and __call() of PublicPropertyDeprecationTrait and PublicMethodDeprecationTrait where the member was made protected and listed there, or from whatever resolves the class.",
                    "since": null,
                    "until": null,
                    "versions": "",
                    "scope": null
                },
                {
                    "text": "A class constant and an enum case are where the docblock stands alone: nothing runs when one is read, so no trigger_error can be attached to it anywhere. Such a deprecation raises nothing — no deprecation log entry, nothing for a test suite running with failOnDeprecation — and the call site turns into a fatal error in the major that removes it. What finds it is the extension scanner, through the ClassConstantMatcher entry the deprecating patch owes it, rather than anything at runtime.",
                    "since": null,
                    "until": null,
                    "versions": "",
                    "scope": null
                },
                {
                    "text": "What a branch deprecated is recorded in typo3/sysext/core/Documentation/Changelog/<version>/Deprecation-<issue>-<Title>.rst and in the matchers below typo3/sysext/install/Configuration/ExtensionScanner/Php/. Take the migration path from there instead of assuming a replacement.",
                    "since": null,
                    "until": null,
                    "versions": "",
                    "scope": null
                },
                {
                    "text": "An entry's Impact section is prose and can promise a deprecation warning the code does not raise. What is raised is a property of the declaration, so read that for the severity rather than the entry.",
                    "since": null,
                    "until": null,
                    "versions": "",
                    "scope": null
                },
                {
                    "text": "A deprecated API keeps working until the next major release, so an existing call site is not automatically a defect. New code uses the replacement the changelog names.",
                    "since": null,
                    "until": null,
                    "versions": "",
                    "scope": null
                },
                {
                    "text": "Authoring a deprecation and finding out what a version deprecated are two directions through the same files. From the reading side: the changelog directory and the extension scanner matchers ship with the core and install packages of any installation, the Extension Scanner in the Install Tool runs the matchers over an extension, and `typo3 upgrade:list` and `typo3 upgrade:run` are the console side of the migrations.",
                    "since": null,
                    "until": null,
                    "versions": "",
                    "scope": null
                },
                {
                    "text": "@internal on a class or on a member says it is not public API, and both are read: a public class can carry an internal method. It is an input to whether a removal is breaking and never the answer on its own.",
                    "since": null,
                    "until": null,
                    "versions": "",
                    "scope": null
                },
                {
                    "text": "What settles it is whether anything outside the core calls it. The core has removed @internal members both as a breaking change and as an Important note, and what separates the two is the call sites rather than the marker: a Breaking entry names them in its Affected installations section, and the extension scanner matchers are where they are looked for. Writing that section is the test of whether there is one. Where there is none, the removal is an ordinary [TASK] with no marker, no changelog entry and no matcher.",
                    "since": null,
                    "until": null,
                    "versions": "",
                    "scope": null
                },
                {
                    "text": "An absent annotation is not a statement that something is public API. Read the changelog for the subsystem and the extension scanner matchers before concluding either way.",
                    "since": null,
                    "until": null,
                    "versions": "",
                    "scope": null
                }
            ]
        },
        {
            "id": "events-extension-points",
            "title": "Events and Extension Points",
            "category": "PHP",
            "scope": null,
            "hints": [
                {
                    "text": "A listener is registered with the #[AsEventListener] attribute from TYPO3\\CMS\\Core\\Attribute, on the class or on a single method. Its arguments are identifier, event, method, before and after; the attribute is repeatable, so one class can listen to several events. Autoconfiguration picks it up — do not add an event.listener tag to Services.yaml, no core listener is registered that way.",
                    "since": 13,
                    "until": null,
                    "versions": "TYPO3 v13 and newer",
                    "scope": null
                },
                {
                    "text": "Event classes live in Classes/Event/ of the extension that dispatches them, are final, and are readonly where the payload is immutable. A listener that may change the outcome gets setters on the event instead of a return value.",
                    "since": null,
                    "until": null,
                    "versions": "",
                    "scope": null
                },
                {
                    "text": "Keep event payloads minimal and stable, and prefer a new event over a hook: a hook is only the right answer where the subsystem still has hook-based extension points.",
                    "since": null,
                    "until": null,
                    "versions": "",
                    "scope": null
                },
                {
                    "text": "The surviving hooks are a subsystem fact, not a second extension-point registry. Ask the subsystem hint with the intent — for example prefilling a form field — so it can name both the remaining hook and the narrower event; the form-framework hint records EXT:form's two remaining SC_OPTIONS calls.",
                    "since": null,
                    "until": null,
                    "versions": "",
                    "scope": null
                },
                {
                    "text": "A PSR-14 event is public API. A new one needs a changelog entry, careful naming, and regression coverage.",
                    "since": null,
                    "until": null,
                    "versions": "",
                    "scope": null
                }
            ]
        }
    ],
    "omittedHints": [],
    "rules": [
        {
            "documentId": "core/contribution/commit-messages",
            "title": "TYPO3 Core Commit Message Rules",
            "uri": "typo3://guides/core/contribution/commit-messages",
            "heading": "Deprecations",
            "body": "- Deprecations must not use `[!!!]`.\n- Deprecations may only use `[TASK]` or `[FEATURE]`.\n- Deprecations must be documented with a changelog RST file.\n- Deprecations need migration guidance and may need extension scanner\n  considerations.\n- All of the above is the authoring side. Reading it — what a given version\n  deprecated, and what that means for code that uses it — works the other way\n  round: the changelog files below `Documentation/Changelog/` of the core\n  package and the matchers below the install package's\n  `Configuration/ExtensionScanner/Php/` are what an installation is checked\n  against, by the Extension Scanner in the Install Tool. Both directories ship\n  with a Composer installation.",
            "versions": "",
            "coverage": 1,
            "score": 66,
            "truncated": false
        },
        {
            "documentId": "core/contribution/commit-messages",
            "title": "TYPO3 Core Commit Message Rules",
            "uri": "typo3://guides/core/contribution/commit-messages",
            "heading": "Breaking Changes",
            "body": "- Breaking changes must use `[!!!]` before the keyword.\n- Breaking changes must be documented with a changelog RST file.\n- Breaking changes should usually target `main`.\n- A removed or narrowed PHP API gets an extension scanner matcher entry in the\n  same patch, below `typo3/sysext/install/Configuration/ExtensionScanner/Php/`.\n  How the removed member is written where it is used decides the file:\n  - `MethodCallMatcher.php` — an instance method.\n  - `MethodCallStaticMatcher.php` — a static method.\n  - `PropertyPublicMatcher.php` — a removed public property.\n  - `PropertyProtectedMatcher.php` — a public property that became protected.\n  - `ClassNameMatcher.php` — a whole class or interface.\n- Visibility routes a property and never a method. The method matchers are a\n  weak match on the method name where it is used, and they do not resolve the\n  class, so they cannot see one. A method that is protected, or that has become\n  protected, is entered where a public one is.\n  `RendererRegistry->getRendererInstances` went from public to protected in\n  `Breaking-110277`, and it stands in `MethodCallMatcher.php`. The list above\n  has no row for a protected method because none is needed, and that absence\n  says nothing about whether an entry is owed.\n- An entry is keyed by the fully qualified name with `->` or `::` and carries\n  `restFiles`, naming the changelog file that removed it. The method matchers\n  add `numberOfMandatoryArguments` and `maximumNumberOfArguments`. A member\n  deprecated before it was removed lists both changelog files.\n- Every Breaking and Deprecation entry carries exactly one of `NotScanned`,\n  `PartiallyScanned` and `FullyScanned` in its `.. index::` line, and that tag\n  is the claim those entries have to back: `FullyScanned` says every item the\n  changelog entry names can be found. The scanner reads PHP, so what an entry\n  changes in TypoScript, TCA, YAML or JavaScript is what leaves it partially\n  scanned.\n- `./Build/Scripts/runTests.sh -s checkExtensionScannerRst` checks that the\n  changelog files the matchers name exist, and nothing checks the other\n  direction. A missing entry surfaces when somebody audits the matcher files\n  against the changelog.",
            "versions": "",
            "coverage": 1,
            "score": 21,
            "truncated": false
        }
    ],
    "checks": [
        "CI=true ./Build/Scripts/runTests.sh -s unit",
        "CI=true ./Build/Scripts/runTests.sh -s functional",
        "CI=true ./Build/Scripts/runTests.sh -s checkRst",
        "CI=true ./Build/Scripts/runTests.sh -s checkExtensionScannerRst"
    ],
    "conditionalChecks": [],
    "testSuites": [
        {
            "suite": "unit",
            "command": "CI=true ./Build/Scripts/runTests.sh -s unit",
            "targeted": "CI=true ./Build/Scripts/runTests.sh -s unit -- --filter <methodName> <path/to/Test.php>",
            "description": "PHP unit tests.",
            "whenToUse": "Use for isolated PHP behavior, utility classes, value objects, and narrow bug fixes.",
            "domains": [
                "php"
            ],
            "versions": ""
        },
        {
            "suite": "functional",
            "command": "CI=true ./Build/Scripts/runTests.sh -s functional",
            "targeted": "CI=true ./Build/Scripts/runTests.sh -s functional -d sqlite -- <path/to/Test.php>",
            "description": "PHP functional tests, sqlite by default.",
            "whenToUse": "Use for TYPO3 services, persistence, configuration, authentication, routing, and integration behavior.",
            "domains": [
                "php",
                "fluid",
                "typoscript"
            ],
            "versions": ""
        },
        {
            "suite": "cgl",
            "command": "CI=true ./Build/Scripts/runTests.sh -s cgl",
            "targeted": "CI=true ./Build/Scripts/runTests.sh -s cgl -n",
            "description": "Checks and fixes coding guideline issues for all core PHP files.",
            "whenToUse": "Use before review when PHP formatting or file headers may be affected. Add `-n` to only report.",
            "domains": [
                "php"
            ],
            "versions": ""
        },
        {
            "suite": "cglGit",
            "command": "CI=true ./Build/Scripts/runTests.sh -s cglGit",
            "targeted": "CI=true ./Build/Scripts/runTests.sh -s cgl -n",
            "description": "Checks and fixes coding guideline issues in the latest committed patch.",
            "whenToUse": "Use for a focused pre-review check after creating a commit, from a normal checkout only. Its file list comes from git inside the container, and a git worktree keeps its gitdir outside the mounted directory: git fails, the list is empty, and the suite reports SUCCESS having read nothing. Use `cgl -n` where the checkout may be a worktree — it asks git nothing.",
            "domains": [
                "php"
            ],
            "versions": ""
        }
    ],
    "checklist": [
        "Content changes, so what is delivered has to be the version that is current after the change — that is what the editor and the visitor are owed. A defect is judged by that outcome: the old version still being served is the defect, and the error it eventually throws is the symptom.",
        "Confirm the target TYPO3 core branch and issue context.",
        "Inspect nearby code, tests, and established subsystem conventions.",
        "Keep the patch focused on the stated task.",
        "Add or update the narrowest useful test coverage.",
        "Run targeted tests first; broaden to CGL, functional, or npm checks when relevant.",
        "Keep the cleanup mechanical; avoid mixing behavioural changes into the same patch.",
        "Annotate the member @deprecated since TYPO3 v<the version this lands in>, will be removed in TYPO3 v<the next major>. and close that line with the migration sentence. Read both versions off the branch you are on rather than off an example.",
        "Keep the member working and open its body with trigger_error('<Class>-><member>() is deprecated since TYPO3 v<the version this lands in> and will be removed in TYPO3 v<the next major>. <migration>', E_USER_DEPRECATED). The docblock and the message say the same three things, and the removal is a patch of its own in that major.",
        "Migrate every caller of the deprecated API in the same patch, and confirm none is left behind.",
        "Add a changelog file below typo3/sysext/core/Documentation/Changelog/<the minor this is released in>/ named Deprecation-<issue>-<UpperCamelCaseDescription>.rst. Nothing has to include it: that directory's Index.rst pulls Deprecation-* in by glob, so the filename is the inclusion.",
        "Open the changelog file with .. include:: /Includes.rst.txt and a unique .. _deprecation-<issue>-<unix timestamp>: anchor directly above the 'Deprecation: #<issue> - <title>' headline, with See :issue:`<issue>` under it. Then Description, Impact, Affected installations and Migration — the last two are what this type owes over a feature entry.",
        "End the changelog file with .. index:: carrying at least one subject tag and exactly one of FullyScanned, PartiallyScanned or NotScanned. Build/Scripts/validateRstFiles.php rejects a Deprecation file without the scanner tag, so it is owed rather than considered.",
        "Back a FullyScanned or PartiallyScanned tag with an extension scanner matcher: an entry below typo3/sysext/install/Configuration/ExtensionScanner/Php/, keyed by the deprecated symbol and naming the changelog file in its restFiles. NotScanned is for what no matcher can find, not for what nobody wrote.",
        "Use [TASK] or [FEATURE] as the commit keyword. A deprecation must never use the [!!!] breaking prefix.",
        "Run the audit before writing the list, and let it own the findings: what a surface is, what evidence a finding rests on, what it is worth and who fixes it are the conformance workflow's answers. A list built from a reading of the checkout instead is an impression, and the items in it are not the ones the report would have given.",
        "Show the list whole and let the maintainer cut items, reorder them or stop, before a single file is changed. That agreement is the one step nothing downstream recovers, and a list arriving with the changes it produced is one nobody had the chance to disagree with.",
        "Keep the list in the reply rather than committing it into the repository. A worklist committed into somebody's history is a file nobody asked for that has to be taken out again; what the history keeps is the commits the items produced, each saying which item it closed.",
        "Work an item in the workflow that owns it, and stop before editing files another owner has. An item no workflow owns is worked here only where the project's own suite, linter or static analysis proves the change — anything else goes back unassigned, because a finding nobody owns and no check covers is a hole in the workflow map and quietly filling it hides the hole.",
        "Hand the worked list back for the re-check rather than grading it. A cleanup that declares its own findings gone has no evidence for it, and the audit kept that responsibility when it handed each finding over.",
        "Report the items still open, the ones dropped with what dropped them, and the ones sent back unassigned. A finished list and an abandoned one read alike in a summary.",
        "Write the commit message with typo3_commit_message_guide and workflow=\"core\": summarize the changed behavior, the affected area and the commands you ran, and it hands back a draft that carries the keyword, the trailers and the wrapping."
    ],
    "checkoutDiscovery": [
        {
            "establish": "Which files the task actually touches",
            "how": "git status --short and git diff --name-only in the core checkout, then call typo3_hint_lookup with those paths for the conventions that apply to them."
        },
        {
            "establish": "Which tests already cover them",
            "how": "Core tests mirror the class path below typo3/sysext/<ext>/Tests/Unit/ and Tests/Functional/. Find the file there, then ask typo3_test_run_guide for the targeted runTests.sh invocation."
        },
        {
            "establish": "The branch you are on and the branches the change is meant for",
            "how": "git branch --show-current. In the normal case the patch targets main and the merging core team member handles the backport; push to a release branch only when the bug does not exist on main."
        },
        {
            "establish": "Whether the paths, classes, labels, and identifiers named in an answer still exist on that branch",
            "how": "Call typo3_component_lookup for curated backend components: it reads the active installation when the target matches. For uncatalogued code or another target branch, grep the checkout; typo3_catalog_scope names the fallback revision."
        },
        {
            "establish": "Whether an icon identifier is registered, and which one spells the shape you want",
            "how": "Ask typo3_icon_lookup: it reads the registry of the installation this server was started in, the T3Icons set and every installed package included. Where there is no reachable installation, the same three places can be read by hand — typo3/sysext/core/Resources/Public/Icons/T3Icons/icons.json, the Configuration/Icons.php of each package, and typo3/sysext/core/Resources/Public/Icons/Flags/ for the flags-* family."
        },
        {
            "establish": "Whether a label for this wording already exists",
            "how": "Identify the XLF resource already used at the consuming code, then ask typo3_label_lookup with that resource. It applies the installation's resource overrides, but a match from another module or package is not reusable in this context. Where the console cannot be reached it reads the installed package's XLF file instead and says so; only where there is no installation at all is there nothing to ask."
        }
    ],
    "nextTools": [
        {
            "tool": "typo3_commit_message_guide",
            "when": "with workflow=\"core\" and isDeprecation=true, to get the keyword and prefix rules checked"
        },
        {
            "tool": "typo3_project_describe",
            "when": "for what the repository is before anything in it is changed"
        },
        {
            "tool": "typo3_extension_describe",
            "when": "for what each extension in scope registers"
        },
        {
            "tool": "typo3_changelog_lookup",
            "when": "for what 14 changed about this area — the first stop when you have not built on it recently, not only a lookup after the fact"
        },
        {
            "tool": "typo3_hint_lookup",
            "when": "with the concrete file paths, once they are known"
        },
        {
            "tool": "typo3_test_run_guide",
            "when": "for the targeted runTests.sh invocation"
        },
        {
            "tool": "typo3_feedback_record",
            "when": "when one of these answers was wrong or incomplete"
        }
    ]
}
```

### brief: task only

Called with:

```json
{
    "task": "Add a badge to the list module"
}
```

Text:

```
Task: Add a badge to the list module
Change type: unknown
Domains: php
Recognized as: Backend UI markup

Hints:
- No hint matched this task text. That means no convention was recognized, not that none applies: call typo3_hint_lookup again with the concrete file paths once they are known.

Rules that apply to this task:

A section carries the range it holds for where it has one. What is bound elsewhere: call typo3_hint_lookup with targetVersion for a convention, and typo3_test_run_guide with targetVersion for a runTests.sh command.

## Testing
Source: TYPO3 Core Contribution Rules (typo3://guides/core/contribution/rules) — matches 50% of the query terms

- Unit tests are expected for isolated behavior.
- Functional tests are expected for persistence, configuration, routing, backend
  behavior, or integration with TYPO3 services.
- End-to-end tests, the `e2e` suite, are useful when the change affects editor
  or administrator workflows and only breaks in the assembled backend. They
  replaced the former acceptance suites.
- Document tests that could not be executed and why.

Each excerpt above is one section of a longer document. Where the task is the whole procedure rather than the fact you searched for, read the page: TYPO3 Core Contribution Rules (typo3://guides/core/contribution/rules). A client may render no resource list, so that address is how one is reached.

Relevant TYPO3 core checks:
- `CI=true ./Build/Scripts/runTests.sh -s unit`
- `CI=true ./Build/Scripts/runTests.sh -s functional`
- `CI=true ./Build/Scripts/runTests.sh -s lintScss`
- `CI=true ./Build/Scripts/runTests.sh -s build`
## cglGit
`CI=true ./Build/Scripts/runTests.sh -s cglGit`
Targeted: `CI=true ./Build/Scripts/runTests.sh -s cgl -n`
Use for a focused pre-review check after creating a commit, from a normal checkout only. Its file list comes from git inside the container, and a git worktree keeps its gitdir outside the mounted directory: git fails, the list is empty, and the suite reports SUCCESS having read nothing. Use `cgl -n` where the checkout may be a worktree — it asks git nothing.

Suggested checklist:
- Content changes, so what is delivered has to be the version that is current after the change — that is what the editor and the visitor are owed. A defect is judged by that outcome: the old version still being served is the defect, and the error it eventually throws is the symptom.
- Confirm the target TYPO3 core branch and issue context.
- Inspect nearby code, tests, and established subsystem conventions.
- Keep the patch focused on the stated task.
- Add or update the narrowest useful test coverage.
- Run targeted tests first; broaden to CGL, functional, or npm checks when relevant.
- Use the existing backend component classes and their documented markup instead of new ad-hoc classes.
- Check the styleguide demo of the component for the canonical structure.
- Write the commit message with typo3_commit_message_guide and workflow="core": summarize the changed behavior, the affected area and the commands you ran, and it hands back a draft that carries the keyword, the trailers and the wrapping.

Establish in your checkout — this server cannot see it:
- Which files the task actually touches
  git status --short and git diff --name-only in the core checkout, then call typo3_hint_lookup with those paths for the conventions that apply to them.
- Which tests already cover them
  Core tests mirror the class path below typo3/sysext/<ext>/Tests/Unit/ and Tests/Functional/. Find the file there, then ask typo3_test_run_guide for the targeted runTests.sh invocation.
- The branch you are on and the branches the change is meant for
  git branch --show-current. In the normal case the patch targets main and the merging core team member handles the backport; push to a release branch only when the bug does not exist on main.
- Whether the paths, classes, labels, and identifiers named in an answer still exist on that branch
  Call typo3_component_lookup for curated backend components: it reads the active installation when the target matches. For uncatalogued code or another target branch, grep the checkout; typo3_catalog_scope names the fallback revision.
- Whether an icon identifier is registered, and which one spells the shape you want
  Ask typo3_icon_lookup: it reads the registry of the installation this server was started in, the T3Icons set and every installed package included. Where there is no reachable installation, the same three places can be read by hand — typo3/sysext/core/Resources/Public/Icons/T3Icons/icons.json, the Configuration/Icons.php of each package, and typo3/sysext/core/Resources/Public/Icons/Flags/ for the flags-* family.
- Whether a label for this wording already exists
  Identify the XLF resource already used at the consuming code, then ask typo3_label_lookup with that resource. It applies the installation's resource overrides, but a match from another module or package is not reusable in this context. Where the console cannot be reached it reads the installed package's XLF file instead and says so; only where there is no installation at all is there nothing to ask.

Next lookups for this task:
- typo3_component_lookup — before writing backend markup or CSS classes
- typo3_changelog_lookup — for what 14 changed about this area — the first stop when you have not built on it recently, not only a lookup after the fact
- typo3_hint_lookup — with the concrete file paths, once they are known
- typo3_test_run_guide — for the targeted runTests.sh invocation
- typo3_commit_message_guide — with workflow="core", before committing — the default is a repository of your own and adds no Forge issue or release trailer
- typo3_feedback_record — when one of these answers was wrong or incomplete
```

Data:

```json
{
    "task": "Add a badge to the list module",
    "paths": [],
    "scopes": [],
    "changeType": "unknown",
    "targetVersion": 14,
    "targetVersions": [
        14
    ],
    "domains": [
        "php"
    ],
    "scope": "core",
    "intents": [
        {
            "id": "backend-ui",
            "title": "Backend UI markup",
            "confidence": "strong",
            "condition": "only if the change adds or alters backend component markup or CSS classes"
        }
    ],
    "skills": [],
    "hints": [],
    "omittedHints": [],
    "rules": [
        {
            "documentId": "core/contribution/rules",
            "title": "TYPO3 Core Contribution Rules",
            "uri": "typo3://guides/core/contribution/rules",
            "heading": "Testing",
            "body": "- Unit tests are expected for isolated behavior.\n- Functional tests are expected for persistence, configuration, routing, backend\n  behavior, or integration with TYPO3 services.\n- End-to-end tests, the `e2e` suite, are useful when the change affects editor\n  or administrator workflows and only breaks in the assembled backend. They\n  replaced the former acceptance suites.\n- Document tests that could not be executed and why.",
            "versions": "",
            "coverage": 0.5,
            "score": 32,
            "truncated": false
        }
    ],
    "checks": [
        "CI=true ./Build/Scripts/runTests.sh -s unit",
        "CI=true ./Build/Scripts/runTests.sh -s functional",
        "CI=true ./Build/Scripts/runTests.sh -s lintScss",
        "CI=true ./Build/Scripts/runTests.sh -s build"
    ],
    "conditionalChecks": [],
    "testSuites": [
        {
            "suite": "cglGit",
            "command": "CI=true ./Build/Scripts/runTests.sh -s cglGit",
            "targeted": "CI=true ./Build/Scripts/runTests.sh -s cgl -n",
            "description": "Checks and fixes coding guideline issues in the latest committed patch.",
            "whenToUse": "Use for a focused pre-review check after creating a commit, from a normal checkout only. Its file list comes from git inside the container, and a git worktree keeps its gitdir outside the mounted directory: git fails, the list is empty, and the suite reports SUCCESS having read nothing. Use `cgl -n` where the checkout may be a worktree — it asks git nothing.",
            "domains": [
                "php"
            ],
            "versions": ""
        }
    ],
    "checklist": [
        "Content changes, so what is delivered has to be the version that is current after the change — that is what the editor and the visitor are owed. A defect is judged by that outcome: the old version still being served is the defect, and the error it eventually throws is the symptom.",
        "Confirm the target TYPO3 core branch and issue context.",
        "Inspect nearby code, tests, and established subsystem conventions.",
        "Keep the patch focused on the stated task.",
        "Add or update the narrowest useful test coverage.",
        "Run targeted tests first; broaden to CGL, functional, or npm checks when relevant.",
        "Use the existing backend component classes and their documented markup instead of new ad-hoc classes.",
        "Check the styleguide demo of the component for the canonical structure.",
        "Write the commit message with typo3_commit_message_guide and workflow=\"core\": summarize the changed behavior, the affected area and the commands you ran, and it hands back a draft that carries the keyword, the trailers and the wrapping."
    ],
    "checkoutDiscovery": [
        {
            "establish": "Which files the task actually touches",
            "how": "git status --short and git diff --name-only in the core checkout, then call typo3_hint_lookup with those paths for the conventions that apply to them."
        },
        {
            "establish": "Which tests already cover them",
            "how": "Core tests mirror the class path below typo3/sysext/<ext>/Tests/Unit/ and Tests/Functional/. Find the file there, then ask typo3_test_run_guide for the targeted runTests.sh invocation."
        },
        {
            "establish": "The branch you are on and the branches the change is meant for",
            "how": "git branch --show-current. In the normal case the patch targets main and the merging core team member handles the backport; push to a release branch only when the bug does not exist on main."
        },
        {
            "establish": "Whether the paths, classes, labels, and identifiers named in an answer still exist on that branch",
            "how": "Call typo3_component_lookup for curated backend components: it reads the active installation when the target matches. For uncatalogued code or another target branch, grep the checkout; typo3_catalog_scope names the fallback revision."
        },
        {
            "establish": "Whether an icon identifier is registered, and which one spells the shape you want",
            "how": "Ask typo3_icon_lookup: it reads the registry of the installation this server was started in, the T3Icons set and every installed package included. Where there is no reachable installation, the same three places can be read by hand — typo3/sysext/core/Resources/Public/Icons/T3Icons/icons.json, the Configuration/Icons.php of each package, and typo3/sysext/core/Resources/Public/Icons/Flags/ for the flags-* family."
        },
        {
            "establish": "Whether a label for this wording already exists",
            "how": "Identify the XLF resource already used at the consuming code, then ask typo3_label_lookup with that resource. It applies the installation's resource overrides, but a match from another module or package is not reusable in this context. Where the console cannot be reached it reads the installed package's XLF file instead and says so; only where there is no installation at all is there nothing to ask."
        }
    ],
    "nextTools": [
        {
            "tool": "typo3_component_lookup",
            "when": "before writing backend markup or CSS classes"
        },
        {
            "tool": "typo3_changelog_lookup",
            "when": "for what 14 changed about this area — the first stop when you have not built on it recently, not only a lookup after the fact"
        },
        {
            "tool": "typo3_hint_lookup",
            "when": "with the concrete file paths, once they are known"
        },
        {
            "tool": "typo3_test_run_guide",
            "when": "for the targeted runTests.sh invocation"
        },
        {
            "tool": "typo3_commit_message_guide",
            "when": "with workflow=\"core\", before committing — the default is a repository of your own and adds no Forge issue or release trailer"
        },
        {
            "tool": "typo3_feedback_record",
            "when": "when one of these answers was wrong or incomplete"
        }
    ]
}
```

### brief: paths of two kinds

Called with:

```json
{
    "task": "Fix the query that reads the events",
    "paths": [
        "packages/acme_events/Classes/Domain/Repository/EventRepository.php",
        "typo3/sysext/core/Classes/Database/Query/QueryBuilder.php"
    ],
    "changeType": "bugfix"
}
```

Text:

```
Of the paths given, packages/acme_events/Classes/Domain/Repository/EventRepository.php is outside the TYPO3 core — a project or third-party extension. What follows is split accordingly: what only the core repository has is left out of the half that is about it. The checks below, the changelog and the submission route belong to the core repository, so they are steps for the paths that are in it and for none of the others.

Task: Fix the query that reads the events
Change type: bugfix
Domains: php
Paths:
- packages/acme_events/Classes/Domain/Repository/EventRepository.php (extension)
- typo3/sysext/core/Classes/Database/Query/QueryBuilder.php

Hints:
The hints below are typo3_hint_lookup's, matched for these paths and quoted whole. A finding that cites one of these rules is citing that lookup rather than this guide.
These are everything typo3_hint_lookup matches for these paths, so calling it again by path adds nothing; a subject it holds under another path or id is still a call away.

# For typo3/sysext/core/Classes/Database/Query/QueryBuilder.php

### PHP

## Reading Records, and What Is Hidden From the Query
Hints:
- A QueryBuilder from ConnectionPool::getQueryBuilderForTable() already carries a DefaultRestrictionContainer: DeletedRestriction, HiddenRestriction, StartTimeRestriction and EndTimeRestriction. A plain select therefore hides disabled and time-restricted rows without saying so, which is what a record that is in the database and not in the result usually is.
- Taking them off is deliberate and partial: getRestrictions()->removeAll() drops all four, and the ordinary form adds DeletedRestriction back, because a deleted row is not a row. BackendUtility::getRecord() is the worked example — removeAll(), then DeletedRestriction unless the caller asked for it too.
- The frontend uses FrontendRestrictionContainer instead, which PageRepository sets with the current Context: the same four plus WorkspaceRestriction and FrontendGroupRestriction. Access groups and workspaces are conditions there and nowhere else.
- Outside the frontend a query returns the live record, and the workspace version is put on top of it afterwards: PageRepository::versionOL($table, $row) overlays it in place. It is a step after the query, not a condition in it.
- The translation works the same way: PageRepository::getLanguageOverlay($table, $row, ?LanguageAspect) replaces the row's fields with the translated ones and honours the fallback chain the LanguageAspect describes. Selecting rows by sys_language_uid is not the same thing and misses the fallback. PageRepository::getPage() shows the order both are applied in: versionOL() first, then the language overlay.
- PageRepository::getDefaultConstraints($table, $enableFieldsToIgnore) returns the enable-field conditions as QueryBuilder expressions, for a query that builds its own restrictions. [TYPO3 v13 and newer]

## System Extension Boundaries
Hints:
- Keep changes inside the owning system extension unless a cross-extension contract really changes.
- Reuse public APIs from other system extensions instead of depending on internal implementation details.
- Check nearby extension-local tests before adding shared behavior.

# For packages/acme_events/Classes/Domain/Repository/EventRepository.php — extension

### PHP

## Models, Repositories and the Table Behind Them
Hints:
- A model maps onto the table its class name implies. Configuration/Extbase/Persistence/Classes.php is where a table named differently is mapped, together with the per-property column names and the record type of a single-table inheritance.
- Orderings are property names, not column names. Ordering by the order records have in the backend therefore needs a property for that field on the model, although it is not a domain concept.

Relevant TYPO3 core checks:
- `CI=true ./Build/Scripts/runTests.sh -s unit`
- `CI=true ./Build/Scripts/runTests.sh -s functional`
## checkExtensionScannerRst
`CI=true ./Build/Scripts/runTests.sh -s checkExtensionScannerRst`
Use when a deprecation or breaking change adds extension scanner matchers.
## checkIntegrityPhp
`CI=true ./Build/Scripts/runTests.sh -s checkIntegrityPhp`
Use before review after touching PHP files; it catches conventions that neither lintPhp nor cgl covers.
## composerInstall
`CI=true ./Build/Scripts/runTests.sh -s composerInstall`
Use once in a checkout that has no vendor/ or bin/ yet, before any other suite: a fresh clone, and a git worktree, which starts without both because /vendor/* and /bin/* are gitignored. Without it every PHP suite stops at `exec: line 9: bin/phpunit: not found`. It is a precondition and not a step — a checkout that already has vendor/ needs it again only after composer.json or composer.lock changed. It needs no PHP on the host, unlike `composer install` run there.
## e2e
`CI=true ./Build/Scripts/runTests.sh -s e2e`
Use for editor or administrator workflows that only break in the assembled backend.

Suggested checklist:
- Content changes, so what is delivered has to be the version that is current after the change — that is what the editor and the visitor are owed. A defect is judged by that outcome: the old version still being served is the defect, and the error it eventually throws is the symptom.
- Confirm the target TYPO3 core branch and issue context.
- Inspect nearby code, tests, and established subsystem conventions.
- Keep the patch focused on the stated task.
- Add or update the narrowest useful test coverage.
- Run targeted tests first; broaden to CGL, functional, or npm checks when relevant.
- Reproduce the bug first, ideally with a failing test that the fix turns green.
- Check whether the bug also affects maintained older release branches.
- Write the commit message with typo3_commit_message_guide and workflow="core": summarize the changed behavior, the affected area and the commands you ran, and it hands back a draft that carries the keyword, the trailers and the wrapping.

Establish in your checkout — this server cannot see it:
- Which files the task actually touches
  git status --short and git diff --name-only in the core checkout, then call typo3_hint_lookup with those paths for the conventions that apply to them.
- Which tests already cover them
  Core tests mirror the class path below typo3/sysext/<ext>/Tests/Unit/ and Tests/Functional/. Find the file there, then ask typo3_test_run_guide for the targeted runTests.sh invocation.
- The branch you are on and the branches the change is meant for
  git branch --show-current. In the normal case the patch targets main and the merging core team member handles the backport; push to a release branch only when the bug does not exist on main.
- Whether the paths, classes, labels, and identifiers named in an answer still exist on that branch
  Call typo3_component_lookup for curated backend components: it reads the active installation when the target matches. For uncatalogued code or another target branch, grep the checkout; typo3_catalog_scope names the fallback revision.
- Whether an icon identifier is registered, and which one spells the shape you want
  Ask typo3_icon_lookup: it reads the registry of the installation this server was started in, the T3Icons set and every installed package included. Where there is no reachable installation, the same three places can be read by hand — typo3/sysext/core/Resources/Public/Icons/T3Icons/icons.json, the Configuration/Icons.php of each package, and typo3/sysext/core/Resources/Public/Icons/Flags/ for the flags-* family.
- Whether a label for this wording already exists
  Identify the XLF resource already used at the consuming code, then ask typo3_label_lookup with that resource. It applies the installation's resource overrides, but a match from another module or package is not reusable in this context. Where the console cannot be reached it reads the installed package's XLF file instead and says so; only where there is no installation at all is there nothing to ask.

Next lookups for this task:
- typo3_changelog_lookup — for what 14 changed about this area — the first stop when you have not built on it recently, not only a lookup after the fact
- typo3_hint_lookup — with the concrete file paths, once they are known
- typo3_test_run_guide — for the targeted runTests.sh invocation
- typo3_commit_message_guide — with workflow="core", before committing — the default is a repository of your own and adds no Forge issue or release trailer
- typo3_feedback_record — when one of these answers was wrong or incomplete
```

Data:

```json
{
    "task": "Fix the query that reads the events",
    "paths": [
        "packages/acme_events/Classes/Domain/Repository/EventRepository.php",
        "typo3/sysext/core/Classes/Database/Query/QueryBuilder.php"
    ],
    "scopes": [
        {
            "path": "packages/acme_events/Classes/Domain/Repository/EventRepository.php",
            "scope": "extension"
        },
        {
            "path": "typo3/sysext/core/Classes/Database/Query/QueryBuilder.php",
            "scope": "core"
        }
    ],
    "changeType": "bugfix",
    "targetVersion": 14,
    "targetVersions": [
        14
    ],
    "domains": [
        "php"
    ],
    "scope": "core",
    "intents": [],
    "skills": [],
    "hints": [
        {
            "id": "persistence-reading",
            "title": "Reading Records, and What Is Hidden From the Query",
            "category": "PHP",
            "scope": null,
            "hints": [
                {
                    "text": "A QueryBuilder from ConnectionPool::getQueryBuilderForTable() already carries a DefaultRestrictionContainer: DeletedRestriction, HiddenRestriction, StartTimeRestriction and EndTimeRestriction. A plain select therefore hides disabled and time-restricted rows without saying so, which is what a record that is in the database and not in the result usually is.",
                    "since": null,
                    "until": null,
                    "versions": "",
                    "scope": null
                },
                {
                    "text": "Taking them off is deliberate and partial: getRestrictions()->removeAll() drops all four, and the ordinary form adds DeletedRestriction back, because a deleted row is not a row. BackendUtility::getRecord() is the worked example — removeAll(), then DeletedRestriction unless the caller asked for it too.",
                    "since": null,
                    "until": null,
                    "versions": "",
                    "scope": null
                },
                {
                    "text": "The frontend uses FrontendRestrictionContainer instead, which PageRepository sets with the current Context: the same four plus WorkspaceRestriction and FrontendGroupRestriction. Access groups and workspaces are conditions there and nowhere else.",
                    "since": null,
                    "until": null,
                    "versions": "",
                    "scope": null
                },
                {
                    "text": "Outside the frontend a query returns the live record, and the workspace version is put on top of it afterwards: PageRepository::versionOL($table, $row) overlays it in place. It is a step after the query, not a condition in it.",
                    "since": null,
                    "until": null,
                    "versions": "",
                    "scope": null
                },
                {
                    "text": "The translation works the same way: PageRepository::getLanguageOverlay($table, $row, ?LanguageAspect) replaces the row's fields with the translated ones and honours the fallback chain the LanguageAspect describes. Selecting rows by sys_language_uid is not the same thing and misses the fallback. PageRepository::getPage() shows the order both are applied in: versionOL() first, then the language overlay.",
                    "since": null,
                    "until": null,
                    "versions": "",
                    "scope": null
                },
                {
                    "text": "PageRepository::getDefaultConstraints($table, $enableFieldsToIgnore) returns the enable-field conditions as QueryBuilder expressions, for a query that builds its own restrictions.",
                    "since": 13,
                    "until": null,
                    "versions": "TYPO3 v13 and newer",
                    "scope": null
                }
            ]
        },
        {
            "id": "system-extension-boundaries",
            "title": "System Extension Boundaries",
            "category": "PHP",
            "scope": null,
            "hints": [
                {
                    "text": "Keep changes inside the owning system extension unless a cross-extension contract really changes.",
                    "since": null,
                    "until": null,
                    "versions": "",
                    "scope": null
                },
                {
                    "text": "Reuse public APIs from other system extensions instead of depending on internal implementation details.",
                    "since": null,
                    "until": null,
                    "versions": "",
                    "scope": null
                },
                {
                    "text": "Check nearby extension-local tests before adding shared behavior.",
                    "since": null,
                    "until": null,
                    "versions": "",
                    "scope": null
                }
            ]
        },
        {
            "id": "extbase-domain-mapping",
            "title": "Models, Repositories and the Table Behind Them",
            "category": "PHP",
            "scope": null,
            "hints": [
                {
                    "text": "A model maps onto the table its class name implies. Configuration/Extbase/Persistence/Classes.php is where a table named differently is mapped, together with the per-property column names and the record type of a single-table inheritance.",
                    "since": null,
                    "until": null,
                    "versions": "",
                    "scope": null
                },
                {
                    "text": "Orderings are property names, not column names. Ordering by the order records have in the backend therefore needs a property for that field on the model, although it is not a domain concept.",
                    "since": null,
                    "until": null,
                    "versions": "",
                    "scope": null
                }
            ]
        }
    ],
    "omittedHints": [],
    "rules": [],
    "checks": [
        "CI=true ./Build/Scripts/runTests.sh -s unit",
        "CI=true ./Build/Scripts/runTests.sh -s functional"
    ],
    "conditionalChecks": [],
    "testSuites": [
        {
            "suite": "checkExtensionScannerRst",
            "command": "CI=true ./Build/Scripts/runTests.sh -s checkExtensionScannerRst",
            "targeted": null,
            "description": "Verifies that all .rst files referenced by the extension scanner exist.",
            "whenToUse": "Use when a deprecation or breaking change adds extension scanner matchers.",
            "domains": [
                "docs",
                "php"
            ],
            "versions": ""
        },
        {
            "suite": "checkIntegrityPhp",
            "command": "CI=true ./Build/Scripts/runTests.sh -s checkIntegrityPhp",
            "targeted": null,
            "description": "Checks core PHP files against the registered integrity rules.",
            "whenToUse": "Use before review after touching PHP files; it catches conventions that neither lintPhp nor cgl covers.",
            "domains": [
                "php"
            ],
            "versions": "TYPO3 v13 and newer"
        },
        {
            "suite": "composerInstall",
            "command": "CI=true ./Build/Scripts/runTests.sh -s composerInstall",
            "targeted": null,
            "description": "Installs the PHP dependencies of the checkout it is run in, into its own vendor/ and bin/, inside the container.",
            "whenToUse": "Use once in a checkout that has no vendor/ or bin/ yet, before any other suite: a fresh clone, and a git worktree, which starts without both because /vendor/* and /bin/* are gitignored. Without it every PHP suite stops at `exec: line 9: bin/phpunit: not found`. It is a precondition and not a step — a checkout that already has vendor/ needs it again only after composer.json or composer.lock changed. It needs no PHP on the host, unlike `composer install` run there.",
            "domains": [
                "php",
                "fluid",
                "typoscript"
            ],
            "versions": ""
        },
        {
            "suite": "e2e",
            "command": "CI=true ./Build/Scripts/runTests.sh -s e2e",
            "targeted": null,
            "description": "End-to-end tests driving a real backend with Playwright.",
            "whenToUse": "Use for editor or administrator workflows that only break in the assembled backend.",
            "domains": [
                "php",
                "typescript",
                "fluid"
            ],
            "versions": "TYPO3 v13 and newer"
        }
    ],
    "checklist": [
        "Content changes, so what is delivered has to be the version that is current after the change — that is what the editor and the visitor are owed. A defect is judged by that outcome: the old version still being served is the defect, and the error it eventually throws is the symptom.",
        "Confirm the target TYPO3 core branch and issue context.",
        "Inspect nearby code, tests, and established subsystem conventions.",
        "Keep the patch focused on the stated task.",
        "Add or update the narrowest useful test coverage.",
        "Run targeted tests first; broaden to CGL, functional, or npm checks when relevant.",
        "Reproduce the bug first, ideally with a failing test that the fix turns green.",
        "Check whether the bug also affects maintained older release branches.",
        "Write the commit message with typo3_commit_message_guide and workflow=\"core\": summarize the changed behavior, the affected area and the commands you ran, and it hands back a draft that carries the keyword, the trailers and the wrapping."
    ],
    "checkoutDiscovery": [
        {
            "establish": "Which files the task actually touches",
            "how": "git status --short and git diff --name-only in the core checkout, then call typo3_hint_lookup with those paths for the conventions that apply to them."
        },
        {
            "establish": "Which tests already cover them",
            "how": "Core tests mirror the class path below typo3/sysext/<ext>/Tests/Unit/ and Tests/Functional/. Find the file there, then ask typo3_test_run_guide for the targeted runTests.sh invocation."
        },
        {
            "establish": "The branch you are on and the branches the change is meant for",
            "how": "git branch --show-current. In the normal case the patch targets main and the merging core team member handles the backport; push to a release branch only when the bug does not exist on main."
        },
        {
            "establish": "Whether the paths, classes, labels, and identifiers named in an answer still exist on that branch",
            "how": "Call typo3_component_lookup for curated backend components: it reads the active installation when the target matches. For uncatalogued code or another target branch, grep the checkout; typo3_catalog_scope names the fallback revision."
        },
        {
            "establish": "Whether an icon identifier is registered, and which one spells the shape you want",
            "how": "Ask typo3_icon_lookup: it reads the registry of the installation this server was started in, the T3Icons set and every installed package included. Where there is no reachable installation, the same three places can be read by hand — typo3/sysext/core/Resources/Public/Icons/T3Icons/icons.json, the Configuration/Icons.php of each package, and typo3/sysext/core/Resources/Public/Icons/Flags/ for the flags-* family."
        },
        {
            "establish": "Whether a label for this wording already exists",
            "how": "Identify the XLF resource already used at the consuming code, then ask typo3_label_lookup with that resource. It applies the installation's resource overrides, but a match from another module or package is not reusable in this context. Where the console cannot be reached it reads the installed package's XLF file instead and says so; only where there is no installation at all is there nothing to ask."
        }
    ],
    "nextTools": [
        {
            "tool": "typo3_changelog_lookup",
            "when": "for what 14 changed about this area — the first stop when you have not built on it recently, not only a lookup after the fact"
        },
        {
            "tool": "typo3_hint_lookup",
            "when": "with the concrete file paths, once they are known"
        },
        {
            "tool": "typo3_test_run_guide",
            "when": "for the targeted runTests.sh invocation"
        },
        {
            "tool": "typo3_commit_message_guide",
            "when": "with workflow=\"core\", before committing — the default is a repository of your own and adds no Forge issue or release trailer"
        },
        {
            "tool": "typo3_feedback_record",
            "when": "when one of these answers was wrong or incomplete"
        }
    ]
}
```

---
date: 2026-08-18T07:15:00+00:00
category: tool-gap
status: closed
closed: 2026-08-18
model: claude-opus-5[1m]
tool: typo3_extension_describe, typo3_task_guide
directory: /home/benji/projects/blog
---

# nothing answers how a freshly booted extension seeds its own content, and hunting for it cost fiv...

## Observation

Task: boot the local DDEV development installation of the t3g/blog extension repository. The install succeeded; the remaining question was the one the user actually cares about — how does this instance get a blog in it.

I correct an undercount in my earlier feedback on tools never called: I wrote "two Bash calls hand-finding exactly those". It was five, and two of the five returned literally nothing:

- `grep -rn "blog/standalone\|blog/integration\|bootstrap" Documentation --include=*.rst` → no output at all. The extension ships four site sets and its manual never names one of them.
- `grep -rn "console.command\|Command" Configuration/Services.yaml; ls Classes/Command; typo3 list | grep -i blog` → no output at all. There is no console command; the answer was the absence.

The other three found it: the extension ships a Setup Wizard as a backend module, documented in Documentation/Setup/Wizard/Index.rst, which creates a standalone blog root page and its configuration, and Documentation/Setup/Index.rst splits standalone (wizard) from integrated (manual) setup. That is the answer I handed the user, and it is the single most useful sentence in my final report.

No tool answers this question. typo3_extension_describe comes closest and I should have called it — it reports the backend modules, the site sets and the manual, so it would have put the Blog module and Documentation/ in front of me in one call. But even it does not answer the question as asked. It would have told me a backend module exists and named the sets; it would not have told me that this module *is* the seeding procedure, or that the manual splits standalone from integrated, or that there is no CLI equivalent. Those three facts are what a boot needs at its last step, and I read them out of .rst files.

This matters beyond one extension. The server already models seeding well for the case where a package ships Initialisation/data.xml — sitepackage-initial-content, initial-content-import-once, initial-content-references, and the operations checklist all cover it. This extension seeds by a different mechanism entirely: an interactive backend wizard. Nothing in the model has a place for that, so the workflow's step 4 ("Seed the content the package is to be developed against") silently has no answer for an extension of this shape, and the caller is left grepping .rst.

Related, same subject: the operations checklist's closing item asks the report to name the URL, the backend user and the corrections made by hand. It does not ask what the user should do next to have something to look at. For an extension repository that is the difference between "the installation answers" and "the installation is useful".

## Query

After the installation answered 200, the question "how does this extension get content into a fresh instance" was worked out with: `find Configuration/Sets -name config.yaml` + head over bootstrap-package sets; `grep -rn "blog/standalone\|blog/integration\|bootstrap" Documentation --include=*.rst` (empty); `ls -R Documentation/Installation Documentation/Setup` + `find Documentation -name "*.rst" | xargs grep -ln`; `sed -n '1,80p' Documentation/Setup/Index.rst` and Wizard/Index.rst; `grep -rn "console.command\|Command" Configuration/Services.yaml; ls Classes/Command; typo3 list | grep -i blog` (empty).

## Suggestion

Give typo3_extension_describe a seeding answer, or add one to the boot workflow: for an extension in scope, say by which of the known mechanisms it seeds a fresh instance — an Initialisation/ data file, a console command, a backend setup module, a site set that has to be added to an existing site, or none — and where that is documented in its own manual. The three inputs are already parsed today (backend modules, site sets, the manual's presence and paths); what is missing is the question being askable. Failing that, add a line to the operations checklist: before reporting the boot done, establish how this package expects a fresh instance to be filled, and name it to the user — for many extensions that is a backend wizard rather than anything the console can do, and it will not be found in the site configuration.

# What was decided, and on what evidence

The working directory: one file per decision, in the group its id names. The
listing below and the one at the foot of each group's own `readme.md` are
written by `bin/cli decisions:index`.

What a decision is and what its states mean:
[documentation/records/decisions.rst](../documentation/records/decisions.rst).
Where an entry goes, how one is written and what a later session adds to it:
[documentation/records/writing-a-decision.rst](../documentation/records/writing-a-decision.rst),
which `bin/cli decisions:check` holds every file to.

## Every decision, by group

What is not listed as revoked still holds. `confirmed` marks the ones somebody
went back to and found standing; the rest are open, which is the ordinary case
and not a defect. What was decided lately is `bin/cli decisions:list`.

### audience

- [`D-AUD-010`][D-AUD-010] — The content model is answered and the records stay with the installation · 2026-08-12
- [`D-AUD-009`][D-AUD-009] — The entry point claims patch work, and a task that ends before one reads itself out · 2026-08-08
- [`D-AUD-008`][D-AUD-008] — The server is called dev-companion, under the vendor TYPO3's own tooling uses · 2026-08-06 · confirmed
- [`D-AUD-005`][D-AUD-005] — An exclusion naming no tool is reported on stderr, and the server starts · 2026-08-04
- [`D-AUD-006`][D-AUD-006] — The server reports the exclusion that happened, and the installer keeps the line it did not write · 2026-08-04
- [`D-AUD-007`][D-AUD-007] — The prose documents are named where a session already looks · 2026-08-04
- [`D-AUD-004`][D-AUD-004] — Every client is offered every tool, and the answer says who it obliges · 2026-08-02
- [`D-AUD-003`][D-AUD-003] — The instructions carry the entry point, because the tool descriptions never arrive · 2026-07-31 · confirmed
- [`D-AUD-001`][D-AUD-001] — The outward description stays core-first until there is non-core knowledge · 2026-07-29 · confirmed

[D-AUD-010]: audience/aud-010-the-content-model-is-answered-and-the-records-stay-with-the-installation.md
[D-AUD-009]: audience/aud-009-the-entry-point-claims-patch-work-and-a-task-that-ends-before-one-reads-itself-out.md
[D-AUD-008]: audience/aud-008-the-server-is-called-dev-companion-under-the-tooling-vendor.md
[D-AUD-005]: audience/aud-005-an-exclusion-naming-no-tool-is-reported-on-stderr.md
[D-AUD-006]: audience/aud-006-the-server-reports-the-exclusion-that-happened-and-the-installer-keeps-the-line-it-did-not-write.md
[D-AUD-007]: audience/aud-007-the-prose-documents-are-named-where-a-session-already-looks.md
[D-AUD-004]: audience/aud-004-every-client-is-offered-every-tool-and-the-answer-obliges.md
[D-AUD-003]: audience/aud-003-the-instructions-carry-the-entry-point.md
[D-AUD-001]: audience/aud-001-the-outward-description-stays-core-first-until-there-is-more.md

### discovery

- [`D-DIS-019`][D-DIS-019] — A project root is found from what its manifest declares · 2026-08-18
- [`D-DIS-017`][D-DIS-017] — The skills reach a project through the installer · 2026-08-12
- [`D-DIS-018`][D-DIS-018] — What `install` writes stays inside the project · 2026-08-12
- [`D-DIS-014`][D-DIS-014] — The refresh is wired by the project, and the fence is not taken · 2026-08-08
- [`D-DIS-016`][D-DIS-016] — How an entrypoint may be named is a per-client question · 2026-08-08 · confirmed
- [`D-DIS-013`][D-DIS-013] — The record holds a digest of what was published · 2026-08-06
- [`D-DIS-011`][D-DIS-011] — What was read from the installation lives as long as the call · 2026-08-04
- [`D-DIS-012`][D-DIS-012] — The driver decides whether the derived columns need the database server · 2026-08-04
- [`D-DIS-010`][D-DIS-010] — What this package writes into a project ignores itself · 2026-08-03
- [`D-DIS-007`][D-DIS-007] — The DDEV console is named by the mount, not by the variable · 2026-08-02 · confirmed
- [`D-DIS-009`][D-DIS-009] — Installed is one step short of callable, and the install is what says so · 2026-08-02 · confirmed
- [`D-DIS-006`][D-DIS-006] — The installation stays worked out from the directory the server was started in · 2026-08-01
- [`D-DIS-005`][D-DIS-005] — A registry with no console command is read by booting the installation · 2026-07-31 · confirmed
- [`D-DIS-001`][D-DIS-001] — The root package counts as an installed package · 2026-07-29 · confirmed
- [`D-DIS-004`][D-DIS-004] — The version comes from the core package, not from the console · 2026-07-29 · confirmed

[D-DIS-019]: discovery/dis-019-a-project-root-is-found-from-what-its-manifest-declares.md
[D-DIS-017]: discovery/dis-017-the-skills-reach-a-project-through-the-installer.md
[D-DIS-018]: discovery/dis-018-what-install-writes-stays-inside-the-project.md
[D-DIS-014]: discovery/dis-014-the-refresh-is-wired-by-the-project-and-the-fence-is-not-taken.md
[D-DIS-016]: discovery/dis-016-how-an-entrypoint-may-be-named-is-a-per-client-question.md
[D-DIS-013]: discovery/dis-013-the-record-holds-a-digest-of-what-was-published.md
[D-DIS-011]: discovery/dis-011-what-was-read-from-the-installation-lives-as-long-as-the-call.md
[D-DIS-012]: discovery/dis-012-the-driver-decides-whether-the-derived-columns-need-the-database-server.md
[D-DIS-010]: discovery/dis-010-what-this-package-writes-into-a-project-ignores-itself.md
[D-DIS-007]: discovery/dis-007-the-ddev-console-is-named-by-the-mount-not-by-the-variable.md
[D-DIS-009]: discovery/dis-009-installed-is-one-step-short-of-callable-and-the-install-is-what-says-so.md
[D-DIS-006]: discovery/dis-006-the-installation-stays-worked-out-from-the-start-directory.md
[D-DIS-005]: discovery/dis-005-a-registry-with-no-command-is-read-by-booting-the-installation.md
[D-DIS-001]: discovery/dis-001-the-root-package-counts-as-an-installed-package.md
[D-DIS-004]: discovery/dis-004-the-version-comes-from-the-core-package-not-from-the-console.md

### answers

- [`D-ANS-082`][D-ANS-082] — The project answer states how its three PHP numbers relate · 2026-08-18
- [`D-ANS-084`][D-ANS-084] — A curated phrase crosses the domain gate where the selected layers do not claim it · 2026-08-18
- [`D-ANS-085`][D-ANS-085] — The project answer is owed by the repository, not by the installation in it · 2026-08-18
- [`D-ANS-083`][D-ANS-083] — The unsupported answer is the whole diagnostic, and the orientation tool is for a caller who has none · 2026-08-17
- [`D-ANS-079`][D-ANS-079] — A change answer carries the votes on it and the comments nobody answered · 2026-08-14
- [`D-ANS-080`][D-ANS-080] — A change answer names the siblings that share its Change-Id · 2026-08-14
- [`D-ANS-077`][D-ANS-077] — The module answer carries the resolved navigation component and each module's routes · 2026-08-12
- [`D-ANS-078`][D-ANS-078] — The icon lookup validates a list of identifiers in one call · 2026-08-12
- [`D-ANS-074`][D-ANS-074] — A path-narrowed suite list names the domains it withheld and when to ask again · 2026-08-11
- [`D-ANS-075`][D-ANS-075] — The hint index is ordered by the rank the matcher already computed · 2026-08-11
- [`D-ANS-076`][D-ANS-076] — A search whose matches are all in one page answers with the page · 2026-08-11
- [`D-ANS-071`][D-ANS-071] — The environment answer names the project and what its files serve · 2026-08-10
- [`D-ANS-072`][D-ANS-072] — A tool description says which questions it takes, and which belong next door · 2026-08-10
- [`D-ANS-073`][D-ANS-073] — Which lines can take a patch is not which lines this patch belongs on · 2026-08-10
- [`D-ANS-068`][D-ANS-068] — A change answer carries the ref that fetches the patch set it names · 2026-08-09
- [`D-ANS-070`][D-ANS-070] — A document is handed over by the call that reads it and by what the answer left of it · 2026-08-09
- [`D-ANS-064`][D-ANS-064] — An issue answer holds what a triage needs and does not make it legible · 2026-08-08
- [`D-ANS-065`][D-ANS-065] — The manual index is the inventory each manual publishes · 2026-08-08
- [`D-ANS-066`][D-ANS-066] — One handle serves every read of one Fetch · 2026-08-08
- [`D-ANS-067`][D-ANS-067] — The changelog above the installed major comes from the manual · 2026-08-08
- [`D-ANS-069`][D-ANS-069] — A backlog row carries the review server and not the journal · 2026-08-08
- [`D-ANS-060`][D-ANS-060] — A bare word in `appliesTo` reaches a path segment and outranks the subsystem · 2026-08-07
- [`D-ANS-061`][D-ANS-061] — An answer that names a document hands it over rather than pointing at it · 2026-08-07
- [`D-ANS-062`][D-ANS-062] — An anonymous read cannot tell a restricted change from an absent one · 2026-08-07
- [`D-ANS-063`][D-ANS-063] — What a core session defends is the option list and the check it could not run itself · 2026-08-07
- [`D-ANS-054`][D-ANS-054] — The backlog is a third way into the tracker, and the areas are read from it · 2026-08-05 · confirmed
- [`D-ANS-055`][D-ANS-055] — A change answers for an issue only where its commit message names it · 2026-08-05
- [`D-ANS-056`][D-ANS-056] — A search hit is filled from the issue it is · 2026-08-05
- [`D-ANS-057`][D-ANS-057] — What hangs off an issue is named, and the reading is the caller's · 2026-08-05
- [`D-ANS-058`][D-ANS-058] — The release lines a trailer claims are a lookup, and not a count of commits · 2026-08-05 · confirmed
- [`D-ANS-059`][D-ANS-059] — What this server holds carried the task, and what it read elsewhere is where it misled · 2026-08-05
- [`D-ANS-048`][D-ANS-048] — A tool declares what can answer it, and both readers render that · 2026-08-04
- [`D-ANS-049`][D-ANS-049] — An answer from outside is held where the caller cannot change it · 2026-08-04
- [`D-ANS-050`][D-ANS-050] — A curated needle matches the word it is, and a stem matches past its end · 2026-08-04
- [`D-ANS-051`][D-ANS-051] — A manual result carries how much of the question it covers, and no page is taken away for covering little · 2026-08-04
- [`D-ANS-052`][D-ANS-052] — The configuration lookup answers for the installation as it stands · 2026-08-04
- [`D-ANS-053`][D-ANS-053] — A rejected call names the argument that was not understood · 2026-08-04
- [`D-ANS-033`][D-ANS-033] — The review server is read anonymously, and the answer says what that leaves out · 2026-08-03 · confirmed
- [`D-ANS-034`][D-ANS-034] — A source outside this package answers JSON, or it did not answer · 2026-08-03
- [`D-ANS-035`][D-ANS-035] — The matcher entry is owed to what the changelog tag claims · 2026-08-03 · confirmed
- [`D-ANS-036`][D-ANS-036] — A query written in Fluid tags is searched in the book that documents them · 2026-08-03
- [`D-ANS-037`][D-ANS-037] — A compound rule query is owed the section its score prefers, and a miss that names the words · 2026-08-03
- [`D-ANS-038`][D-ANS-038] — The tracker is searched by words as well as read by number · 2026-08-03
- [`D-ANS-039`][D-ANS-039] — The Extbase fork is delivered by the content-element intent, and it forks on the request rather than on the category · 2026-08-03
- [`D-ANS-040`][D-ANS-040] — A boundary guard is asked with a query that clears the coverage floor · 2026-08-03
- [`D-ANS-041`][D-ANS-041] — The changelog title is read where the file names carry nothing · 2026-08-03
- [`D-ANS-042`][D-ANS-042] — An identifier reaches the changelog entries whose body names it · 2026-08-03
- [`D-ANS-043`][D-ANS-043] — A miss is answered in data, and says which corpus its silence belongs to · 2026-08-03
- [`D-ANS-044`][D-ANS-044] — The environment answer carries the lifecycle it declares, beside the interpreter it runs · 2026-08-03
- [`D-ANS-045`][D-ANS-045] — The Classes section covers the directory it names, and a value read off the tree says so · 2026-08-03
- [`D-ANS-046`][D-ANS-046] — A manual result covers the question it is returned for, and the silence names the corpus that answers · 2026-08-03
- [`D-ANS-047`][D-ANS-047] — A word behind a namespace prefix is searched for as the name it is · 2026-08-03
- [`D-ANS-005`][D-ANS-005] — A question that is not supported here is answered in a shape of its own · 2026-08-02
- [`D-ANS-006`][D-ANS-006] — An identifier is found however it is spelled · 2026-08-02
- [`D-ANS-007`][D-ANS-007] — Two shapes for "not answered", one word for why · 2026-08-02
- [`D-ANS-008`][D-ANS-008] — A number a reader cannot reproduce is read as wrong · 2026-08-02
- [`D-ANS-009`][D-ANS-009] — A shipped-file deprecation is found by the tool that lists the file · 2026-08-02 · confirmed
- [`D-ANS-010`][D-ANS-010] — "Does it still work" is a question for the manual, not the changelog · 2026-08-02
- [`D-ANS-011`][D-ANS-011] — A scope answer states what a manifest declares, and the comparison is the audit's · 2026-08-02
- [`D-ANS-012`][D-ANS-012] — An `oneOf` alternative is stated where the caller composes the call · 2026-08-02
- [`D-ANS-013`][D-ANS-013] — What runs a project is a placement, not a missing answer · 2026-08-02
- [`D-ANS-014`][D-ANS-014] — The extension answer enumerates registrations, not files — and a registration is one wherever it is declared · 2026-08-02
- [`D-ANS-015`][D-ANS-015] — A registration the extension answer misreads is inside its boundary, not evidence about where it runs · 2026-08-02
- [`D-ANS-016`][D-ANS-016] — A miss names the query that would have hit, not only the reach of each word · 2026-08-02
- [`D-ANS-017`][D-ANS-017] — A union-typed argument gets the wording a client can compose against · 2026-08-02
- [`D-ANS-018`][D-ANS-018] — A plugin is a kind of content element, not one whose template is missing · 2026-08-02
- [`D-ANS-019`][D-ANS-019] — A FlexForm, a site set and a form set are read from the file names and call shapes core itself reads them by · 2026-08-02
- [`D-ANS-020`][D-ANS-020] — A deprecation is answered by the version that removes it · 2026-08-02
- [`D-ANS-021`][D-ANS-021] — A manual query is told what short buys, because the index is a table of contents · 2026-08-02
- [`D-ANS-022`][D-ANS-022] — The matcher takes a hyphenated compound apart, measured over the corpus first · 2026-08-02
- [`D-ANS-024`][D-ANS-024] — A rule reaches only the task that already names its subject · 2026-08-02
- [`D-ANS-025`][D-ANS-025] — A query a hint carries whole is not diluted out of it · 2026-08-02
- [`D-ANS-026`][D-ANS-026] — The ViewHelper reference is indexed, and a manual carries the collection it is published in · 2026-08-02
- [`D-ANS-028`][D-ANS-028] — A two-letter query word is searched for, and the stopword list is what keeps the others out · 2026-08-02
- [`D-ANS-030`][D-ANS-030] — The changelog matcher runs over the title it prints · 2026-08-02
- [`D-ANS-031`][D-ANS-031] — The core answer names the tool that runs the suites · 2026-08-02
- [`D-ANS-032`][D-ANS-032] — The dilution reference of the manual ranking is the length of an ordinary title · 2026-08-02
- [`D-ANS-004`][D-ANS-004] — The instruction budget is 2048 characters, on one client's evidence · 2026-07-31
- [`D-ANS-002`][D-ANS-002] — Three numbers now decide what a lookup answers, and they were measured, not reasoned · 2026-07-30 · confirmed
- [`D-ANS-003`][D-ANS-003] — Retrieval stays lexical and runtime inspection stays narrow · 2026-07-30 · confirmed

[D-ANS-082]: answers/ans-082-the-project-answer-states-how-its-three-php-numbers-relate.md
[D-ANS-084]: answers/ans-084-a-curated-phrase-crosses-the-domain-gate-where-the-selected-layers-do-not-claim-it.md
[D-ANS-085]: answers/ans-085-the-project-answer-is-owed-by-the-repository-not-by-the-installation-in-it.md
[D-ANS-083]: answers/ans-083-the-unsupported-answer-is-the-whole-diagnostic-and-the-orientation-tool-is-for-a-caller-who-has-none.md
[D-ANS-079]: answers/ans-079-a-change-answer-carries-the-votes-on-it-and-the-comments-nobody-answered.md
[D-ANS-080]: answers/ans-080-a-change-answer-names-the-siblings-that-share-its-change-id.md
[D-ANS-077]: answers/ans-077-the-module-answer-carries-the-resolved-navigation-component-and-each-modules-routes.md
[D-ANS-078]: answers/ans-078-the-icon-lookup-validates-a-list-of-identifiers-in-one-call.md
[D-ANS-074]: answers/ans-074-a-path-narrowed-suite-list-names-the-domains-it-withheld-and-when-to-ask-again.md
[D-ANS-075]: answers/ans-075-the-hint-index-is-ordered-by-the-rank-the-matcher-already-computed.md
[D-ANS-076]: answers/ans-076-a-search-whose-matches-are-all-in-one-page-answers-with-the-page.md
[D-ANS-071]: answers/ans-071-the-environment-answer-names-the-project-and-what-its-files-serve.md
[D-ANS-072]: answers/ans-072-a-tool-description-says-which-questions-it-takes-and-which-belong-next-door.md
[D-ANS-073]: answers/ans-073-which-lines-can-take-a-patch-is-not-which-lines-this-patch-belongs-on.md
[D-ANS-068]: answers/ans-068-a-change-answer-carries-the-ref-that-fetches-the-patch-set-it-names.md
[D-ANS-070]: answers/ans-070-a-document-is-handed-over-by-the-call-that-reads-it-and-by-what-the-answer-left-of-it.md
[D-ANS-064]: answers/ans-064-an-issue-answer-holds-what-a-triage-needs-and-does-not-make-it-legible.md
[D-ANS-065]: answers/ans-065-the-manual-index-is-the-inventory-each-manual-publishes.md
[D-ANS-066]: answers/ans-066-one-handle-serves-every-read-of-one-fetch.md
[D-ANS-067]: answers/ans-067-the-changelog-above-the-installed-major-comes-from-the-manual.md
[D-ANS-069]: answers/ans-069-a-backlog-row-carries-the-review-server-and-not-the-journal.md
[D-ANS-060]: answers/ans-060-a-bare-word-in-appliesto-reaches-a-path-segment-and-outranks-the-subsystem.md
[D-ANS-061]: answers/ans-061-an-answer-that-names-a-document-hands-it-over-rather-than-pointing-at-it.md
[D-ANS-062]: answers/ans-062-an-anonymous-read-cannot-tell-a-restricted-change-from-an-absent-one.md
[D-ANS-063]: answers/ans-063-what-a-core-session-defends-is-the-option-list-and-the-check-it-could-not-run-itself.md
[D-ANS-054]: answers/ans-054-the-backlog-is-a-third-way-into-the-tracker-and-the-areas-are-read-from-it.md
[D-ANS-055]: answers/ans-055-a-change-answers-for-an-issue-only-where-its-commit-message-names-it.md
[D-ANS-056]: answers/ans-056-a-search-hit-is-filled-from-the-issue-it-is.md
[D-ANS-057]: answers/ans-057-what-hangs-off-an-issue-is-named-and-the-reading-is-the-callers.md
[D-ANS-058]: answers/ans-058-the-release-lines-a-trailer-claims-are-a-lookup-and-not-a-count-of-commits.md
[D-ANS-059]: answers/ans-059-what-this-server-holds-carried-the-task-and-what-it-read-elsewhere-misled-it.md
[D-ANS-048]: answers/ans-048-a-tool-declares-what-can-answer-it-and-both-readers-render-that.md
[D-ANS-049]: answers/ans-049-an-answer-from-outside-is-held-where-the-caller-cannot-change-it.md
[D-ANS-050]: answers/ans-050-a-curated-needle-matches-the-word-it-is-and-a-stem-matches-past-its-end.md
[D-ANS-051]: answers/ans-051-a-manual-result-carries-how-much-of-the-question-it-covers.md
[D-ANS-052]: answers/ans-052-the-configuration-lookup-answers-for-the-installation-as-it-stands.md
[D-ANS-053]: answers/ans-053-a-rejected-call-names-the-argument-that-was-not-understood.md
[D-ANS-033]: answers/ans-033-the-review-server-is-read-anonymously-and-the-answer-says-what-that-leaves-out.md
[D-ANS-034]: answers/ans-034-a-source-outside-this-package-answers-json-or-it-did-not-answer.md
[D-ANS-035]: answers/ans-035-the-matcher-entry-is-owed-to-what-the-changelog-tag-claims.md
[D-ANS-036]: answers/ans-036-a-query-written-in-fluid-tags-is-searched-in-the-book-that-documents-them.md
[D-ANS-037]: answers/ans-037-a-compound-rule-query-is-owed-the-section-its-score-prefers-and-a-miss-that-names-the-words.md
[D-ANS-038]: answers/ans-038-the-tracker-is-searched-by-words-as-well-as-read-by-number.md
[D-ANS-039]: answers/ans-039-the-extbase-fork-is-delivered-by-the-task-intent-and-forks-on-the-request.md
[D-ANS-040]: answers/ans-040-a-boundary-guard-is-asked-with-a-query-that-clears-the-floor.md
[D-ANS-041]: answers/ans-041-the-changelog-title-is-read-where-the-file-names-carry-nothing.md
[D-ANS-042]: answers/ans-042-an-identifier-reaches-the-changelog-entries-whose-body-names-it.md
[D-ANS-043]: answers/ans-043-a-miss-is-answered-in-data-and-says-which-corpus-its-silence-belongs-to.md
[D-ANS-044]: answers/ans-044-the-environment-answer-carries-the-lifecycle-it-declares.md
[D-ANS-045]: answers/ans-045-the-classes-section-covers-the-directory-it-names.md
[D-ANS-046]: answers/ans-046-a-manual-result-covers-the-question-it-is-returned-for.md
[D-ANS-047]: answers/ans-047-a-word-behind-a-namespace-prefix-is-searched-for-as-the-name-it-is.md
[D-ANS-005]: answers/ans-005-an-unmet-precondition-is-answered-not-raised.md
[D-ANS-006]: answers/ans-006-an-identifier-is-found-however-it-is-spelled.md
[D-ANS-007]: answers/ans-007-two-shapes-for-not-answered-and-one-word-for-why.md
[D-ANS-008]: answers/ans-008-a-number-a-reader-cannot-reproduce-is-read-as-wrong.md
[D-ANS-009]: answers/ans-009-a-shipped-file-deprecation-is-found-by-the-tool-that-lists-it.md
[D-ANS-010]: answers/ans-010-does-it-still-work-is-a-question-for-the-manual.md
[D-ANS-011]: answers/ans-011-a-scope-answer-states-what-a-manifest-declares.md
[D-ANS-012]: answers/ans-012-an-oneof-alternative-is-stated-where-the-call-is-composed.md
[D-ANS-013]: answers/ans-013-what-runs-a-project-is-a-placement-not-a-missing-answer.md
[D-ANS-014]: answers/ans-014-the-extension-answer-enumerates-registrations-not-files.md
[D-ANS-015]: answers/ans-015-a-registration-the-extension-answer-misreads-is-inside-its-boundary.md
[D-ANS-016]: answers/ans-016-a-miss-names-the-query-that-would-have-hit.md
[D-ANS-017]: answers/ans-017-a-union-typed-argument-gets-wording-a-client-can-compose-against.md
[D-ANS-018]: answers/ans-018-a-plugin-is-a-kind-of-content-element-not-one-whose-template-is-missing.md
[D-ANS-019]: answers/ans-019-three-registration-kinds-read-from-what-core-reads-them-for.md
[D-ANS-020]: answers/ans-020-a-deprecation-is-answered-by-the-version-that-removes-it.md
[D-ANS-021]: answers/ans-021-a-manual-query-is-told-what-short-buys.md
[D-ANS-022]: answers/ans-022-the-matcher-takes-a-hyphenated-compound-apart.md
[D-ANS-024]: answers/ans-024-a-rule-reaches-only-the-task-that-already-names-its-subject.md
[D-ANS-025]: answers/ans-025-a-query-a-hint-carries-whole-is-not-diluted-out-of-it.md
[D-ANS-026]: answers/ans-026-the-viewhelper-reference-is-indexed-and-a-manual-carries-the-collection-it-is-published-in.md
[D-ANS-028]: answers/ans-028-a-two-letter-query-word-is-searched-for-and-the-stopword-list-is-what-keeps-the-others-out.md
[D-ANS-030]: answers/ans-030-the-changelog-matcher-runs-over-the-title-it-prints.md
[D-ANS-031]: answers/ans-031-the-core-answer-names-the-tool-that-runs-the-suites.md
[D-ANS-032]: answers/ans-032-the-dilution-reference-of-the-manual-ranking-is-the-length-of-an-ordinary-title.md
[D-ANS-004]: answers/ans-004-the-instruction-budget-is-2048-characters-on-one-clients-evidence.md
[D-ANS-002]: answers/ans-002-three-numbers-decide-what-a-lookup-answers.md
[D-ANS-003]: answers/ans-003-retrieval-stays-lexical-and-runtime-inspection-stays-narrow.md

### knowledge

- [`D-KNW-083`][D-KNW-083] — The shared-root collision is stated for the partial root as well · 2026-08-18
- [`D-KNW-084`][D-KNW-084] — The corpus states which placeholder spelling a relation value survives · 2026-08-18
- [`D-KNW-085`][D-KNW-085] — When DDEV writes additional.php is a gap this server owns · 2026-08-18 · confirmed
- [`D-KNW-086`][D-KNW-086] — Which PHP a covered version runs on is a gap this server owns · 2026-08-18
- [`D-KNW-087`][D-KNW-087] — A listed neighbour says what it prevents · 2026-08-18
- [`D-KNW-088`][D-KNW-088] — What a Composer installation generates below the document root is a gap this server owns · 2026-08-18
- [`D-KNW-089`][D-KNW-089] — What a warm TCA cache hides from `extension:setup` is a gap this server owns · 2026-08-18
- [`D-KNW-090`][D-KNW-090] — The corpus names the PHP type a record and a transformed column arrive as · 2026-08-18 · confirmed
- [`D-KNW-091`][D-KNW-091] — A PHP version is the payload a hint may state, and a TYPO3 version is not · 2026-08-18
- [`D-KNW-092`][D-KNW-092] — What an installation that does not answer is diagnosed from is a gap this server owns · 2026-08-18
- [`D-KNW-093`][D-KNW-093] — A command whose success is unconditional is followed by what a correct result looks like · 2026-08-18 · confirmed
- [`D-KNW-094`][D-KNW-094] — How a variable reaches a console command in the container is a gap this server owns · 2026-08-18 · confirmed
- [`D-KNW-095`][D-KNW-095] — The installation procedure is a document and the hints keep the facts · 2026-08-18
- [`D-KNW-096`][D-KNW-096] — How a package fills a fresh instance is a gap this server owns · 2026-08-18
- [`D-KNW-097`][D-KNW-097] — Which site a request matches when two bases collide is a gap this server owns · 2026-08-18
- [`D-KNW-098`][D-KNW-098] — Where a site nobody wrote came from is a gap this server owns · 2026-08-18
- [`D-KNW-099`][D-KNW-099] — What a row handed to lib.contentElement owes is a gap this server owns · 2026-08-18
- [`D-KNW-080`][D-KNW-080] — The impexp export hint is corrected against a run of the command it prescribes · 2026-08-17 · confirmed
- [`D-KNW-081`][D-KNW-081] — What a NEW placeholder may contain in a relation field is a gap this server owns · 2026-08-17 · confirmed
- [`D-KNW-082`][D-KNW-082] — A content element names its template, and the CType derivation is theme_camino's · 2026-08-17
- [`D-KNW-071`][D-KNW-071] — Proving what a rendering change renders is a procedure this server carries · 2026-08-14
- [`D-KNW-073`][D-KNW-073] — The corpus states what makes a change breaking with no member moved · 2026-08-14
- [`D-KNW-075`][D-KNW-075] — How Fluid resolves an object path is a gap this server owns · 2026-08-14 · confirmed
- [`D-KNW-076`][D-KNW-076] — What a new backend label costs before it resolves is a gap this server owns · 2026-08-14 · confirmed
- [`D-KNW-077`][D-KNW-077] — The TypeScript style hint names the config and carries what cannot be guessed · 2026-08-14
- [`D-KNW-078`][D-KNW-078] — The corpus states the shape a Record-sourced row has · 2026-08-14
- [`D-KNW-079`][D-KNW-079] — The corpus states what a new backend label costs · 2026-08-14
- [`D-KNW-070`][D-KNW-070] — Backend routing internals are a gap this server owns · 2026-08-12
- [`D-KNW-066`][D-KNW-066] — The browser baseline is a release day, and core usage is not evidence of it · 2026-08-10
- [`D-KNW-067`][D-KNW-067] — The JavaScript test layer is a hint, and a test query still answers from PHP · 2026-08-10
- [`D-KNW-068`][D-KNW-068] — Looking at a backend change is a suite the core already carries · 2026-08-10
- [`D-KNW-069`][D-KNW-069] — A browser in a container reaches a DDEV site on the router's own network · 2026-08-10
- [`D-KNW-065`][D-KNW-065] — What a public method on a non-final core class commits its author to is a gap this server owns · 2026-08-09 · confirmed
- [`D-KNW-064`][D-KNW-064] — The disabled assertions a core checkout carries are a grep and not a lookup · 2026-08-08
- [`D-KNW-063`][D-KNW-063] — What a TCA type stores is a subject this server owns and does not carry · 2026-08-07
- [`D-KNW-055`][D-KNW-055] — The first check a standalone extension repository gets is a gap this server owns · 2026-08-04
- [`D-KNW-056`][D-KNW-056] — A file skeleton is shipped as a version-bound document section · 2026-08-04
- [`D-KNW-057`][D-KNW-057] — A document declares what it is and when to reach for it · 2026-08-04
- [`D-KNW-058`][D-KNW-058] — The document namespace is scope first and derived from the file · 2026-08-04
- [`D-KNW-059`][D-KNW-059] — One place spells how a document is addressed · 2026-08-04
- [`D-KNW-060`][D-KNW-060] — What a backend spec locates by is written where the spec is · 2026-08-04
- [`D-KNW-061`][D-KNW-061] — The manual scaffold is a document and the hint keeps the policy · 2026-08-04
- [`D-KNW-062`][D-KNW-062] — What a hint pays with is the mechanism and the file it is read in · 2026-08-04
- [`D-KNW-029`][D-KNW-029] — A hint names the domains it is asked from, and the file names the subject · 2026-08-03
- [`D-KNW-030`][D-KNW-030] — A hint is one question, and the DataHandler family is six of them · 2026-08-03
- [`D-KNW-031`][D-KNW-031] — A suite is a property of the domain, not of the hint · 2026-08-03
- [`D-KNW-032`][D-KNW-032] — The corpus is filed by question, and two splits were taken back · 2026-08-03
- [`D-KNW-033`][D-KNW-033] — Every hint names the domains it is asked from, and none is `any` · 2026-08-03
- [`D-KNW-034`][D-KNW-034] — The file is the subject, and JavaScript is not a domain of its own · 2026-08-03
- [`D-KNW-035`][D-KNW-035] — The corpus and the tool that answers from it are called hints · 2026-08-03
- [`D-KNW-036`][D-KNW-036] — The standards check handed over is the one that cannot pass empty · 2026-08-03
- [`D-KNW-037`][D-KNW-037] — A content-element preview draws the element's own payload, and the corpus names the fields · 2026-08-03
- [`D-KNW-038`][D-KNW-038] — A hint is reached by the role of a file rather than by the extension it sits in · 2026-08-03
- [`D-KNW-039`][D-KNW-039] — The type a changelog entry owes is stated in prose and the skeleton stays a hint · 2026-08-03
- [`D-KNW-041`][D-KNW-041] — The checkout a suite is started in supplies its own dependencies · 2026-08-03
- [`D-KNW-042`][D-KNW-042] — What the image pipeline does below the task layer is a gap this server owns · 2026-08-03
- [`D-KNW-043`][D-KNW-043] — A rule about what an API may be used for carries the strength of the claim and the source it was read from · 2026-08-03
- [`D-KNW-044`][D-KNW-044] — One search over the whole Tests/ tree finds what asserts a rendered output · 2026-08-03
- [`D-KNW-045`][D-KNW-045] — The document root is named by what configures it and by what serves it · 2026-08-03
- [`D-KNW-046`][D-KNW-046] — The non-interactive install path is a gap this server owns · 2026-08-03 · confirmed
- [`D-KNW-047`][D-KNW-047] — What installs TYPO3 below the extension being developed is a gap this server owns · 2026-08-03
- [`D-KNW-048`][D-KNW-048] — What the impexp import rewrites in a site configuration is a gap this server owns · 2026-08-03
- [`D-KNW-049`][D-KNW-049] — What DDEV writes into the settings is named in full, and so is what it cannot configure · 2026-08-03 · confirmed
- [`D-KNW-050`][D-KNW-050] — What a missing `target-language` does to a translation file is a gap this server owns · 2026-08-03
- [`D-KNW-051`][D-KNW-051] — The public-asset answer names the internal static beside the supported route · 2026-08-03
- [`D-KNW-052`][D-KNW-052] — The order a Fluid template name is resolved in is a gap this server owns · 2026-08-03 · confirmed
- [`D-KNW-053`][D-KNW-053] — The root-package layout is stated from an installation and holds across the covered majors · 2026-08-03
- [`D-KNW-054`][D-KNW-054] — What booting a declared installation takes is stated as one hint beside the project's own · 2026-08-03
- [`D-KNW-005`][D-KNW-005] — One `Scope` replaced the four vocabularies · 2026-08-02
- [`D-KNW-006`][D-KNW-006] — A word for a thing administered from the backend adds no domain to a backend-only task · 2026-08-02
- [`D-KNW-007`][D-KNW-007] — A hint says whose it is in both directions · 2026-08-02
- [`D-KNW-008`][D-KNW-008] — Tooling is a row the answer crosses, not a dimension the corpus stores · 2026-08-02
- [`D-KNW-009`][D-KNW-009] — A domain keyword is a phrasing, not a word · 2026-08-02
- [`D-KNW-010`][D-KNW-010] — What the core reads from the environment is a gap this server owns · 2026-08-02
- [`D-KNW-011`][D-KNW-011] — A rule that names a defect names its correction · 2026-08-02
- [`D-KNW-012`][D-KNW-012] — `extension.neon` is PHPStan's filename, and the hint keeps the one include it means · 2026-08-02
- [`D-KNW-013`][D-KNW-013] — This repository's own sentence is reworded rather than indexed · 2026-08-02
- [`D-KNW-016`][D-KNW-016] — What an `f:else` does to the branch beside it is a gap this server owns · 2026-08-02
- [`D-KNW-017`][D-KNW-017] — A verification question is routed to the layer that verifies it · 2026-08-02
- [`D-KNW-018`][D-KNW-018] — What a datamap does to a relation field is a gap this server owns · 2026-08-02 · confirmed
- [`D-KNW-019`][D-KNW-019] — The corpus states that a functional test sees only what it primed · 2026-08-02
- [`D-KNW-020`][D-KNW-020] — What a preview template is handed is stated on both majors, and a field resolves by its TCA type · 2026-08-02
- [`D-KNW-021`][D-KNW-021] — A Fluid preview template replaces the content half, and the corpus names what is drawn around it · 2026-08-02
- [`D-KNW-022`][D-KNW-022] — The corpus states how long a per-class test database lives · 2026-08-02
- [`D-KNW-023`][D-KNW-023] — Which page may hold a record is a gap this server owns · 2026-08-02 · confirmed
- [`D-KNW-024`][D-KNW-024] — The Fluid namespace prefix is what a template question is written in · 2026-08-02
- [`D-KNW-026`][D-KNW-026] — Where a one-off script may not be written is a gap this server owns · 2026-08-02
- [`D-KNW-027`][D-KNW-027] — Which caches a change invalidates is a gap this server owns · 2026-08-02 · confirmed
- [`D-KNW-028`][D-KNW-028] — How a file becomes a processed one is a gap this server owns · 2026-08-02
- [`D-KNW-004`][D-KNW-004] — Package knowledge needs a producer before it needs discovery · 2026-07-30

[D-KNW-083]: knowledge/knw-083-the-shared-root-collision-is-stated-for-the-partial-root-as-well.md
[D-KNW-084]: knowledge/knw-084-the-corpus-states-which-placeholder-spelling-a-relation-value-survives.md
[D-KNW-085]: knowledge/knw-085-when-ddev-writes-additional-php-is-a-gap-this-server-owns.md
[D-KNW-086]: knowledge/knw-086-which-php-a-covered-version-runs-on-is-a-gap-this-server-owns.md
[D-KNW-087]: knowledge/knw-087-a-listed-neighbour-says-what-it-prevents.md
[D-KNW-088]: knowledge/knw-088-what-a-composer-installation-generates-below-the-document-root-is-a-gap-this-server-owns.md
[D-KNW-089]: knowledge/knw-089-what-a-warm-tca-cache-hides-from-extension-setup-is-a-gap-this-server-owns.md
[D-KNW-090]: knowledge/knw-090-the-corpus-names-the-php-type-a-record-and-a-transformed-column-arrive-as.md
[D-KNW-091]: knowledge/knw-091-a-php-version-is-the-payload-a-hint-may-state-and-a-typo3-version-is-not.md
[D-KNW-092]: knowledge/knw-092-what-an-installation-that-does-not-answer-is-diagnosed-from-is-a-gap-this-server-owns.md
[D-KNW-093]: knowledge/knw-093-a-command-whose-success-is-unconditional-is-followed-by-what-a-correct-result-looks-like.md
[D-KNW-094]: knowledge/knw-094-how-a-variable-reaches-a-console-command-in-the-container-is-a-gap-this-server-owns.md
[D-KNW-095]: knowledge/knw-095-the-installation-procedure-is-a-document-and-the-hints-keep-the-facts.md
[D-KNW-096]: knowledge/knw-096-how-a-package-fills-a-fresh-instance-is-a-gap-this-server-owns.md
[D-KNW-097]: knowledge/knw-097-which-site-a-request-matches-when-two-bases-collide-is-a-gap-this-server-owns.md
[D-KNW-098]: knowledge/knw-098-where-a-site-nobody-wrote-came-from-is-a-gap-this-server-owns.md
[D-KNW-099]: knowledge/knw-099-what-a-row-handed-to-lib-contentelement-owes-is-a-gap-this-server-owns.md
[D-KNW-080]: knowledge/knw-080-the-impexp-export-hint-is-corrected-against-a-run-of-the-command-it-prescribes.md
[D-KNW-081]: knowledge/knw-081-what-a-new-placeholder-may-contain-in-a-relation-field-is-a-gap-this-server-owns.md
[D-KNW-082]: knowledge/knw-082-a-content-element-names-its-template-and-the-ctype-derivation-is-theme-caminos.md
[D-KNW-071]: knowledge/knw-071-proving-what-a-rendering-change-renders-is-a-procedure-this-server-carries.md
[D-KNW-073]: knowledge/knw-073-the-corpus-states-what-makes-a-change-breaking-with-no-member-moved.md
[D-KNW-075]: knowledge/knw-075-how-fluid-resolves-an-object-path-is-a-gap-this-server-owns.md
[D-KNW-076]: knowledge/knw-076-what-a-new-backend-label-costs-before-it-resolves-is-a-gap-this-server-owns.md
[D-KNW-077]: knowledge/knw-077-the-typescript-style-hint-names-the-config-and-carries-what-cannot-be-guessed.md
[D-KNW-078]: knowledge/knw-078-the-corpus-states-the-shape-a-record-sourced-row-has.md
[D-KNW-079]: knowledge/knw-079-the-corpus-states-what-a-new-backend-label-costs.md
[D-KNW-070]: knowledge/knw-070-backend-routing-internals-are-a-gap-this-server-owns.md
[D-KNW-066]: knowledge/knw-066-the-browser-baseline-is-a-release-day-and-core-usage-is-not-evidence-of-it.md
[D-KNW-067]: knowledge/knw-067-the-javascript-test-layer-is-a-hint-and-a-test-query-still-answers-from-php.md
[D-KNW-068]: knowledge/knw-068-looking-at-a-backend-change-is-a-suite-the-core-already-carries.md
[D-KNW-069]: knowledge/knw-069-a-browser-in-a-container-reaches-a-ddev-site-on-the-routers-own-network.md
[D-KNW-065]: knowledge/knw-065-what-a-public-method-on-a-non-final-core-class-commits-its-author-to-is-a-gap-this-server-owns.md
[D-KNW-064]: knowledge/knw-064-the-disabled-assertions-a-core-checkout-carries-are-a-grep-and-not-a-lookup.md
[D-KNW-063]: knowledge/knw-063-what-a-tca-type-stores-is-a-subject-this-server-owns-and-does-not-carry.md
[D-KNW-055]: knowledge/knw-055-the-first-check-a-standalone-extension-repository-gets-is-a-gap-this-server-owns.md
[D-KNW-056]: knowledge/knw-056-a-file-skeleton-is-shipped-as-a-bound-document-section.md
[D-KNW-057]: knowledge/knw-057-a-document-declares-what-it-is-and-when-to-reach-for-it.md
[D-KNW-058]: knowledge/knw-058-the-document-namespace-is-scope-first-and-derived-from-the-file.md
[D-KNW-059]: knowledge/knw-059-one-place-spells-how-a-document-is-addressed.md
[D-KNW-060]: knowledge/knw-060-what-a-backend-spec-locates-by-is-written-where-the-spec-is.md
[D-KNW-061]: knowledge/knw-061-the-manual-scaffold-is-a-document-and-the-hint-keeps-the-policy.md
[D-KNW-062]: knowledge/knw-062-what-a-hint-pays-with-is-the-mechanism-and-the-file-it-is-read-in.md
[D-KNW-029]: knowledge/knw-029-a-hint-names-the-domains-it-is-asked-from-and-the-file-names-the-subject.md
[D-KNW-030]: knowledge/knw-030-a-hint-is-one-question-and-the-datahandler-family-is-six.md
[D-KNW-031]: knowledge/knw-031-a-suite-is-a-property-of-the-domain-not-of-the-hint.md
[D-KNW-032]: knowledge/knw-032-the-corpus-is-filed-by-question-and-two-splits-were-taken-back.md
[D-KNW-033]: knowledge/knw-033-every-hint-names-the-domains-it-is-asked-from-and-none-is-any.md
[D-KNW-034]: knowledge/knw-034-the-file-is-the-subject-and-javascript-is-not-a-domain.md
[D-KNW-035]: knowledge/knw-035-the-corpus-and-the-tool-that-answers-from-it-are-called-hints.md
[D-KNW-036]: knowledge/knw-036-the-standards-check-handed-over-is-the-one-that-cannot-pass-empty.md
[D-KNW-037]: knowledge/knw-037-a-content-element-preview-draws-the-elements-own-payload-and-the-corpus-names-the-fields.md
[D-KNW-038]: knowledge/knw-038-a-hint-is-reached-by-the-role-of-a-file-rather-than-by-the-extension-it-sits-in.md
[D-KNW-039]: knowledge/knw-039-the-type-a-changelog-entry-owes-is-stated-in-prose-and-the-skeleton-stays-a-hint.md
[D-KNW-041]: knowledge/knw-041-the-checkout-a-suite-is-started-in-supplies-its-own-dependencies.md
[D-KNW-042]: knowledge/knw-042-what-the-image-pipeline-does-below-the-task-layer-is-a-gap-this-server-owns.md
[D-KNW-043]: knowledge/knw-043-a-rule-about-what-an-api-may-be-used-for-carries-its-strength-and-its-source.md
[D-KNW-044]: knowledge/knw-044-one-search-over-the-whole-tests-tree-finds-what-asserts-a-rendered-output.md
[D-KNW-045]: knowledge/knw-045-the-document-root-is-named-by-what-configures-it-and-by-what-serves-it.md
[D-KNW-046]: knowledge/knw-046-the-non-interactive-install-path-is-a-gap-this-server-owns.md
[D-KNW-047]: knowledge/knw-047-what-installs-typo3-below-the-extension-being-developed-is-a-gap-this-server-owns.md
[D-KNW-048]: knowledge/knw-048-what-the-impexp-import-rewrites-in-a-site-configuration-is-a-gap-this-server-owns.md
[D-KNW-049]: knowledge/knw-049-what-ddev-writes-into-the-settings-is-named-in-full-and-so-is-what-it-cannot-configure.md
[D-KNW-050]: knowledge/knw-050-what-a-missing-target-language-does-to-a-translation-file-is-a-gap-this-server-owns.md
[D-KNW-051]: knowledge/knw-051-the-public-asset-answer-names-the-internal-static-beside-the-supported-route.md
[D-KNW-052]: knowledge/knw-052-the-order-a-fluid-template-name-is-resolved-in-is-a-gap-this-server-owns.md
[D-KNW-053]: knowledge/knw-053-the-root-package-layout-is-stated-from-an-installation-and-holds-across-the-covered-majors.md
[D-KNW-054]: knowledge/knw-054-what-booting-a-declared-installation-takes-is-stated-as-one-hint-beside-the-projects-own.md
[D-KNW-005]: knowledge/knw-005-one-scope-replaced-the-four-vocabularies.md
[D-KNW-006]: knowledge/knw-006-a-word-for-a-thing-administered-from-the-backend.md
[D-KNW-007]: knowledge/knw-007-a-hint-says-whose-it-is-in-both-directions.md
[D-KNW-008]: knowledge/knw-008-tooling-is-a-row-that-is-crossed-in-the-answer.md
[D-KNW-009]: knowledge/knw-009-a-domain-keyword-is-a-phrasing-not-a-word.md
[D-KNW-010]: knowledge/knw-010-what-the-core-reads-from-the-environment-is-a-gap-this-server-owns.md
[D-KNW-011]: knowledge/knw-011-a-rule-that-names-a-defect-names-its-correction.md
[D-KNW-012]: knowledge/knw-012-an-extension-neon-is-phpstans-filename-and-not-a-typo3-one.md
[D-KNW-013]: knowledge/knw-013-this-repositorys-own-sentence-is-reworded-rather-than-indexed.md
[D-KNW-016]: knowledge/knw-016-what-an-f-else-does-to-the-branch-beside-it-is-a-gap-this-server-owns.md
[D-KNW-017]: knowledge/knw-017-a-verification-question-is-routed-to-the-layer-that-verifies.md
[D-KNW-018]: knowledge/knw-018-what-a-datamap-does-to-a-relation-field-is-a-gap-this-server-owns.md
[D-KNW-019]: knowledge/knw-019-the-corpus-states-that-a-test-sees-only-what-it-primed.md
[D-KNW-020]: knowledge/knw-020-what-a-preview-template-is-handed-is-stated-on-both-majors.md
[D-KNW-021]: knowledge/knw-021-a-fluid-preview-template-replaces-the-content-half-and-the-corpus-says-so.md
[D-KNW-022]: knowledge/knw-022-the-corpus-states-how-long-a-per-class-test-database-lives.md
[D-KNW-023]: knowledge/knw-023-which-page-may-hold-a-record-is-a-gap-this-server-owns.md
[D-KNW-024]: knowledge/knw-024-the-fluid-namespace-prefix-is-what-a-template-question-is-written-in.md
[D-KNW-026]: knowledge/knw-026-where-a-one-off-script-may-not-be-written-is-a-gap-this-server-owns.md
[D-KNW-027]: knowledge/knw-027-which-caches-a-change-invalidates-is-a-gap-this-server-owns.md
[D-KNW-028]: knowledge/knw-028-how-a-file-becomes-a-processed-one-is-a-gap-this-server-owns.md
[D-KNW-004]: knowledge/knw-004-package-knowledge-needs-a-producer-before-it-needs-discovery.md

### versions

- [`D-VER-006`][D-VER-006] — A narrowed statement is split before it is bound · 2026-08-18 · confirmed
- [`D-VER-007`][D-VER-007] — A declared major that is not installed is answered by naming the reading that settles it · 2026-08-18
- [`D-VER-005`][D-VER-005] — A document section declares the majors it holds for · 2026-08-04
- [`D-VER-004`][D-VER-004] — A supported range is a property of the package, not of the checkout · 2026-07-31 · confirmed
- [`D-VER-003`][D-VER-003] — The Fluid engine gets no version axis of its own, because the core pins it · 2026-07-30 · confirmed
- [`D-VER-001`][D-VER-001] — A version range is data on the statement, not a sentence in it · 2026-07-29 · confirmed

[D-VER-006]: versions/ver-006-a-narrowed-statement-is-split-before-it-is-bound.md
[D-VER-007]: versions/ver-007-a-declared-major-that-is-not-installed-is-answered-by-naming-the-reading.md
[D-VER-005]: versions/ver-005-a-document-section-declares-the-majors-it-holds-for.md
[D-VER-004]: versions/ver-004-a-supported-range-is-a-property-of-the-package.md
[D-VER-003]: versions/ver-003-the-fluid-engine-gets-no-version-axis-of-its-own.md
[D-VER-001]: versions/ver-001-a-version-range-is-data-on-the-statement-not-a-sentence-in-it.md

### catalog

- [`D-CAT-004`][D-CAT-004] — What the component index may hold is what the core files as a component · 2026-08-11
- [`D-CAT-003`][D-CAT-003] — The component index is curated; its contract comes from the installation · 2026-07-30
- [`D-CAT-001`][D-CAT-001] — A catalog entry is bound whole, and the binding is derived · 2026-07-29

[D-CAT-004]: catalog/cat-004-what-the-component-index-may-hold-is-what-the-core-files-as-a-component.md
[D-CAT-003]: catalog/cat-003-the-component-index-is-curated-its-contract-comes-from-the-installation.md
[D-CAT-001]: catalog/cat-001-a-catalog-entry-is-bound-whole-and-the-binding-is-derived.md

### scope

- [`D-SCO-012`][D-SCO-012] — The root manifest places the work before the dependencies are installed · 2026-08-18
- [`D-SCO-010`][D-SCO-010] — All three `typo3` namespaces are kept, and the draft RFC is read as a reference · 2026-08-04
- [`D-SCO-011`][D-SCO-011] — A tool that describes one thing carries `describe`, and `scope` stays with the sources · 2026-08-04
- [`D-SCO-009`][D-SCO-009] — The brief is one brief, and names the paths a step is not for · 2026-08-02
- [`D-SCO-007`][D-SCO-007] — The signals are combined per call, and a call is not a path · 2026-08-01
- [`D-SCO-002`][D-SCO-002] — A core-only intent asks for evidence, not for silence · 2026-07-29 · confirmed
- [`D-SCO-003`][D-SCO-003] — What is core-only is decided per line, by what it names · 2026-07-29 · confirmed
- [`D-SCO-005`][D-SCO-005] — The installation is evidence about the task, and the weakest kind · 2026-07-29
- [`D-SCO-006`][D-SCO-006] — Why "project work is out of scope" kept coming back · 2026-07-29

[D-SCO-012]: scope/sco-012-the-root-manifest-places-the-work-before-the-dependencies-are-installed.md
[D-SCO-010]: scope/sco-010-all-three-typo3-namespaces-are-kept-and-the-draft-rfc-is-read-as-a-reference.md
[D-SCO-011]: scope/sco-011-a-tool-that-describes-one-thing-carries-describe-and-scope-stays-with-the-sources.md
[D-SCO-009]: scope/sco-009-the-brief-is-one-brief-and-names-the-paths-a-step.md
[D-SCO-007]: scope/sco-007-the-signals-are-combined-per-call-and-a-call-is-not-a-path.md
[D-SCO-002]: scope/sco-002-a-core-only-intent-asks-for-evidence-not-for-silence.md
[D-SCO-003]: scope/sco-003-what-is-core-only-is-decided-per-line-by-what-it-names.md
[D-SCO-005]: scope/sco-005-the-installation-is-evidence-about-the-task-and-the-weakest-kind.md
[D-SCO-006]: scope/sco-006-why-project-work-is-out-of-scope-kept-coming-back.md

### guides

- [`D-GUI-012`][D-GUI-012] — The brief names the guide the recognized work belongs to · 2026-08-18
- [`D-GUI-013`][D-GUI-013] — The brief names the sweep a change owes · 2026-08-18
- [`D-GUI-011`][D-GUI-011] — Reviewing a report against code is a change type of its own · 2026-08-08
- [`D-GUI-009`][D-GUI-009] — A stated change type keeps the skeleton and the words keep their surface · 2026-08-04
- [`D-GUI-010`][D-GUI-010] — The commit workflow defaults to the repository most callers are in · 2026-08-04
- [`D-GUI-003`][D-GUI-003] — The wrapping conflict is resolved in the answer rather than in silence · 2026-08-03
- [`D-GUI-004`][D-GUI-004] — A review brief states the removal surface rather than matching it · 2026-08-03
- [`D-GUI-005`][D-GUI-005] — The product premise is one statement, on the brief every task passes through · 2026-08-03
- [`D-GUI-006`][D-GUI-006] — A task that changes nothing is a change type of its own · 2026-08-03
- [`D-GUI-007`][D-GUI-007] — The brief carries a selection of the hints and says whose they are · 2026-08-03 · confirmed
- [`D-GUI-008`][D-GUI-008] — Operating an installation is a change type of its own · 2026-08-03
- [`D-GUI-001`][D-GUI-001] — A missing release target becomes a placeholder, not `main` · 2026-07-29

[D-GUI-012]: guides/gui-012-the-brief-names-the-guide-the-recognized-work-belongs-to.md
[D-GUI-013]: guides/gui-013-the-brief-names-the-sweep-a-change-owes.md
[D-GUI-011]: guides/gui-011-reviewing-a-report-against-code-is-a-change-type-of-its-own.md
[D-GUI-009]: guides/gui-009-a-stated-change-type-keeps-the-skeleton-and-the-words-keep-their-surface.md
[D-GUI-010]: guides/gui-010-the-commit-workflow-defaults-to-the-repository-most-callers-are-in.md
[D-GUI-003]: guides/gui-003-the-wrapping-conflict-is-resolved-in-the-answer-rather-than-in-silence.md
[D-GUI-004]: guides/gui-004-a-review-brief-states-the-removal-surface-rather-than-matching-it.md
[D-GUI-005]: guides/gui-005-the-product-premise-is-one-statement-on-the-brief-every-task-passes-through.md
[D-GUI-006]: guides/gui-006-a-task-that-changes-nothing-is-a-change-type-of-its-own.md
[D-GUI-007]: guides/gui-007-the-brief-carries-a-selection-of-the-hints-and-says-whose-they-are.md
[D-GUI-008]: guides/gui-008-operating-an-installation-is-a-change-type-of-its-own.md
[D-GUI-001]: guides/gui-001-a-missing-release-target-becomes-a-placeholder-not-main.md

### evidence

- [`D-EVI-007`][D-EVI-007] — A case no test holds says so with its exit code · 2026-08-18
- [`D-EVI-006`][D-EVI-006] — One installation per covered version, kept and started · 2026-08-03
- [`D-EVI-004`][D-EVI-004] — The environment is made here, and the repository under review is not · 2026-08-02
- [`D-EVI-005`][D-EVI-005] — A registration nothing can reach is cleared, and the database goes with it · 2026-08-02
- [`D-EVI-001`][D-EVI-001] — Forward evidence comes from a review, not from a prompt that knows the answer · 2026-07-31 · confirmed
- [`D-EVI-002`][D-EVI-002] — A skill crossing is read rather than run · 2026-07-31 · confirmed
- [`D-EVI-003`][D-EVI-003] — A review runs the checks that cannot change the code · 2026-07-31

[D-EVI-007]: evidence/evi-007-a-case-no-test-holds-says-so-with-its-exit-code.md
[D-EVI-006]: evidence/evi-006-one-installation-per-covered-version-kept-and-started.md
[D-EVI-004]: evidence/evi-004-the-environment-is-made-here-and-the-repository-under-review-is-not.md
[D-EVI-005]: evidence/evi-005-a-registration-nothing-can-reach-is-cleared-and-the-database-goes-with-it.md
[D-EVI-001]: evidence/evi-001-forward-evidence-comes-from-a-review.md
[D-EVI-002]: evidence/evi-002-a-skill-crossing-is-read-rather-than-run.md
[D-EVI-003]: evidence/evi-003-a-review-runs-the-checks-that-cannot-change-the-code.md

### task-skills

- [`D-SKL-044`][D-SKL-044] — A step that names two hint ids says what each one alone answers · 2026-08-18
- [`D-SKL-045`][D-SKL-045] — A build workflow names the guide at the step that needs it · 2026-08-18
- [`D-SKL-046`][D-SKL-046] — A precondition is restated in the workflow that writes the file it guards · 2026-08-18
- [`D-SKL-047`][D-SKL-047] — The Composer root step fetches the installer keys from the hint that owns them · 2026-08-18
- [`D-SKL-048`][D-SKL-048] — A build workflow says a symptom is a lookup trigger · 2026-08-18
- [`D-SKL-049`][D-SKL-049] — The gate at the end of a workflow waits for the corrections it would sit on · 2026-08-18
- [`D-SKL-050`][D-SKL-050] — Producing a distribution's content earns a task skill, and the project repository is owned · 2026-08-18 · confirmed
- [`D-SKL-051`][D-SKL-051] — A site built from scratch reaches the installation intent · 2026-08-18
- [`D-SKL-052`][D-SKL-052] — The injected size of a skill is what the retention rule leaves · 2026-08-18
- [`D-SKL-053`][D-SKL-053] — An absence in the extension answer names the skill that owns it · 2026-08-18
- [`D-SKL-054`][D-SKL-054] — The listing budget is what a client reads, and a draft is not in it · 2026-08-18
- [`D-SKL-055`][D-SKL-055] — A call a skill names in order not to make it is written as a discharge · 2026-08-18
- [`D-SKL-056`][D-SKL-056] — The installation workflow branches on the declared procedure and proves what the run wrote · 2026-08-18
- [`D-SKL-057`][D-SKL-057] — A command's option set is read from the installed console and its meaning from the manual · 2026-08-18
- [`D-SKL-058`][D-SKL-058] — A hint is routed by what the repository is rather than by how its installation came to exist · 2026-08-18
- [`D-SKL-059`][D-SKL-059] — The installation that already answers is owned by the workflow that created it · 2026-08-18
- [`D-SKL-037`][D-SKL-037] — The sweep's exemption names what a task produces, and its examples illustrate it · 2026-08-14
- [`D-SKL-038`][D-SKL-038] — The change answer names the skill that owns the patch it describes · 2026-08-14
- [`D-SKL-039`][D-SKL-039] — A brief that changes nothing routes only the workflows that change nothing · 2026-08-14
- [`D-SKL-041`][D-SKL-041] — A patch carried onto current code is carried on a named branch · 2026-08-14
- [`D-SKL-042`][D-SKL-042] — A report is copyable markdown, and the answer is where it goes · 2026-08-14
- [`D-SKL-043`][D-SKL-043] — A rule query carries two subjects, and a third is a call of its own · 2026-08-14
- [`D-SKL-035`][D-SKL-035] — A new skill is measured against a run without it · 2026-08-12
- [`D-SKL-036`][D-SKL-036] — A skill runs where the installer put it · 2026-08-12
- [`D-SKL-033`][D-SKL-033] — Activation is the client's, and the order after it is what this server holds · 2026-08-11
- [`D-SKL-034`][D-SKL-034] — A step of the order is skippable on what the session holds, never on how it arrived · 2026-08-11
- [`D-SKL-032`][D-SKL-032] — A probe is worth what the session can run, and nothing it can only see · 2026-08-10
- [`D-SKL-028`][D-SKL-028] — A triage that reaches for a previous attempt is routed to the patch · 2026-08-09
- [`D-SKL-029`][D-SKL-029] — Precedent is listed by the changelog's own axes before it is asked for in words · 2026-08-09
- [`D-SKL-030`][D-SKL-030] — A review surface names the lookup that can answer it · 2026-08-09
- [`D-SKL-031`][D-SKL-031] — A triage picks a candidate on where the symptom is visible and what the suite already models · 2026-08-09
- [`D-SKL-023`][D-SKL-023] — A published skill no intent names is one the brief cannot route to · 2026-08-08
- [`D-SKL-024`][D-SKL-024] — A description names the task and leaves the steps to the body · 2026-08-08 · confirmed
- [`D-SKL-025`][D-SKL-025] — A routed tool is called and held to what the skill sends the session to read · 2026-08-08
- [`D-SKL-026`][D-SKL-026] — The descriptions are written to the listing budget they share · 2026-08-08
- [`D-SKL-027`][D-SKL-027] — A draft declares itself under this server's own metadata key · 2026-08-08
- [`D-SKL-022`][D-SKL-022] — A handoff between skills is an instruction rather than a closing sentence · 2026-08-07 · confirmed
- [`D-SKL-021`][D-SKL-021] — Triage and fetching a patch are two workflows that end before somebody else's act · 2026-08-05
- [`D-SKL-014`][D-SKL-014] — The commit step is named where a skill's workflow ends in a change · 2026-08-04
- [`D-SKL-016`][D-SKL-016] — Acting on a conformance report earns a task skill of its own · 2026-08-04
- [`D-SKL-017`][D-SKL-017] — A named check is established against the package it lands on · 2026-08-04
- [`D-SKL-018`][D-SKL-018] — The guide of the chosen layer arrives with the brief · 2026-08-04
- [`D-SKL-019`][D-SKL-019] — An absent surface is asked for by the id of its convention · 2026-08-04
- [`D-SKL-020`][D-SKL-020] — A re-check runs what the finding was about · 2026-08-04
- [`D-SKL-005`][D-SKL-005] — Core contribution earns two task skills, one for reviewing a patch and one for creating one · 2026-08-03
- [`D-SKL-006`][D-SKL-006] — The site-new cluster earns the route into the skill that owns the task · 2026-08-03
- [`D-SKL-007`][D-SKL-007] — Every disposition a review makes carries its evidence · 2026-08-03
- [`D-SKL-008`][D-SKL-008] — A review reads the review the patch is already in · 2026-08-03
- [`D-SKL-009`][D-SKL-009] — The rule that keeps not landing is written as an act with an object · 2026-08-03 · confirmed
- [`D-SKL-010`][D-SKL-010] — The assessment that precedes a core patch reads the issue and the review server · 2026-08-03
- [`D-SKL-012`][D-SKL-012] — Bringing a package's development installation into existence earns a task skill · 2026-08-03
- [`D-SKL-013`][D-SKL-013] — The guide names the skill that owns the task · 2026-08-03
- [`D-SKL-002`][D-SKL-002] — A focused audit narrows what is assessed, not the list it closes on · 2026-08-02
- [`D-SKL-003`][D-SKL-003] — A sweep is bounded by the changelog's own axes, not by the extension's vocabulary · 2026-08-02
- [`D-SKL-004`][D-SKL-004] — What a task does when the lookups run out is written for a review · 2026-08-02
- [`D-SKL-001`][D-SKL-001] — The order a task starts in is one file, and the reading comes last in it · 2026-08-01 · confirmed

[D-SKL-044]: task-skills/skl-044-a-step-that-names-two-hint-ids-says-what-each-one-alone-answers.md
[D-SKL-045]: task-skills/skl-045-a-build-workflow-names-the-guide-at-the-step-that-needs-it.md
[D-SKL-046]: task-skills/skl-046-a-precondition-is-restated-in-the-workflow-that-writes-the-file-it-guards.md
[D-SKL-047]: task-skills/skl-047-the-composer-root-step-fetches-the-installer-keys-from-the-hint-that-owns-them.md
[D-SKL-048]: task-skills/skl-048-a-build-workflow-says-a-symptom-is-a-lookup-trigger.md
[D-SKL-049]: task-skills/skl-049-the-gate-at-the-end-of-a-workflow-waits-for-the-corrections-it-would-sit-on.md
[D-SKL-050]: task-skills/skl-050-producing-a-distributions-content-earns-a-task-skill.md
[D-SKL-051]: task-skills/skl-051-a-site-built-from-scratch-reaches-the-installation-intent.md
[D-SKL-052]: task-skills/skl-052-the-injected-size-of-a-skill-is-what-the-retention-rule-leaves.md
[D-SKL-053]: task-skills/skl-053-an-absence-in-the-extension-answer-names-the-skill-that-owns-it.md
[D-SKL-054]: task-skills/skl-054-the-listing-budget-is-what-a-client-reads-and-a-draft-is-not-in-it.md
[D-SKL-055]: task-skills/skl-055-a-call-a-skill-names-in-order-not-to-make-it-is-written-as-a-discharge.md
[D-SKL-056]: task-skills/skl-056-the-installation-workflow-branches-on-the-declared-procedure-and-proves-what-the-run-wrote.md
[D-SKL-057]: task-skills/skl-057-a-commands-option-set-is-read-from-the-installed-console-and-its-meaning-from-the-manual.md
[D-SKL-058]: task-skills/skl-058-a-hint-is-routed-by-what-the-repository-is-rather-than-by-how-its-installation-came-to-exist.md
[D-SKL-059]: task-skills/skl-059-the-installation-that-already-answers-is-owned-by-the-workflow-that-created-it.md
[D-SKL-037]: task-skills/skl-037-the-sweeps-exemption-names-what-a-task-produces-and-its-examples-illustrate-it.md
[D-SKL-038]: task-skills/skl-038-the-change-answer-names-the-skill-that-owns-the-patch-it-describes.md
[D-SKL-039]: task-skills/skl-039-a-brief-that-changes-nothing-routes-only-the-workflows-that-change-nothing.md
[D-SKL-041]: task-skills/skl-041-a-patch-carried-onto-current-code-is-carried-on-a-named-branch.md
[D-SKL-042]: task-skills/skl-042-a-report-is-copyable-markdown-and-the-answer-is-where-it-goes.md
[D-SKL-043]: task-skills/skl-043-a-rule-query-carries-two-subjects-and-a-third-is-a-call-of-its-own.md
[D-SKL-035]: task-skills/skl-035-a-new-skill-is-measured-against-a-run-without-it.md
[D-SKL-036]: task-skills/skl-036-a-skill-runs-where-the-installer-put-it.md
[D-SKL-033]: task-skills/skl-033-activation-is-the-clients-and-the-order-after-it-is-what-this-server-holds.md
[D-SKL-034]: task-skills/skl-034-a-step-of-the-order-is-skippable-on-what-the-session-holds-never-on-how-it-arrived.md
[D-SKL-032]: task-skills/skl-032-a-probe-is-worth-what-the-session-can-run-and-nothing-it-can-only-see.md
[D-SKL-028]: task-skills/skl-028-a-triage-that-reaches-for-a-previous-attempt-is-routed-to-the-patch.md
[D-SKL-029]: task-skills/skl-029-precedent-is-listed-by-the-changelogs-own-axes.md
[D-SKL-030]: task-skills/skl-030-a-review-surface-names-the-lookup-that-can-answer-it.md
[D-SKL-031]: task-skills/skl-031-a-triage-picks-a-candidate-on-where-the-symptom-is-visible-and-what-the-suite-models.md
[D-SKL-023]: task-skills/skl-023-a-published-skill-no-intent-names-is-one-the-brief-cannot-route-to.md
[D-SKL-024]: task-skills/skl-024-a-description-names-the-task-and-leaves-the-steps-to-the-body.md
[D-SKL-025]: task-skills/skl-025-a-routed-tool-is-called-and-held-to-what-the-skill-sends-the-session-to-read.md
[D-SKL-026]: task-skills/skl-026-the-descriptions-are-written-to-the-listing-budget-they-share.md
[D-SKL-027]: task-skills/skl-027-a-draft-declares-itself-under-this-servers-own-metadata-key.md
[D-SKL-022]: task-skills/skl-022-a-handoff-between-skills-is-an-instruction-rather-than-a-closing-sentence.md
[D-SKL-021]: task-skills/skl-021-triage-and-fetching-a-patch-are-two-workflows-that-end-before-somebody-elses-act.md
[D-SKL-014]: task-skills/skl-014-the-commit-step-is-named-where-a-skills-workflow-ends-in-a-change.md
[D-SKL-016]: task-skills/skl-016-acting-on-a-conformance-report-earns-a-task-skill-of-its-own.md
[D-SKL-017]: task-skills/skl-017-a-named-check-is-established-against-the-package-it-lands-on.md
[D-SKL-018]: task-skills/skl-018-the-guide-of-the-chosen-layer-arrives-with-the-brief.md
[D-SKL-019]: task-skills/skl-019-an-absent-surface-is-asked-for-by-the-id-of-its-convention.md
[D-SKL-020]: task-skills/skl-020-a-re-check-runs-what-the-finding-was-about.md
[D-SKL-005]: task-skills/skl-005-core-contribution-earns-a-skill-and-the-domain-is-the-work.md
[D-SKL-006]: task-skills/skl-006-the-site-new-cluster-earns-the-route-into-the-skill-that-owns-the-task.md
[D-SKL-007]: task-skills/skl-007-every-disposition-a-review-makes-carries-its-evidence.md
[D-SKL-008]: task-skills/skl-008-a-review-reads-the-review-the-patch-is-already-in.md
[D-SKL-009]: task-skills/skl-009-the-rule-that-keeps-not-landing-is-written-as-an-act-with-an-object.md
[D-SKL-010]: task-skills/skl-010-the-assessment-that-precedes-a-core-patch-reads-the-issue-and-the-review-server.md
[D-SKL-012]: task-skills/skl-012-bringing-a-development-installation-into-existence-earns-a-task-skill.md
[D-SKL-013]: task-skills/skl-013-the-guide-names-the-skill-that-owns-the-task.md
[D-SKL-002]: task-skills/skl-002-a-focused-audit-narrows-what-is-assessed.md
[D-SKL-003]: task-skills/skl-003-a-sweep-is-bounded-by-the-changelogs-own-axes.md
[D-SKL-004]: task-skills/skl-004-what-a-task-does-when-the-lookups-run-out-is-written-for-a-review.md
[D-SKL-001]: task-skills/skl-001-the-order-a-task-starts-in-is-one-file.md

### feedback

- [`D-FBK-047`][D-FBK-047] — The debrief asks what an answer left out and what the session wanted · 2026-08-18
- [`D-FBK-048`][D-FBK-048] — The debrief is offered as a prompt where the channel is · 2026-08-18
- [`D-FBK-045`][D-FBK-045] — A feedback is queued by the call that records it · 2026-08-14
- [`D-FBK-046`][D-FBK-046] — The check that catches a duplicate id names the files and the command · 2026-08-14
- [`D-FBK-041`][D-FBK-041] — What nothing answers for is called unresolved · 2026-08-04
- [`D-FBK-042`][D-FBK-042] — The read-only boundary is the installation, and the feedback channel writes on this side of it · 2026-08-04
- [`D-FBK-043`][D-FBK-043] — A structure is answered with a document rather than with a rule · 2026-08-04
- [`D-FBK-044`][D-FBK-044] — A mangled call is refused rather than taken apart · 2026-08-04
- [`D-FBK-025`][D-FBK-025] — A judgement reads the corpus, decides the shape, and sets the priority · 2026-08-03
- [`D-FBK-026`][D-FBK-026] — The ladder needs an outcome that builds something · 2026-08-03
- [`D-FBK-027`][D-FBK-027] — The server builds what costs its caller round trips · 2026-08-03
- [`D-FBK-038`][D-FBK-038] — What decides a breaking removal is the caller, not the marker · 2026-08-03
- [`D-FBK-039`][D-FBK-039] — A mangled name is rewritten once, and the comparison carries the rest · 2026-08-03
- [`D-FBK-040`][D-FBK-040] — The card a judgement folds into another is deleted by the same commit · 2026-08-03
- [`D-FBK-011`][D-FBK-011] — The suite holds what one branch can be right about · 2026-08-02
- [`D-FBK-012`][D-FBK-012] — The queue comes first, and the sighting hands over one · 2026-08-02
- [`D-FBK-013`][D-FBK-013] — An empty queue is a state, not a failure · 2026-08-02
- [`D-FBK-014`][D-FBK-014] — Every stage is a directory, and closing is none · 2026-08-02
- [`D-FBK-015`][D-FBK-015] — A priority is a class, and the stamp is the rest · 2026-08-02
- [`D-FBK-016`][D-FBK-016] — A feedback waits on the board rather than behind it · 2026-08-02
- [`D-FBK-017`][D-FBK-017] — A judgement turns a feedback into work, and the work closes it · 2026-08-02
- [`D-FBK-018`][D-FBK-018] — A strength is evidence about a boundary, not about a decision · 2026-08-02 · confirmed
- [`D-FBK-019`][D-FBK-019] — A secret pasted into a feedback is taken out on the way in · 2026-08-02
- [`D-FBK-020`][D-FBK-020] — A session is charged per call, so the calls are what is budgeted · 2026-08-02 · confirmed
- [`D-FBK-021`][D-FBK-021] — A summary feedback is judged against its series, not on its own · 2026-08-02
- [`D-FBK-023`][D-FBK-023] — A correction is judged by what its withdrawal moves · 2026-08-02
- [`D-FBK-024`][D-FBK-024] — A feedback about the caller's conduct toward its user names no surface · 2026-08-02 · confirmed
- [`D-FBK-006`][D-FBK-006] — A name is cut where the feedback starts to differ · 2026-08-01
- [`D-FBK-007`][D-FBK-007] — How a todo is worked travels with the todo · 2026-08-01
- [`D-FBK-008`][D-FBK-008] — One todo is one file, and the queue is in the names · 2026-08-01
- [`D-FBK-009`][D-FBK-009] — A todo nobody can start waits where it says why · 2026-08-01
- [`D-FBK-010`][D-FBK-010] — `main` carries the state and the branch carries the work · 2026-08-01
- [`D-FBK-001`][D-FBK-001] — The backlog is read out rather than enforced · 2026-07-31 · confirmed
- [`D-FBK-002`][D-FBK-002] — The order of the work is declared, not inferred · 2026-07-31 · confirmed
- [`D-FBK-004`][D-FBK-004] — The model is asked, because nothing else here can say it · 2026-07-31

[D-FBK-047]: feedback/fbk-047-the-debrief-asks-what-an-answer-left-out-and-what-the-session-wanted.md
[D-FBK-048]: feedback/fbk-048-the-debrief-is-offered-as-a-prompt-where-the-channel-is.md
[D-FBK-045]: feedback/fbk-045-a-feedback-is-queued-by-the-call-that-records-it.md
[D-FBK-046]: feedback/fbk-046-the-check-that-catches-a-duplicate-id-names-the-files-and-the-command.md
[D-FBK-041]: feedback/fbk-041-what-nothing-answers-for-is-called-unresolved.md
[D-FBK-042]: feedback/fbk-042-the-read-only-boundary-is-the-installation-and-the-channel-writes-on-this-side-of-it.md
[D-FBK-043]: feedback/fbk-043-a-structure-is-answered-with-a-document-rather-than-with-a-rule.md
[D-FBK-044]: feedback/fbk-044-a-mangled-call-is-refused-rather-than-taken-apart.md
[D-FBK-025]: feedback/fbk-025-a-judgement-reads-the-corpus-decides-the-shape-and-sets-the-priority.md
[D-FBK-026]: feedback/fbk-026-the-ladder-needs-an-outcome-that-builds-something.md
[D-FBK-027]: feedback/fbk-027-the-server-builds-what-costs-its-caller-round-trips.md
[D-FBK-038]: feedback/fbk-038-what-decides-a-breaking-removal-is-the-caller-not-the-marker.md
[D-FBK-039]: feedback/fbk-039-a-mangled-name-is-rewritten-once-and-the-comparison-carries-the-rest.md
[D-FBK-040]: feedback/fbk-040-the-card-a-judgement-folds-into-another-is-deleted-by-the-same-commit.md
[D-FBK-011]: feedback/fbk-011-the-suite-holds-what-one-branch-can-be-right-about.md
[D-FBK-012]: feedback/fbk-012-the-queue-comes-first-and-the-sighting-hands-over-one.md
[D-FBK-013]: feedback/fbk-013-an-empty-queue-is-a-state-not-a-failure.md
[D-FBK-014]: feedback/fbk-014-every-stage-is-a-directory-and-closing-is-none.md
[D-FBK-015]: feedback/fbk-015-a-priority-is-a-class-and-the-stamp-is-the-rest.md
[D-FBK-016]: feedback/fbk-016-a-feedback-waits-on-the-board-rather-than-behind-it.md
[D-FBK-017]: feedback/fbk-017-a-judgement-turns-a-feedback-into-work-and-the-work-closes-it.md
[D-FBK-018]: feedback/fbk-018-a-strength-is-evidence-about-a-boundary-not-about-a-decision.md
[D-FBK-019]: feedback/fbk-019-a-secret-pasted-into-a-feedback-is-taken-out-on-the-way-in.md
[D-FBK-020]: feedback/fbk-020-a-session-is-charged-per-call-so-the-calls-are-what-is-budgeted.md
[D-FBK-021]: feedback/fbk-021-a-summary-feedback-is-judged-against-its-series-not-on-its-own.md
[D-FBK-023]: feedback/fbk-023-a-correction-is-judged-by-what-its-withdrawal-moves.md
[D-FBK-024]: feedback/fbk-024-a-feedback-about-the-callers-conduct-toward-its-user-names-no-surface.md
[D-FBK-006]: feedback/fbk-006-a-name-is-cut-where-the-feedback-starts-to-differ.md
[D-FBK-007]: feedback/fbk-007-how-a-todo-is-worked-travels-with-the-todo.md
[D-FBK-008]: feedback/fbk-008-one-todo-is-one-file-and-the-queue-is-in-the-names.md
[D-FBK-009]: feedback/fbk-009-a-todo-nobody-can-start-waits-where-it-says-why.md
[D-FBK-010]: feedback/fbk-010-main-carries-the-state-and-the-branch-carries-the-work.md
[D-FBK-001]: feedback/fbk-001-the-backlog-is-read-out-rather-than-enforced.md
[D-FBK-002]: feedback/fbk-002-the-order-of-the-work-is-declared-not-inferred.md
[D-FBK-004]: feedback/fbk-004-the-model-is-asked-because-nothing-else-can-say-it.md

### documentation

- [`D-DOC-034`][D-DOC-034] — A recording is answered from the checkout the command makes · 2026-08-18
- [`D-DOC-035`][D-DOC-035] — What the prose costs is counted beside how long a sentence is · 2026-08-18
- [`D-DOC-036`][D-DOC-036] — A todo serves a decision by its id · 2026-08-18
- [`D-DOC-033`][D-DOC-033] — The derived half of a tool page stays committed · 2026-08-14
- [`D-DOC-032`][D-DOC-032] — A section heading is the label a contents list shows · 2026-08-13
- [`D-DOC-024`][D-DOC-024] — The site's theme is a package, and this repository keeps none of it · 2026-08-12
- [`D-DOC-025`][D-DOC-025] — The documentation is four sections, and the bar carries those four · 2026-08-12
- [`D-DOC-026`][D-DOC-026] — The site is the documentation, and the readme stays out of it · 2026-08-12
- [`D-DOC-027`][D-DOC-027] — The renderer's configuration sits with the pages it renders · 2026-08-12
- [`D-DOC-028`][D-DOC-028] — The renderer is a build tool, and this repository carries none of it · 2026-08-12
- [`D-DOC-029`][D-DOC-029] — The documentation is reStructuredText, and the rest of the corpus is not · 2026-08-12
- [`D-DOC-030`][D-DOC-030] — The front page is a landing page, in the theme's marketing layout · 2026-08-12
- [`D-DOC-031`][D-DOC-031] — A page is railed under a label and headed by a sentence · 2026-08-12
- [`D-DOC-022`][D-DOC-022] — The reader picks the colours and the page remembers it · 2026-08-09
- [`D-DOC-017`][D-DOC-017] — The documentation is published from a copy this repository writes · 2026-08-06
- [`D-DOC-015`][D-DOC-015] — A renumber moves what a link path settles and names the rest · 2026-08-04
- [`D-DOC-016`][D-DOC-016] — An answer that reads no installation is derived and checked like the fields above it · 2026-08-04
- [`D-DOC-009`][D-DOC-009] — Prose names what counts rather than the count · 2026-08-03
- [`D-DOC-010`][D-DOC-010] — `targetVersion` opens with one sentence and diverges after it · 2026-08-03
- [`D-DOC-011`][D-DOC-011] — A schema is written as the shape it validates · 2026-08-03
- [`D-DOC-012`][D-DOC-012] — The second root is an installation this repository writes · 2026-08-03
- [`D-DOC-013`][D-DOC-013] — A commit here is three keywords and a condensed subject · 2026-08-03
- [`D-DOC-014`][D-DOC-014] — A working directory holds entries, and the documentation describes them · 2026-08-03
- [`D-DOC-003`][D-DOC-003] — A decision says what came back, and a requirement says what it rests on · 2026-08-02
- [`D-DOC-004`][D-DOC-004] — A requirement is written in the same sections as a decision · 2026-08-02
- [`D-DOC-005`][D-DOC-005] — A number is three digits so a group lists in order · 2026-08-02
- [`D-DOC-006`][D-DOC-006] — A recording says what it is of, and nothing fails on its age · 2026-08-02
- [`D-DOC-007`][D-DOC-007] — One page per tool, and the answer on it whole · 2026-08-02
- [`D-DOC-008`][D-DOC-008] — The calls that reach outside stay in the shared table · 2026-08-02
- [`D-DOC-001`][D-DOC-001] — A table is written so it reads unrendered · 2026-08-01
- [`D-DOC-002`][D-DOC-002] — The prose rule is measured, and only the lead fails on it · 2026-08-01

[D-DOC-034]: documentation/doc-034-a-recording-is-answered-from-the-checkout-the-command-makes.md
[D-DOC-035]: documentation/doc-035-what-the-prose-costs-is-counted-beside-how-long-a-sentence-is.md
[D-DOC-036]: documentation/doc-036-a-todo-serves-a-decision-by-its-id.md
[D-DOC-033]: documentation/doc-033-the-derived-half-of-a-tool-page-stays-committed.md
[D-DOC-032]: documentation/doc-032-a-section-heading-is-the-label-a-contents-list-shows.md
[D-DOC-024]: documentation/doc-024-the-sites-theme-is-a-package-and-this-repository-keeps-none-of-it.md
[D-DOC-025]: documentation/doc-025-the-documentation-is-four-sections-and-the-bar-carries-those-four.md
[D-DOC-026]: documentation/doc-026-the-site-is-the-documentation-and-the-readme-stays-out-of-it.md
[D-DOC-027]: documentation/doc-027-the-renderers-configuration-sits-with-the-pages-it-renders.md
[D-DOC-028]: documentation/doc-028-the-renderer-is-a-build-tool-and-this-repository-carries-none-of-it.md
[D-DOC-029]: documentation/doc-029-the-documentation-is-reStructuredText-and-the-rest-of-the-corpus-is-not.md
[D-DOC-030]: documentation/doc-030-the-front-page-is-a-landing-page-in-the-themes-marketing-layout.md
[D-DOC-031]: documentation/doc-031-a-page-is-railed-under-a-label-and-headed-by-a-sentence.md
[D-DOC-022]: documentation/doc-022-the-reader-picks-the-colours-and-the-page-remembers-it.md
[D-DOC-017]: documentation/doc-017-the-documentation-is-published-from-a-copy-this-repository-writes.md
[D-DOC-015]: documentation/doc-015-a-renumber-moves-what-a-link-path-settles-and-names-the-rest.md
[D-DOC-016]: documentation/doc-016-an-answer-that-reads-no-installation-is-derived-and-checked.md
[D-DOC-009]: documentation/doc-009-prose-names-what-counts-rather-than-the-count.md
[D-DOC-010]: documentation/doc-010-targetversion-opens-with-one-sentence-and-diverges-after-it.md
[D-DOC-011]: documentation/doc-011-a-schema-is-written-as-the-shape-it-validates.md
[D-DOC-012]: documentation/doc-012-the-second-root-is-an-installation-this-repository-writes.md
[D-DOC-013]: documentation/doc-013-a-commit-here-is-three-keywords-and-a-condensed-subject.md
[D-DOC-014]: documentation/doc-014-a-working-directory-holds-entries-and-the-documentation-describes-them.md
[D-DOC-003]: documentation/doc-003-a-decision-says-what-came-back-and-what-rests-on-it.md
[D-DOC-004]: documentation/doc-004-a-requirement-is-written-in-the-same-sections-as-a-decision.md
[D-DOC-005]: documentation/doc-005-a-number-is-three-digits-so-a-group-lists-in-order.md
[D-DOC-006]: documentation/doc-006-a-recording-says-what-it-is-of.md
[D-DOC-007]: documentation/doc-007-one-page-per-tool-and-the-answer-whole.md
[D-DOC-008]: documentation/doc-008-the-calls-that-reach-outside-stay-in-the-shared-table.md
[D-DOC-001]: documentation/doc-001-a-table-is-written-so-it-reads-unrendered.md
[D-DOC-002]: documentation/doc-002-the-prose-rule-is-measured-and-only-the-lead-fails.md

### code

- [`D-COD-004`][D-COD-004] — What leaves this process goes through one seam · 2026-08-03
- [`D-COD-003`][D-COD-003] — A directory is read through symfony/finder · 2026-08-02
- [`D-COD-001`][D-COD-001] — One file declares one class · 2026-08-01
- [`D-COD-002`][D-COD-002] — The upkeep CLI is a Symfony Console application · 2026-08-01

[D-COD-004]: code/cod-004-what-leaves-this-process-goes-through-one-seam.md
[D-COD-003]: code/cod-003-a-directory-is-read-through-symfony-finder.md
[D-COD-001]: code/cod-001-one-file-declares-one-class.md
[D-COD-002]: code/cod-002-the-upkeep-cli-is-a-symfony-console-application.md

### Revoked, and kept as the record

- [`D-ANS-081`][D-ANS-081] — A symptom is answered across the domain it was observed in · 2026-08-18 → D-ANS-084
- [`D-KNW-072`][D-KNW-072] — What makes a core change breaking when no PHP member moved is a gap this server owns · 2026-08-14 → D-KNW-073
- [`D-KNW-074`][D-KNW-074] — The shape a Record-sourced row has is a gap this server owns · 2026-08-14 → D-KNW-078
- [`D-SKL-040`][D-SKL-040] — A skill whose product is a report says the report is a file · 2026-08-14 → D-SKL-042
- [`D-DOC-018`][D-DOC-018] — The site opens on the readme and the map is a page below it · 2026-08-09 → D-DOC-026
- [`D-DOC-019`][D-DOC-019] — The site's stylesheet and script are built files, and what is solved is taken from a package · 2026-08-09 → D-DOC-024
- [`D-DOC-020`][D-DOC-020] — The site is rendered by one command that installs what it needs · 2026-08-09 → D-DOC-028
- [`D-DOC-021`][D-DOC-021] — The site is searched in a dialog opened with Ctrl-K · 2026-08-09 → D-DOC-024
- [`D-DOC-023`][D-DOC-023] — The site is built to the TYPO3 Support App design system · 2026-08-09 → D-DOC-024
- [`D-DIS-015`][D-DIS-015] — The installed entrypoint is named relatively wherever it exists · 2026-08-08 → D-DIS-016
- [`D-SKL-015`][D-SKL-015] — A step of the order is skipped only where it has already run or has nothing to find · 2026-08-04 → D-SKL-034
- [`D-FBK-037`][D-FBK-037] — API stability is worth a lookup and git state is not · 2026-08-03 → D-FBK-038
- [`D-KNW-040`][D-KNW-040] — What asserts a rendered output is a gap this server owns · 2026-08-03 → D-KNW-044
- [`D-SKL-011`][D-SKL-011] — The call plan a skill writes down is measured against the corpus that answers it · 2026-08-03 → D-SKL-043
- [`D-ANS-023`][D-ANS-023] — A ViewHelper question is answered by widening the manual index · 2026-08-02 → D-ANS-026
- [`D-ANS-027`][D-ANS-027] — The Extbase fork is placed where a caller who has not chosen passes · 2026-08-02 → D-ANS-039
- [`D-ANS-029`][D-ANS-029] — The scanner matcher is stated on the route a removal takes · 2026-08-02 → D-ANS-035
- [`D-DIS-008`][D-DIS-008] — The columns TYPO3 derives are reachable where the database server is · 2026-08-02 → D-DIS-012
- [`D-FBK-022`][D-FBK-022] — A feedback brings its card in the commit that brings it in · 2026-08-02 → D-FBK-045
- [`D-KNW-014`][D-KNW-014] — The record variable a v14 preview template is handed is a gap this server owns · 2026-08-02 → D-KNW-020
- [`D-KNW-015`][D-KNW-015] — The corpus states what a Fluid preview template replaces · 2026-08-02 → D-KNW-021
- [`D-KNW-025`][D-KNW-025] — What a backend preview owes the editor is a gap this server owns · 2026-08-02 → D-KNW-037
- [`D-FBK-005`][D-FBK-005] — The queue is worked before the pile is sighted · 2026-08-01 → D-FBK-012
- [`D-SCO-008`][D-SCO-008] — The path decides, and the answer may say it cannot · 2026-08-01 → D-KNW-005
- [`D-FBK-003`][D-FBK-003] — A session is handed one todo, not the file · 2026-07-31 → D-FBK-002
- [`D-KNW-003`][D-KNW-003] — `provenance` is not the third spelling of `binding`, and stays · 2026-07-30 → D-KNW-005
- [`D-ANS-001`][D-ANS-001] — The unanswered result keeps its shape and gains a reason · 2026-07-29 → D-ANS-005
- [`D-AUD-002`][D-AUD-002] — Two profiles, because a third one would have been the same set · 2026-07-29 → D-AUD-004
- [`D-CAT-002`][D-CAT-002] — The index of worked examples is curated, and existence is all that is checked · 2026-07-29
- [`D-DIS-002`][D-DIS-002] — Discovery honours the declared vendor-dir and bin-dir · 2026-07-29
- [`D-DIS-003`][D-DIS-003] — A label query is words, and the console is asked with a regex · 2026-07-29
- [`D-GUI-002`][D-GUI-002] — The commit workflow is asked for, not inferred · 2026-07-29 → D-GUI-010
- [`D-KNW-001`][D-KNW-001] — Sitepackage work is answered from the General category · 2026-07-29
- [`D-KNW-002`][D-KNW-002] — A hint about typo3/testing-framework is verified against tags, not against the checkouts · 2026-07-29
- [`D-SCO-001`][D-SCO-001] — Outside the core the core test guide declines rather than adapts · 2026-07-29
- [`D-SCO-004`][D-SCO-004] — The frontend is recognised by name, and only the two UI sections go · 2026-07-29
- [`D-VER-002`][D-VER-002] — The prose is not bound; it says which half it is · 2026-07-29 → D-VER-005

[D-ANS-081]: answers/ans-081-a-symptom-is-answered-across-the-domain-it-was-observed-in.md
[D-KNW-072]: knowledge/knw-072-what-makes-a-core-change-breaking-when-no-php-member-moved-is-a-gap-this-server-owns.md
[D-KNW-074]: knowledge/knw-074-the-shape-a-record-sourced-row-has-is-a-gap-this-server-owns.md
[D-SKL-040]: task-skills/skl-040-a-skill-whose-product-is-a-report-says-the-report-is-a-file.md
[D-DOC-018]: documentation/doc-018-the-site-opens-on-the-readme-and-the-map-is-a-page-below-it.md
[D-DOC-019]: documentation/doc-019-the-sites-stylesheet-and-script-are-built-files-and-what-is-solved-is-taken-from-a-package.md
[D-DOC-020]: documentation/doc-020-the-site-is-rendered-by-one-command-that-installs-what-it-needs.md
[D-DOC-021]: documentation/doc-021-the-site-is-searched-in-a-dialog-opened-with-ctrl-k.md
[D-DOC-023]: documentation/doc-023-the-site-is-built-to-the-typo3-support-app-design-system.md
[D-DIS-015]: discovery/dis-015-the-installed-entrypoint-is-named-relatively-wherever-it-exists.md
[D-SKL-015]: task-skills/skl-015-a-step-of-the-order-is-skipped-only-where-it-has-already-run-or-has-nothing-to-find.md
[D-FBK-037]: feedback/fbk-037-api-stability-is-worth-a-lookup-and-git-state-is-not.md
[D-KNW-040]: knowledge/knw-040-what-asserts-a-rendered-output-is-a-gap-this-server-owns.md
[D-SKL-011]: task-skills/skl-011-the-call-plan-a-skill-writes-down-is-measured-against-the-corpus-that-answers-it.md
[D-ANS-023]: answers/ans-023-a-viewhelper-question-is-answered-by-widening-the-index.md
[D-ANS-027]: answers/ans-027-the-extbase-fork-is-placed-where-the-undecided-caller-passes.md
[D-ANS-029]: answers/ans-029-the-scanner-matcher-is-stated-on-the-route-a-removal-takes.md
[D-DIS-008]: discovery/dis-008-the-columns-typo3-derives-are-reachable-where-the-database-is.md
[D-FBK-022]: feedback/fbk-022-a-feedback-brings-its-card-in-the-commit-that-brings-it-in.md
[D-KNW-014]: knowledge/knw-014-the-record-variable-a-v14-preview-template-is-handed-is-a-gap-this-server-owns.md
[D-KNW-015]: knowledge/knw-015-the-corpus-states-what-a-fluid-preview-template-replaces.md
[D-KNW-025]: knowledge/knw-025-what-a-backend-preview-owes-the-editor-is-a-gap-this-server-owns.md
[D-FBK-005]: feedback/fbk-005-the-queue-is-worked-before-the-pile-is-sighted.md
[D-SCO-008]: scope/sco-008-the-path-decides-and-the-answer-may-say-it-cannot.md
[D-FBK-003]: feedback/fbk-003-a-session-is-handed-one-todo-not-the-file.md
[D-KNW-003]: knowledge/knw-003-provenance-is-not-the-third-spelling-of-binding.md
[D-ANS-001]: answers/ans-001-the-unanswered-result-keeps-its-shape-and-gains-a-reason.md
[D-AUD-002]: audience/aud-002-two-profiles-because-a-third-would-have-been-the-same-set.md
[D-CAT-002]: catalog/cat-002-the-index-of-worked-examples-is-curated.md
[D-DIS-002]: discovery/dis-002-discovery-honours-the-declared-vendor-dir-and-bin-dir.md
[D-DIS-003]: discovery/dis-003-a-label-query-is-words-and-the-console-is-asked-with-a-regex.md
[D-GUI-002]: guides/gui-002-the-commit-workflow-is-asked-for-not-inferred.md
[D-KNW-001]: knowledge/knw-001-sitepackage-work-is-answered-from-the-general-category.md
[D-KNW-002]: knowledge/knw-002-a-hint-about-typo3-testing-framework-is-verified-against-tags.md
[D-SCO-001]: scope/sco-001-outside-the-core-the-core-test-guide-declines-rather-than-adapts.md
[D-SCO-004]: scope/sco-004-the-frontend-is-recognised-by-name-and-only-the-two-ui-sections-go.md
[D-VER-002]: versions/ver-002-the-prose-is-not-bound-it-says-which-half-it-is.md

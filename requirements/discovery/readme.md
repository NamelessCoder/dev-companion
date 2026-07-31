# Discovery — which installation is read, and how

What the server may conclude about the checkout it was started in, how it is
found, and how a client is set up to reach it. A layout that cannot be read is
a one-line fix for the user; five tools going quiet is not.

See [the requirements readme](../readme.md) for how an entry is written and
when it is added.

| Id | What must hold | State |
| --- | --- | --- |
| [`R-DIS-1`](dis-1-discovery-belongs-to-the-stdio-entrypoint-alone.md) | Discovery belongs to the stdio entrypoint alone | held |
| [`R-DIS-2`](dis-2-the-packages-are-read-from-the-declared-vendor-directory.md) | The packages are read from the declared vendor directory | held |
| [`R-DIS-3`](dis-3-the-console-is-looked-for-where-the-installation-declares-it.md) | The console is looked for where the installation declares it | held |
| [`R-DIS-4`](dis-4-the-extension-being-worked-on-is-part-of-its-own-installation.md) | The extension being worked on is part of its own installation | held |
| [`R-DIS-5`](dis-5-a-repository-with-no-installation-around-it-is-not-one.md) | A repository with no installation around it is not one | held |
| [`R-DIS-6`](dis-6-nothing-is-started-as-a-side-effect-of-a-lookup.md) | Nothing is started as a side effect of a lookup | held |
| [`R-DIS-7`](dis-7-the-installation-and-the-console-can-be-named-outright.md) | The installation and the console can be named outright | held |
| [`R-DIS-8`](dis-8-a-failed-discovery-names-where-it-looked.md) | A failed discovery names where it looked | held |
| [`R-DIS-9`](dis-9-a-negative-is-never-remembered.md) | A negative is never remembered | held |
| [`R-DIS-10`](dis-10-reachable-and-ready-are-two-questions.md) | Reachable and ready are two questions | held |
| [`R-DIS-11`](dis-11-the-entrypoint-installs-its-own-client-configuration.md) | The entrypoint installs its own client configuration | held |
| [`R-DIS-12`](dis-12-codex-setup-installs-the-server-and-its-skills.md) | Codex setup installs the server and its skills | held |
| [`R-DIS-13`](dis-13-which-agent-clients-can-be-installed-into.md) | Which agent clients can be installed into | held |
| [`R-DIS-14`](dis-14-an-installed-skill-is-a-workflow-not-a-prompt-fragment.md) | An installed skill is a workflow, not a prompt fragment | held |
| [`R-DIS-15`](dis-15-the-ddev-client-entry-names-an-entrypoint-that-exists.md) | The DDEV client entry names an entrypoint that exists | held |
| [`R-DIS-16`](dis-16-a-repository-that-serves-two-majors-is-answered-for-both.md) | A repository that serves two majors is answered for both | held |
| [`R-DIS-17`](dis-17-an-extension-below-tests-is-the-test-setups.md) | An extension below Tests/ is the test setup's | held |
| [`R-DIS-18`](dis-18-a-console-command-never-inherits-the-clients-stdin.md) | A console command never inherits the client's stdin | held |
| [`R-DIS-19`](dis-19-a-registry-with-no-command-is-answered-by-the-installation-itself.md) | A registry with no command is answered by the installation itself | held |
| [`R-DIS-20`](dis-20-the-project-records-which-clients-are-installed-in-it.md) | The project records which clients are installed in it | held |

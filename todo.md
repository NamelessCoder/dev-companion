# What is being worked on

This file exists so a session can end anywhere. It holds the **order of the
work** and where the last one stopped — not what must be true (that is
[requirements.md](requirements.md)), not the questions real sessions asked (that
is `feedback/`), not the map of what the audiences need (that is `scenarios/`).
Those three outlive the work; this one is consumed by it.

Rules that keep it from becoming a fourth backlog:

- An item names what it serves — a requirement, a note, a scenario. An item that
  serves nothing is not a task, it is an idea, and ideas go in the note that had
  them.
- An item says what the **next concrete step** is, in enough detail that someone
  who has read nothing else can start. "Continue with the bindings" is not that;
  "bind the statements in `php.json` against `.checkouts/12.4` and `13.4`" is.
- A finished item is deleted, not ticked. What it established is already in
  `requirements.md`, and the commit is the record that it happened.
- The order is the order. When something jumps the queue, it moves up here
  first, so the reason is written down before the work starts.

---

## 1. Re-read the four open notes against what now exists

**Serves:** `feedback/` · **Next step:** run each note's own query against the
current server and trim what has been answered.

Two of them (`094054`, `100314`) asked for a project mode that now partly
exists: `typo3_project_scope` reads the sites, the extensions and the commands,
`typo3_changelog_lookup` reads what a version changed. `100640` asked for the
profile, which is item 2. `094245` asked for `targetVersion`, which now exists
on `typo3_architecture_lookup` and `typo3_task_guide` but not on the catalog
tools.

## 2. Profile: show a project only the half that fits it

**Serves:** `feedback/2026-07-29-100640`, R-AUD-1 · **Decided:** derived from the
installation, `TYPO3_MCP_PROFILE` overrides · **Next step:** a `Profile` class
that resolves `core` / `project` / `all` from `Instance::describe()['kind']` and
the variable, and a filter in `Tools::definitions()`.

The tool list already varies by environment — `Feedback::isAvailable()` hides
two tools outside a standalone checkout — so the mechanism exists and the
precedent with it. What has to be decided per tool is which profile it belongs
to; the `provenance` field in `knowledge/server-scope.json` already says
`core-only`, `transferable` or `installation` per topic, and that is the input.

Keep `typo3_server_scope` in every profile, and have it name the active one.
A client that sees a shorter list must be able to find out why.

## 3. Bind the rest of the knowledge to versions

**Serves:** R-AUD-4 · **Next step:** go file by file through
`knowledge/architecture-hints/`, and for every statement that names an API,
check it against `.checkouts/12.4` and `.checkouts/13.4` — those are where the
current shape usually is not.

About fifteen statements are bound so far. Everything else is implicitly "holds
on all four covered lines", and that is not true everywhere: the TCA Schema API,
the `#[AsEventListener]` attribute, the `.fluid.html` extension, the site set
label domain, the DataHandler event names and the backend module registration
all changed inside the covered range.

Order by how much a wrong answer costs: PHP first (the APIs), then Fluid, then
TypoScript, then the general hints. The prose documents below `knowledge/*.md`
have no binding mechanism at all yet — decide whether they need one or whether
the hints carry it.

## 4. A maintenance guide, once the pieces are there

**Serves:** `feedback/2026-07-29-100314`, scenario `SITE-02` · **Next step:**
none yet — this waits until items 2 and 3 are done, because it composes them.

The upgrade question is "what do I do, in which order, and what breaks". Three
of the four inputs exist now: `typo3_project_scope` knows what is installed,
`typo3_changelog_lookup` knows what changed, the console knows `upgrade:list`.
What is missing is the order of operations, and that is knowledge to be written
rather than a tool to be built — it belongs in `knowledge/` first.

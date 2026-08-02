# Let a hint declare project or extension, not only core or nothing

**Serves:** R-AUD-1, R-AUD-5, decisions/

`D-KNW-5` gave the corpus one vocabulary and the enum has five cases, but the
hints still only ever write one of them. Every `scope` in
`knowledge/architecture-hints/*.json` and `knowledge/task-intents.json` is
`core`, and absent means `any` — so a statement can say "this obliges a core
patch" and cannot say "this is how a project does it" or "this is an extension
author's problem". `Scope::ofKnowledge()` offers `project` and `extension`;
nothing writes them.

The corpus already draws the line and draws it as prose. `core-tests` and
`project-extension-tests` are two hints in `php.json` whose difference is a
sentence — "the conventions of a core test hold here … but the harness they run
in does not exist yet" — and `project-repository-layout` and
`extension-repository-layout` are two more in `general.json`. Splitting a hint
is how the corpus says what a field could say, and a hint split that way is
matched, ranked and filtered as two.

The step is to settle whether those pairs stay two hints with a `scope` each, or
become one hint whose statements carry their own — the shape `R-AUD-5` already
uses for the single changelog sentence inside `fluid-viewhelpers`. Then decide
whether `Scope::of()` should read a declared scope at all: it places a path
today, and a hint that declares `project` is a statement about the answer rather
than about the caller's file, which is the distinction `any` and `uncertain`
already keep apart. Do not re-derive that the three audiences exist or that the
vocabulary is one: `R-AUD-1` and `D-KNW-5` say so and both are held.

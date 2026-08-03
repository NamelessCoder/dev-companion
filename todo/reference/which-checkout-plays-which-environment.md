# Which checkout plays which environment

The standing answer to a question every run asks first. A scenario names a kind
of directory; which one on this machine plays it belongs here, where it can go
stale without taking a case with it. A forward run is a fresh MCP client session
with the installed skills, and a session in this repository may neither activate
those skills nor grade its own implementation as behavioral evidence.

Two of them are no longer a machine's business at all.
`bin/cli environment:create` makes `E-SITE` and `E-NONE` below `.environments/`,
so a case that needs an installation to answer from needs nothing off this page
— `bin/cli environment:status` says what this checkout has. What stays here is
what a scaffold cannot produce: the site package `REVIEW-01` reviews, the three
extension checkouts whose real infrastructure is what they play (`D-EVI-004`),
and the core checkout somebody works in, which is where a patch to review comes
from.

- **`E-CORE`** — `/home/benji/projects/typo3-cms`, the core repository itself:
  `main` at TYPO3 15.0.0-dev, PHP `^8.5` declared and 8.5 in DDEV, no extensions
  and no sites. The server is not a dependency there, so it is installed from
  this checkout the way `E-EXT` is — done on 2026-08-03, when the published
  copies were a day behind the skills here. **It is worked in**, which is what
  it plays: `origin/main..main` carried an unpushed core patch and
  `.claude/worktrees/` a branch with more when `REVIEW-03` was unblocked on
  2026-08-03, so that review finds a diff without one being made for it.
  `git status` there is never empty — the generated `.gitignore` block is
  uncommitted, `.claude/` and `opencode.json` are untracked — and a run notes
  that before it starts rather than reading it afterwards as a session that
  wrote. The DDEV project was paused on 2026-08-02, so a runtime lookup answers
  unsupported until it is started. A GPT-5 mini session reviewed the GD/SVG
  placeholder patch here on 2026-08-01 (`feedback/2026-08-01-114526`): it was
  given a subsystem task rather than the scenario prompt, so it is precedent for
  the environment and not a run.
- **`E-SITE`** — `/home/benji/projects/site-new`, site package below
  `extensions/printworks_sitepackage`, TYPO3 14.3.5 under DDEV. The server is a
  dependency there: refresh the skills with
  `ddev exec php vendor/bin/typo3-cms-mcp update --agent=claude`. Its tree is
  clean as of 2026-08-02, at `e7f3f05` — the `.gitignore` modification noted
  here before was committed in `1523751`. `/var/` is gitignored there, so a run
  that boots the installation still leaves `git status` empty.
- **`E-EXT`** — two checkouts play it, and which one a run needs is a property
  of the run. In both the server is **not** a Composer dependency, so it is
  reached from this checkout:
  `php /home/benji/projects/typo3-cms-mcp/bin/typo3-cms-mcp install --agent=claude`
  from the project root publishes the skills and writes the host-php
  `.mcp.json`. Repeat it after any skill change — the published skills are a
  copy and nothing reports it when they are older than the server. The generated
  ignore block in each `.gitignore` and the untracked `.mcp.json` are from that
  install and stay.
  - `/home/benji/projects/syntax` — `bk2k/syntax` 5.0.0, TYPO3 14.3.0 below
    `.build/vendor`, DDEV project `syntax` on PHP 8.2, declared
    `^13.4 || ^14.3`. **Static quality infrastructure is incomplete**:
    php-cs-fixer and phplint in CI, no PHPStan, no `Tests/` at all. `REVIEW-02`
    ran here twice on 2026-07-31, `covered` at 12:21 and `covered` again at
    13:32 — the second against the server that runs the checks, and the first
    run of any kind to reach the console half from an extension checkout.
  - `/home/benji/projects/bootstrap_package` — TYPO3 14.3.0 below
    `.build/vendor`, DDEV project `bootstrap-package` on PHP 8.5. **Complete**
    infrastructure, which is what it plays. `REVIEW-02` ran here twice on
    2026-07-31, `partial` at 02:55 and `covered` at 08:15 after the corrections
    that run earned.
  - `/home/benji/projects/news` — `georgringer/news` 13.0.2 at `3fe278a2`, TYPO3
    **13.4.33** below `.Build/vendor` (capital B), host PHP 8.3, no DDEV. **A
    major behind the world**, which is what it plays: it declares `^12.4.37
    || ^13.4.15` on PHP `>= 8.1 < 8.5` while 14 is out, so the declared range
    does real work in a run instead of being quoted. It owns
    `Build/Scripts/runTests.sh`, two per-major workflows, 30 test classes and a
    `Documentation/` tree, and at 132 classes it is the only checkout here
    large enough that a review has to choose what to open. Cloned
    `--single-branch` **on purpose**: `origin/main` carries the finished v14
    migration, and the checkout that plays this environment for `EXT-01` must
    not hand that answer over with one `git log` — fetching another branch into
    it ends its usefulness for that scenario. It is also the one checkout here
    that carries a **correct escaping opt-out**: six
    `<f:format.htmlentitiesDecode>` around `{newsItem.title}` and
    `{newsItem.alternativeTitle}` in `Detail.html` and its two `Styles/`
    copies, all inside `<n:titleTag>`, whose `TitleTagViewHelper` returns
    nothing and hands the rendered children to `NewsTitleProvider`; the
    installed 13.4.33 core puts the resolved title through `htmlspecialchars()`
    into `<title>|</title>` in `PageRenderer`. That shape is what `SKILL-09`
    needs, so it is the checkout that case is read in. `REVIEW-02` ran here on
    2026-07-31, `partial` at 14:23; a first attempt at 14:02 on the `12-13`
    branch was discarded rather than judged, because that branch is 185 commits
    behind `13.x` and 0 ahead, and the run spent its top finding saying so.

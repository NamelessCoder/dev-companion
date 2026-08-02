# Say that ext_localconf.php was not read either

**Serves:** feedback/2026-07-31-174526-stale-registration-detection-gap-ext-localconf.md
**Priority:** normal

Settle what `notReadStatically` claims, then make both places that state it say
the same thing. `Extension::notReadStatically()` draws from five declarative
files — `Modules.php`, `Routes.php`, `AjaxRoutes.php`, `RequestMiddlewares.php`
and, where the container did not answer, `Icons.php` — and `ext_localconf.php`
is never among them. For `bootstrap_package` it therefore answers `[]`, while
that file registers two `FE/contentRenderingTemplates` entries, the global Fluid
prefix `bk2k`, an RTE preset, a `formEngine` node and two `SC_OPTIONS` hooks,
none of which are in any list the answer carries. Both sentences a reader has
over-claim: the schema's `an empty list here means every file that exists was
read`, and the rendered caveat, which excludes registrations made
`with a PHP call` where these are plain assignments into `$GLOBALS`. So decide
whether `ext_localconf.php` joins the list — it does assemble nothing at
runtime, which is what the field currently means — or whether the field keeps
its five files and the two sentences are narrowed to what they actually say.
This touches `src/` and a declared `outputSchema`, which is why it is queued.
`D-ANS-003` carries the judgement; `D-ANS-008` is the same shape one field over.

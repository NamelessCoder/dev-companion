# No hint says what backend TypeScript may know about the server, and what must stay in PHP

**Serves:** feedback/2026-08-24-100400-no-hint-says-what-backend-typescript-may-know.md
**Priority:** normal
**Branch:** todo/no-hint-says-what-backend-typescript-may-know
**Claimed:** 2026-08-24

Judged as step 1a on 2026-08-24 and taken on as a hint of its own: `D-KNW-107`
carries the evidence, and the reading is what is left. Read `.checkouts/` for
backend modules besides `ext:form` where the client submits what the editor
chose and the controller resolves the resource behind it — the `Classes/` side
of the modules whose sources sit under `Build/Sources/TypeScript/` — and settle
whether the boundary is a core convention or a fact about the form wizard. Where
it is a convention, write the hint against the majors it holds on, with an
`appliesTo` that matches a `Classes/` path as well as a TypeScript one, and
state it as the distinction the checkout supports rather than the feedback's
"backend TypeScript must not hold an `EXT:` path", which `form-manager.ts`
contradicts on `main`. Where it is not, the fact belongs to `form-framework` and
`D-KNW-107` gets the **Wrong if** it went to.

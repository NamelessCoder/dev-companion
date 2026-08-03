# Decide whether `scope` is one word or two in this server

**Serves:** decisions/
**Priority:** normal
**Waiting on:** rename the verb, rename the enum, or state the overlap and keep
    it? The enum is one vocabulary the whole server stands on and the verb is
    four tool names, so the cheaper rename is not the better one by default.
    Keeping it is a real answer, and then the overlap belongs in the tool
    descriptions rather than in nobody's head.

`scope` names two things here and both go out on the wire.
[`Knowledge\Scope`](../../src/Knowledge/Scope.php) is the one vocabulary for
which kind of work an answer is for — `core`, `project`, `extension`, `any`,
`uncertain` — and `scope` is also one of the five verbs a tool name may end in,
where it means "states what a source covers". Four tools carry that verb:
`typo3_server_scope`, `typo3_catalog_scope`, `typo3_project_scope` and
`typo3_extension_scope`. The last two read as cases of the enum and are not one
— they describe the project and the extension being worked in, which is the verb
sense — so a client is handed two tool names that look like a vocabulary it also
receives inside the answers, and nothing tells it they are unrelated. Decide in
`decisions/` whether the verb is renamed, the enum is, or the overlap is stated
and kept, and settle it before the namespace card is worked: both change tool
names, and one rename costs less than two.

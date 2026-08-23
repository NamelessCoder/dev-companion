# Judge what nothing has answered for

**Serves:** requirements/, decisions/
**Every:** session
**Run:** bin/cli unresolved:list

Give every entry the listing marks as unnamed one of two answers: a todo below
that takes it on, or a `judged:` date in its front matter, which says a session
read it and left it as it is. The reason goes where the entry already keeps it —
the sentence **Held by** owes, or the decision it rests on — and the date is
what stops the next session deriving the same judgement again. Nothing here is
checked or built while judging. Three states mean unfinished and none of them
can make a check fail — a requirement marked **open**, one held by
`not guarded`, a decision still `open` whose **Wrong if** nobody has been back
to — which is why they went unread for as long as they existed, and why a
requirement sat there unbuilt from the day the directory was created. A
requirement no test can hold is a legitimate answer; one nobody has judged is
not.

The decisions this asks about are the ones no test declares. An entry a test
holds is read by whoever makes that test fail, because the failure prints it —
`D-DOC-054` — so the listing names the oldest of the rest and this todo is
pointed at those.

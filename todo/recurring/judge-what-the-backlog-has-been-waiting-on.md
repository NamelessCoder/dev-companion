# Judge what the backlog has been waiting on

**Serves:** requirements/, decisions/
**Every:** session
**Run:** bin/cli backlog:list

Give every entry the listing marks as unnamed one of two answers: a todo below
that takes it on, or the sentence in `decisions/` that says why it stays as it
is. Nothing here is checked or built while judging. Three states mean unfinished
and none of them can make a check fail — a requirement marked **open**, one held
by `not guarded`, a decision still `open` whose **Wrong if** nobody has been
back to — which is why they went unread for as long as they existed, and why a
requirement sat there unbuilt from the day the directory was created. A
requirement no test can hold is a legitimate answer; one nobody has judged is
not.

# Make a priority a field every todo carries

**Serves:** todo/
**Priority:** high

Give every todo in a stage a `**Priority:**`, make `bin/cli todo:check` say so
where one is missing, and have `bin/cli todo:sync` write `low` on the card it
creates. That takes the fourth state out of `D-FBK-015` — absence meaning
nobody has judged this yet — and the reason is that an optional field cannot be
checked: a priority somebody forgot looks exactly like one deliberately left
off, and nothing can tell them apart. What a card is for is readable without
the trick, because a judging card is the one that serves a `feedback/` file.
`Todo::rank()` then has three cases rather than four, the `unjudged` line in
`bin/cli todo:list` goes, and `QueuedTodo::queueATodo()` writes a priority like
everything else. The 67 cards on the board are rewritten in the same commit,
which is `todo:sync` run again after they are deleted rather than a hand edit.
`D-FBK-015` gains a **Since then** saying which half went and why, and
`R-FBK-007` still holds: `low` below everything judged is where an unjudged card
sat before.

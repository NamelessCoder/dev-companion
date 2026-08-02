# Make every stage in `todo/` a directory of its own

**Serves:** todo/

Move the queued todos into `todo/open/`, so that no todo sits loose at the root
and where a file sits says which stage it is in for all of them alike. Take
`todo/reference/` out of the store in the same move: what a session would
otherwise rediscover is documentation, not work in a stage, and it belongs
below `documentation/`. `Todo::read()` resolves the kind from where a file
sits, so the queue's kind becomes `open/` rather than the root, and
`Todo::items()`, the `queue` branch of `bin/cli todo:check` and the paths in
`todo/readme.md` follow it. Closing stays a deletion and does not become a
stage: the commit that finishes a todo is its record, and a done pile would be
a second thing to keep true — the archive under `feedback/` is the one
exception, and it is one because the agent asking what became of its report has
no git. Write the decision entry with the commit, and let it carry the six
transitions and why `closed` is not among the places.

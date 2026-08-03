# What this server has to do

The working directory: one file per requirement, in the group its id names, and
a listing at the foot of each group's own `readme.md` written by
`bin/cli requirements:index`.

What a requirement is, where an entry goes and how one is written:
[documentation/requirements/writing-a-requirement.md](../documentation/requirements/writing-a-requirement.md).
`bin/cli requirements:check` holds every file to that shape, and
`bin/cli backlog:list` reads out the ones nothing answers for.

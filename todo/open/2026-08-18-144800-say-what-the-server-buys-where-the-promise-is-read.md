# Say what the server buys, where the promise is read

**Serves:** documentation/, decisions/
**Priority:** normal

A comparison of sessions run with this server against sessions run without it
found the median cost of a lookup task roughly unchanged and the spread
collapsed: on the widest task the runs without it ranged 37-fold from the
cheapest to the dearest and the runs with it 1.4-fold, while the turn count fell
by two fifths and the wall time by half. `documentation/readme.rst` promises
knowledge, version binding and skills, and says nothing about the shape of the
saving, so somebody weighing whether to install it guesses at one. Write the
measurement into a decision — what it covers, that it is one project, one model
and five runs per cell, that this repository cannot re-run it, and that a second
project reporting a lower median instead would show the framing wrong — and put
the one sentence it earns into the readme: a predictable ceiling rather than a
lower average.

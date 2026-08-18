# Reach the four task shapes a contract case is written in

**Serves:** scenarios/contracts/, D-GUI-015
**Priority:** normal

Judged on 2026-08-19 while reading the contract cases nothing guards, and
written up in `D-GUI-015`: four cases are held by tests that feed a brief naming
the answer, and the prompt each case carries reaches less than that brief. The
measurement is in the entry, and each case now says what was measured against
it.

Take `SKILL-07` first, because it is the only wrong answer among the four. Its
prompt asks for a backend module "for reviewing imported records" and the
`audit` intent fires on `reviewing`, so the guide hands back
`typo3-extension-conformance`. Establish whether `audit` should see a word
inside the subject a module is built for at all, and what that costs the audit
requests it is there to catch.

Then the three silences, one at a time, each measured before and after with the
case's own prompt:

- `EXT-08` — changing what the core does without overriding the class that does
  it. The `event-listener` intent's needles are the mechanism; the prompt names
  the goal.
- `SKILL-11` — a security review in a maintainer's words. Nothing reaches
  `typo3-extension-conformance`, which the case is written about.
- `SITE-09` — `site-setting` is detected and not confirmed, so the guide names
  no skill. Read what raises a detection to `confirmed` before touching the
  needles.

`D-SKL-013` is the standing warning to read against: a needle that reaches two
intents is a false route, so each of the four is measured on its own and on the
neighbour it could steal from. No test asserts the arrival until the needles are
curated, per the entry's fourth **Decided**.

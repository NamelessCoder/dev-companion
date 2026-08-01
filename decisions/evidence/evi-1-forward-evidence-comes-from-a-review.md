---
id: D-EVI-1
date: 2026-07-31
status: standing
---

# D-EVI-1 — Forward evidence comes from a review, not from a prompt that knows the answer

**Only an open review that names the working context and the user's intent is
recorded as forward evidence; everything that names a task shape is a targeted
contract case.**

The scenario suite was written to describe what the three audiences need, and it
was then also used as the evidence that an agent finds those needs on its own.
Those are two different tests, and one file shape hid it.

- **Evidence:** every one of the 32 prompts named its own subsystem, and several
  named the implementation — a status list with a refresh action, a carousel with
  inline children. The one recorded forward run, `EXT-04` on 2026-07-30, met all
  five criteria it was judged against and produced six defects none of them
  measured, which is what a prompt that already contains the answer buys. Five
  prompts additionally named one project on one machine, so nobody else could
  run them.
- **Decided:** the suite splits. Three open forward reviews — site project,
  reusable extension, core patch — name the working context and the user's
  intent and nothing else, and only these are recorded. Everything that names a
  task shape becomes a targeted contract case: still readable, still printable,
  never forward evidence. One case is one file, and an environment is a kind of
  working directory rather than someone's checkout.
- **Wrong if:** two consecutive review runs produce findings too diffuse to tie
  back to a requirement or a feedback — then the broad prompt measures the model's
  taste rather than this server. Or the contract cases quietly stop being read
  because nothing schedules them, and the routing they hold rots while the three
  reviews stay green.

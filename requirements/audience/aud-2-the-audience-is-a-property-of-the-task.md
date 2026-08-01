---
id: R-AUD-2
status: open
---

# R-AUD-2 — The audience is a property of the task

**The audience is a property of the task, not of the directory.**

Extensions are routinely developed inside a site installation, a site package
is an extension, and a core checkout can be the place someone debugs their
site. Signals are combined, and where they disagree the answer says the
audience is uncertain instead of picking one silently.

**Held by:** not guarded, and nothing can hold it yet — `Scope::isOutsideCore()`
combines the signals over the whole call and returns a boolean, so neither the
per-path decision nor the uncertain answer exists to be tested. `META-03` is the
case that would hold both ([`D-SCO-7`](../../decisions/scope/sco-7-the-signals-are-combined-per-call-and-a-call-is-not-a-path.md)).

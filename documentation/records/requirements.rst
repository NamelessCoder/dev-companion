What a requirement is
=====================

One thing that must be true of this server, with where the demand came from and
what holds it to that — a test where there is one, ``not guarded`` where there
is none.

It has to keep holding while everything around it changes. That is what makes it
worth a file of its own rather than a sentence in the change that established
it: the change is finished, the demand is not.

This is the base. What a change rested on and what would show it wrong is a
decision, and a requirement names the ones it stands on in its own ``restsOn:``
— :doc:`what a decision is <decisions>`. Where an entry goes and how it is
written is :doc:`writing-a-requirement.md <writing-a-requirement>`.

A feedback is one route a demand arrives by, and the most common one. It is not
what this directory is for: an entry is read long after the question that
produced it was answered, and a demand arrives as readily from a review, from a
recorded run, or from a reading that found nothing holding a rule everybody
assumed.

What the state means
--------------------

``status`` is ``held`` or ``open``, and a third state is derived from them:

* **open** — accepted and not built yet. It is read out beside **not guarded**
  rather than in a list of its own, because a requirement nobody has implemented
  and one that could silently regress are the same kind of thing.
* **not guarded** — built, and **Held by** names no test. It is never written in
  the front matter: an entry may not claim it of itself, and it is what a claim
  of ``held`` turns out to be. It is the honest answer for a requirement no test
  can hold, and it is the one worth seeing in a listing, because from afar it
  looks exactly like ``held``.
* **held** — built, and the tests it names hold it there.

They are the ``RequirementState`` enum. ``bin/cli requirements:check`` cannot
fail on **open** or **not guarded**; both are legitimate, and
:doc:`bin/cli unresolved:list <index>` reads them out instead, together with
whether a todo names the id. Nothing in ``requirements/`` reaches the order of
the work on its own; that listing is the whole of the coupling.

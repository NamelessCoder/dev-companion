.. _typo3_gerrit_lookup:

``typo3_gerrit_lookup``
=======================

Find out whether a TYPO3 core patch already exists and what state its review is
in, from the review server at review.typo3.org. Pass issue with a Forge issue
number to search the commit messages of every change for it — the question "has
somebody already fixed this" — or change with the Change-Id from a commit
message, or the change number a review URL ends with, to read the one it names.
Answers with the change number, its Change-Id, subject, status, target branch,
review URL, and the patch set that is current on the server with the commit it
is — which is what says whether a checkout is the revision under review. A
change is answered together with the changes sharing its Change-Id, whichever
handle named it — that is how a backport on a release branch is reached. Each
change also carries the ref that patch set is fetchable by and the review server
to fetch it over, so getting it into a checkout takes no second lookup. A change
read by name carries the review it is in as well: the value every voter holds
per label and whether the submit rule is satisfied, and every comment left on it
with its patch set, its file and line, whether the thread is unresolved and
which comment it replies to. That is where a comment somebody left on an earlier
patch set and nobody answered is read. Why a vote is gone is in the review log
instead, which messages asks for. A call carries issue or change, never both.
This reaches the network, and it reads: reviewing, voting and uploading stay
yours. Answers from: network.

``readOnlyHint: true`` · ``destructiveHint: false`` · ``idempotentHint: true`` · ``openWorldHint: true``

Answers from :ref:`network <answer-sources-network>`.

Takes
-----

.. code-block:: yaml

    # Forge issue number, with or without the leading #, for example "105403".
    # Searches every change whose commit message names it, which is where Resolves:
    # and Related: put it. A call carries issue or change, never both.
    issue: string  # optional
    # One change to read, named either by the Change-Id its commit message carries,
    # for example "I0f4c5b9a3e2d1c7b8a6f5e4d3c2b1a0f9e8d7c6b", or by the change
    # number a review URL ends with, for example "89011". Prefer the Change-Id where
    # the commit is in front of you: it is part of the patch being read, it survives
    # being amended into a new patch set, and it cannot be mistaken for the Forge
    # issue number the way a bare change number can. A call carries issue or change,
    # never both.
    change: string  # optional
    # One of: none, people, all. The review log of a change: every message its patch
    # sets and its reviewers left. Ask for it to find out why a vote is gone —
    # Gerrit writes "Outdated Votes: * Code-Review+1 (copy condition: ...)" into the
    # message of the upload that dropped it, and the labels afterwards look exactly
    # like a change nobody has voted on. "none" leaves it out and is the default,
    # since it is 57.9 KB against 14.3 KB on a change with 21 patch sets. "people"
    # drops what a service user wrote, which on that change is 20 of 46 messages and
    # every one of them a CI pipeline report. "all" keeps them. How many were
    # dropped is answered whichever you ask for. Narrows change and is ignored by
    # issue.
    messages: string  # optional
    limit: integer  # optional

The call carries exactly one of these sets of arguments: ``issue`` — or
``change``.

Answers with
------------

.. code-block:: yaml

    # One of: answered, empty, unavailable.
    status: string
    # The review server the answer came from.
    source: string
    # The Gerrit query this was answered with, so the same question can be asked
    # again by hand.
    query: string
    # The changes that matched, newest activity first.
    changes:
      - # Change number, the digits its review URL ends with.
        number: integer  # optional
        # The Change-Id its commit message carries, empty where the server named
        # none. It survives an amend and a rebase onto another branch, so it is what
        # to hold the commit in front of you against — and changes sharing one are
        # the same patch on more than one branch, which passing it back as change
        # reads all of.
        changeId: string  # optional
        # The commit subject.
        subject: string  # optional
        # NEW while it is open, MERGED once it landed, ABANDONED when it was given
        # up.
        status: string  # optional
        # The branch the change targets.
        branch: string  # optional
        # The patch set that is current on the server, counting from 1. Zero where
        # the server named none.
        patchSet: integer  # optional
        # The commit the current patch set is. A checkout whose HEAD is another
        # commit is not the revision under review.
        commit: string  # optional
        # The Gerrit project it was pushed to.
        project: string  # optional
        # When the change last moved.
        updated: string  # optional
        # Where a person reads the review.
        url: string  # optional
        # How to get this patch set into a checkout. Null where the server named no
        # patch set, since a ref names one.
        fetch:  # optional
          # The static ref this patch set is filed under. Every patch set keeps its
          # own, so an earlier one stays fetchable after a newer is pushed.
          ref: string
          # What to fetch that ref from. It is the review server rather than origin:
          # a core clone fetches from the GitHub mirror, and refs/changes is not
          # there.
          remote: string
        # What the change stands at, one entry per label. Null where the review was
        # not read, which is every hit of an issue search: a list with zeros in it
        # means nobody has voted, and that is a different answer.
        labels: array or null  # optional
        # How many comments the change carries, which the review server states
        # whether or not they were read.
        commentCount: integer  # optional
        # The comments left on the change, oldest first. Empty means it carries
        # none. Null means they were not read: an issue search asks for none, and a
        # change lookup whose comment call did not answer says so here rather than
        # with an empty list — hold it against commentCount.
        comments: array or null  # optional
        # The review log, oldest first, where messages asked for it. Null otherwise,
        # which is the default and every hit of an issue search.
        messages: array or null  # optional
        # How many of the log a service user wrote, which messages: "people" is what
        # drops. Answered whichever way it was asked, so a log full of pipeline
        # reports answering zero here is Gerrit no longer tagging its service users
        # rather than a change no bot has been near. Null where the log was not
        # read.
        botMessageCount: integer or null  # optional
    # Why nothing was answered, where status says unavailable. Null otherwise.
    unavailable:
      # One of: source-not-answering, source-not-parseable. source-not-answering:
      # review.typo3.org did not answer this time, and the same call may answer the
      # next. source-not-parseable: something answered and it was not the review
      # API, which is what a proxy or a captive portal looks like from here.
      cause: string
      reason: string
    # Why an empty answer cannot be read as an absence, or null where it can. This
    # server reads the review server without credentials, so a change that is
    # private or work in progress is invisible to it and looks exactly like one
    # nobody pushed. Null means empty really does mean nothing matched.
    indistinguishable: string or null

Answered
--------

Recorded on 2026-08-17 by ``bin/cli tools:record``. Answered against
core-checkout, TYPO3 14.3.7-dev, the 14.3 core checkout below .checkouts/,
whose console could not be reached: <installation> has no TYPO3 console —
none of bin/typo3, vendor/bin/typo3 exists. Nothing checks what is below this
heading; everything above it is derived from the class that answers the call,
and ``bin/cli tools:check`` holds it.

gerrit: has this issue a patch already
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Called with:

.. code-block:: json

    {
        "issue": "110348",
        "limit": 3
    }

Text:

.. code-block:: text

    TYPO3 core review server: https://review.typo3.org
    Query: message:110348

    ## [TASK] Deprecate AssetCollector media handling (MERGED)
    Change 95040 · main · https://review.typo3.org/c/Packages/TYPO3.CMS/+/95040
    Change-Id: Ib755fc396e94a1ee4273338163804782768dc707
    Patch set 3 · e82b930e6e0587842427496c5ce01f625b27fb66
    Fetch: git fetch https://review.typo3.org/Packages/TYPO3.CMS refs/changes/40/95040/3
    Last moved: 2026-08-02 20:40:50.000000000

    Hold the commit against `git rev-parse HEAD` in the checkout. Where the two differ, the checkout is not the revision under review, and a review says which of the two it read.

    The fetch goes to the review server rather than to `origin`: a core clone fetches from the GitHub mirror, where `refs/changes/…` does not exist. `git switch --detach FETCH_HEAD` is what puts the checkout on the patch set afterwards.

Data:

.. code-block:: json

    {
        "status": "answered",
        "source": "https://review.typo3.org",
        "query": "message:110348",
        "changes": [
            {
                "number": 95040,
                "changeId": "Ib755fc396e94a1ee4273338163804782768dc707",
                "subject": "[TASK] Deprecate AssetCollector media handling",
                "status": "MERGED",
                "branch": "main",
                "patchSet": 3,
                "commit": "e82b930e6e0587842427496c5ce01f625b27fb66",
                "project": "Packages/TYPO3.CMS",
                "updated": "2026-08-02 20:40:50.000000000",
                "url": "https://review.typo3.org/c/Packages/TYPO3.CMS/+/95040",
                "fetch": {
                    "ref": "refs/changes/40/95040/3",
                    "remote": "https://review.typo3.org/Packages/TYPO3.CMS"
                },
                "labels": null,
                "commentCount": 0,
                "comments": null,
                "messages": null,
                "botMessageCount": null
            }
        ],
        "indistinguishable": null,
        "unavailable": null
    }

gerrit: one change by number
~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Called with:

.. code-block:: json

    {
        "change": "89011"
    }

Text:

.. code-block:: text

    TYPO3 core review server: https://review.typo3.org
    Query: change:If7a109358c5432f55cc2947a1f6d0f437b830183

    ## [TASK] Raise --dev phpunit/phpunit:^11.5.17 (MERGED)
    Change 89011 · main · https://review.typo3.org/c/Packages/TYPO3.CMS/+/89011
    Change-Id: If7a109358c5432f55cc2947a1f6d0f437b830183
    Patch set 4 · fabe19d4150feb4b80317bba217d289115c6d00d
    Fetch: git fetch https://review.typo3.org/Packages/TYPO3.CMS refs/changes/11/89011/4
    Last moved: 2025-04-09 19:01:42.000000000
    Verified: satisfied · Stefan Bürk +1 · Christian Kuhn +2 · core-ci +1 · Benni Mack +1
    Code-Review: satisfied · Stefan Bürk +1 · Christian Kuhn +2 · core-ci 0 · Benni Mack +1

    ### Comments (1, 0 unresolved)

    - Christian Kuhn · patch set 3 · resolved
      temp -1: backport pushed, will run nightly on both.

    ## [TASK] Raise --dev phpunit/phpunit:^11.5.17 (MERGED)
    Change 89012 · 13.4 · https://review.typo3.org/c/Packages/TYPO3.CMS/+/89012
    Change-Id: If7a109358c5432f55cc2947a1f6d0f437b830183
    Patch set 2 · fc13415b1744d6cefea5241449d61d4a06a09980
    Fetch: git fetch https://review.typo3.org/Packages/TYPO3.CMS refs/changes/12/89012/2
    Last moved: 2025-04-09 19:01:53.000000000
    Verified: satisfied · Christian Kuhn +2 · core-ci +1
    Code-Review: satisfied · Christian Kuhn +2 · core-ci 0

    `unresolved` is the flag on the thread as its last writer left it, not a judgement that nobody answered: a comment can carry a reply and stay unresolved, and one can be resolved with nothing written under it. Which of them this review would otherwise make a second time is yours to read.

    A vote a later patch set dropped is absent here rather than zero, and the copy condition that dropped it is written in the review log alone — ask again with `messages: "people"` where a label stands at nothing and you need to know whether it ever stood elsewhere.

    More than one change above carries the same Change-Id. That is what a backport keeps, so they are one patch on the branches each of them names. Gerrit relates them by nothing else, and the state of one says nothing about the state of the other.

    Hold the commit against `git rev-parse HEAD` in the checkout. Where the two differ, the checkout is not the revision under review, and a review says which of the two it read.

    The fetch goes to the review server rather than to `origin`: a core clone fetches from the GitHub mirror, where `refs/changes/…` does not exist. `git switch --detach FETCH_HEAD` is what puts the checkout on the patch set afterwards.

    A patch set in front of you opens one of two workflows: `typo3-core-patch-review` reviews it, and `typo3-core-patch-checkout` fetches it into a checkout and backs out again. Open the one this task is before reading the diff.
    Both start at `typo3_project_describe`: which installation this checkout is, what it runs, and which whole procedures this server carries.

Data:

.. code-block:: json

    {
        "status": "answered",
        "source": "https://review.typo3.org",
        "query": "change:If7a109358c5432f55cc2947a1f6d0f437b830183",
        "changes": [
            {
                "number": 89011,
                "changeId": "If7a109358c5432f55cc2947a1f6d0f437b830183",
                "subject": "[TASK] Raise --dev phpunit/phpunit:^11.5.17",
                "status": "MERGED",
                "branch": "main",
                "patchSet": 4,
                "commit": "fabe19d4150feb4b80317bba217d289115c6d00d",
                "project": "Packages/TYPO3.CMS",
                "updated": "2025-04-09 19:01:42.000000000",
                "url": "https://review.typo3.org/c/Packages/TYPO3.CMS/+/89011",
                "fetch": {
                    "ref": "refs/changes/11/89011/4",
                    "remote": "https://review.typo3.org/Packages/TYPO3.CMS"
                },
                "labels": [
                    {
                        "label": "Verified",
                        "satisfied": true,
                        "votes": [
                            {
                                "voter": "Stefan Bürk",
                                "value": 1,
                                "on": "2025-04-09 19:01:42.000000000"
                            },
                            {
                                "voter": "Christian Kuhn",
                                "value": 2,
                                "on": "2025-04-09 19:01:42.000000000"
                            },
                            {
                                "voter": "core-ci",
                                "value": 1,
                                "on": "2025-04-09 19:01:42.000000000"
                            },
                            {
                                "voter": "Benni Mack",
                                "value": 1,
                                "on": "2025-04-09 19:01:42.000000000"
                            }
                        ]
                    },
                    {
                        "label": "Code-Review",
                        "satisfied": true,
                        "votes": [
                            {
                                "voter": "Stefan Bürk",
                                "value": 1,
                                "on": "2025-04-09 19:01:42.000000000"
                            },
                            {
                                "voter": "Christian Kuhn",
                                "value": 2,
                                "on": "2025-04-09 19:01:42.000000000"
                            },
                            {
                                "voter": "core-ci",
                                "value": 0,
                                "on": ""
                            },
                            {
                                "voter": "Benni Mack",
                                "value": 1,
                                "on": "2025-04-09 19:01:42.000000000"
                            }
                        ]
                    }
                ],
                "commentCount": 1,
                "comments": [
                    {
                        "id": "c8ceabfc_3296c5f5",
                        "author": "Christian Kuhn",
                        "on": "2025-04-09 18:19:04.000000000",
                        "patchSet": 3,
                        "file": "/PATCHSET_LEVEL",
                        "line": null,
                        "unresolved": false,
                        "inReplyTo": null,
                        "message": "temp -1: backport pushed, will run nightly on both."
                    }
                ],
                "messages": null,
                "botMessageCount": null
            },
            {
                "number": 89012,
                "changeId": "If7a109358c5432f55cc2947a1f6d0f437b830183",
                "subject": "[TASK] Raise --dev phpunit/phpunit:^11.5.17",
                "status": "MERGED",
                "branch": "13.4",
                "patchSet": 2,
                "commit": "fc13415b1744d6cefea5241449d61d4a06a09980",
                "project": "Packages/TYPO3.CMS",
                "updated": "2025-04-09 19:01:53.000000000",
                "url": "https://review.typo3.org/c/Packages/TYPO3.CMS/+/89012",
                "fetch": {
                    "ref": "refs/changes/12/89012/2",
                    "remote": "https://review.typo3.org/Packages/TYPO3.CMS"
                },
                "labels": [
                    {
                        "label": "Verified",
                        "satisfied": true,
                        "votes": [
                            {
                                "voter": "Christian Kuhn",
                                "value": 2,
                                "on": "2025-04-09 19:01:53.000000000"
                            },
                            {
                                "voter": "core-ci",
                                "value": 1,
                                "on": "2025-04-09 19:01:53.000000000"
                            }
                        ]
                    },
                    {
                        "label": "Code-Review",
                        "satisfied": true,
                        "votes": [
                            {
                                "voter": "Christian Kuhn",
                                "value": 2,
                                "on": "2025-04-09 19:01:53.000000000"
                            },
                            {
                                "voter": "core-ci",
                                "value": 0,
                                "on": ""
                            }
                        ]
                    }
                ],
                "commentCount": 0,
                "comments": [],
                "messages": null,
                "botMessageCount": null
            }
        ],
        "indistinguishable": null,
        "unavailable": null
    }

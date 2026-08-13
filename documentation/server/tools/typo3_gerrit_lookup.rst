.. _typo3_gerrit_lookup:

``typo3_gerrit_lookup``
=======================

Find out whether a TYPO3 core patch already exists, from the review server at
review.typo3.org. Pass issue with a Forge issue number to search the commit
messages of every change for it — the question "has somebody already fixed this"
— or change with the Change-Id from a commit message, or the change number a
review URL ends with, to read the one it names. Answers with the change number,
subject, status, target branch, review URL, the Change-Id, and the patch set
that is current on the server with the commit it is — which is what says whether
a checkout is the revision under review. A change is answered together with the
changes sharing its Change-Id, whichever handle named it — that is how a
backport on a release branch is reached. Each change also carries the ref that
patch set is fetchable by and the review server to fetch it over, so getting it
into a checkout takes no second lookup. A call carries issue or change, never
both. This reaches the network, and it reads: reviewing, voting and uploading
stay yours. Answers from: network.

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
        # none. A backport keeps it unchanged, so changes sharing one are the same
        # patch on more than one branch — pass it back as change to read all of
        # them.
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

Recorded on 2026-08-14 by ``bin/cli tools:record``. Answered against
core-checkout, TYPO3 14.3.7-dev, the 14.3 core checkout below .checkouts/,
whose console answers. Nothing checks what is below this heading; everything
above it is derived from the class that answers the call, and ``bin/cli
tools:check`` holds it.

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
                }
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

    ## [TASK] Raise --dev phpunit/phpunit:^11.5.17 (MERGED)
    Change 89012 · 13.4 · https://review.typo3.org/c/Packages/TYPO3.CMS/+/89012
    Change-Id: If7a109358c5432f55cc2947a1f6d0f437b830183
    Patch set 2 · fc13415b1744d6cefea5241449d61d4a06a09980
    Fetch: git fetch https://review.typo3.org/Packages/TYPO3.CMS refs/changes/12/89012/2
    Last moved: 2025-04-09 19:01:53.000000000

    More than one change above carries the same Change-Id. That is what a backport keeps, so they are one patch on the branches each of them names. Gerrit relates them by nothing else, and the state of one says nothing about the state of the other.

    Hold the commit against `git rev-parse HEAD` in the checkout. Where the two differ, the checkout is not the revision under review, and a review says which of the two it read.

    The fetch goes to the review server rather than to `origin`: a core clone fetches from the GitHub mirror, where `refs/changes/…` does not exist. `git switch --detach FETCH_HEAD` is what puts the checkout on the patch set afterwards.

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
                }
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
                }
            }
        ],
        "indistinguishable": null,
        "unavailable": null
    }

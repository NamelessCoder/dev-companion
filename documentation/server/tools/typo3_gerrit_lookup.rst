.. _typo3_gerrit_lookup:

``typo3_gerrit_lookup``
=======================

Find out whether a TYPO3 core patch already exists and what state its review is
in, from the review server at review.typo3.org. Pass issue with a Forge issue
number to search the commit messages of every change for it — the question "has
somebody already fixed this" — or change with the Change-Id from a commit
message, or the change number a review URL ends with, to read the one it names.
Or commit with a hash out of a checkout, abbreviated as git log prints it or
whole, which is the handle a session working in a clone actually holds — the
review server refuses one passed as change. Or search the server without holding
any of them: query takes words matched against the commit messages, path takes a
repository path and answers the changes touching it, the two combine, and open
narrows them to what is still under review. That is the direction a triage opens
with — is anybody working on this file, and did anybody ever try this fix — and
it is the review surface a checkout cannot see, since a core clone carries what
landed and says nothing about what is open. Or backlog to enumerate the core's
open changes without holding a handle or a wording — oldest pushed or longest
untouched, narrowed by size, by vote state, by whether it still merges, by
branch, by date and by person. That is where a review session starts: "which
open reviews have been sitting a long time", "which of them are small and almost
voted through", "which are mine" and "which have I already voted on" are
maxSize, minCodeReview, negativeVotes, mergeable, owner and reviewedBy, and none
of them is answerable by words in a commit message. Answers with the change
number, its Change-Id, subject, status, target branch, review URL, and the patch
set that is current on the server with the commit it is — which is what says
whether a checkout is the revision under review. Every change, whichever way it
was reached, also carries how many lines it adds and removes, whether it still
merges, when it was pushed, how many comment threads are unresolved, and what
each label stands at — the size, the age and the vote state a reviewer picks a
change by. A change is answered together with the changes sharing its Change-Id,
whichever handle named it — that is how a backport on a release branch is
reached, and how a commit hash answers which branches carry the fix. Every
change whose commit message was read also names the branches its Releases:
trailer claims. The trailer is the author's claim about where the patch belongs
and the siblings beside it are what was pushed, so read the two together rather
than one for the other. It also carries the relation chain it sits in: the
changes stacked on it and the changes it is built on, each with its number, its
status and its subject, which is what says whether the change is one part of a
larger feature and how far that feature has got. The two relations are different
— a chain is changes built on one another, a shared Change-Id is one patch on
several branches. A change read by name also carries the Forge issues its commit
message names in its Resolves: and Related: trailers, each with its subject,
tracker and status. That is the join between the patch and the tracker, and it
is where a second issue named nowhere else in the review is seen. Each change
also carries the ref that patch set is fetchable by and the review server to
fetch it over, so getting it into a checkout takes no second lookup. A change
read by name carries the review it is in as well: the value every voter holds
per label and whether the submit rule is satisfied, and every comment left on it
with its patch set, its file and line, whether the thread is unresolved and
which comment it replies to. That is where a comment somebody left on an earlier
patch set and nobody answered is read. Why a vote is gone is in the review log
instead, which messages asks for. A call carries issue, change, commit, a search
by query and path, or backlog, never two of those. Beside the branch each change
targets it names the branches that take a patch today, each with the day its
regular support ends — the list a Releases: trailer may name, which a core clone
supplies nowhere, since git branch -r reaches back to TYPO3_3-6 and says nothing
about which of them is still maintained. Which of those lines a change belongs
on is not answered here: that is the author's claim, and
typo3_commit_message_guide with workflow="core" is what reads a trailer against
them. This reaches the network, and it reads: reviewing, voting and uploading
stay yours. Answers from: network.

``readOnlyHint: true`` · ``destructiveHint: false`` · ``idempotentHint: true`` · ``openWorldHint: true``

Answers from :ref:`network <answer-sources-network>`.

Takes
-----

.. code-block:: yaml

    # Forge issue number, with or without the leading #, for example "105403".
    # Searches every change whose commit message names it, which is where Resolves:
    # and Related: put it. A call carries issue, change, commit, a search by query
    # and path, or backlog, never two of those.
    issue: string  # optional
    # One change to read, named either by the Change-Id its commit message carries,
    # for example "I0f4c5b9a3e2d1c7b8a6f5e4d3c2b1a0f9e8d7c6b", or by the change
    # number a review URL ends with, for example "89011". Prefer the Change-Id where
    # the commit is in front of you: it is part of the patch being read, it survives
    # being amended into a new patch set, and it cannot be mistaken for the Forge
    # issue number the way a bare change number can. A call carries issue, change,
    # commit, a search by query and path, or backlog, never two of those.
    change: string  # optional
    # A commit hash out of a checkout, abbreviated as git log prints it or whole,
    # for example "cf227b18e20". Answers the change that commit is a patch set of,
    # and with it the changes sharing its Change-Id — which is how a hash in your
    # own history reaches the backports beside it and the branches each of them
    # targets. Pass a hash here rather than as change: the review server answers
    # "Invalid change format" to a commit passed as change, which arrives as the
    # server not answering at all. A call carries issue, change, commit, a search by
    # query and path, or backlog, never two of those.
    commit: string  # optional
    # Words to search the review server for, for example "impexp translation". Every
    # word has to appear, and what they are matched against is the commit message
    # — the subject and the body, so a change whose subject does not carry the
    # word is still found. They are not matched against the diff: change 89000 added
    # writePagesOrder and a search for that name answers nothing, so a zero says no
    # commit message names the word rather than that nobody has touched the code.
    # Ask again in the words a commit message would use, and pass path for the
    # changes that touch a file whatever they are called. Combine it with path to
    # narrow one by the other, and with open for what is still under review. A call
    # carries issue, change, commit, a search by query and path, or backlog, never
    # two of those.
    query: string  # optional
    # A path in the repository, for example "typo3/sysext/impexp" or
    # "typo3/sysext/impexp/Classes/Import.php". Answers the changes that touch it
    # — the path itself and everything under it — which is the surface a
    # checkout cannot see: a core clone carries what landed and says nothing about
    # what is open. It is the way to ask whether somebody is already working on a
    # file before writing a patch for it, and with open it is that question exactly.
    # Without open it reaches the abandoned and the merged changes too, which is
    # where an earlier attempt at the same fix is found. Combine it with query to
    # narrow one by the other. A call carries issue, change, commit, a search by
    # query and path, or backlog, never two of those.
    path: string  # optional
    # Narrow a search to the changes that are still under review. False, the
    # default, reaches every state — which is what "has anybody ever tried this"
    # needs, since an abandoned or merged attempt is the answer to it. True is "who
    # is working on this now". Narrows query and path, and is ignored by issue,
    # change and commit.
    open: boolean  # optional
    # One of: none, people, all. The review log of a change: every message its patch
    # sets and its reviewers left. Ask for it to find out why a vote is gone —
    # Gerrit writes "Outdated Votes: * Code-Review+1 (copy condition: ...)" into the
    # message of the upload that dropped it, and the labels afterwards look exactly
    # like a change nobody has voted on. "none" leaves it out and is the default,
    # since it is 57.9 KB against 14.3 KB on a change with 21 patch sets. "people"
    # drops what a service user wrote, which on that change is 20 of 46 messages and
    # every one of them a CI pipeline report. "all" keeps them. How many were
    # dropped is answered whichever you ask for. Narrows change and commit, and is
    # ignored by every other way in.
    messages: string  # optional
    # One of: oldest, stale. Enumerate the open changes of the TYPO3 core instead of
    # reading one or matching words: "oldest" orders them by when they were pushed,
    # "stale" by how long nobody has touched them. The two answer different
    # questions about one backlog — pushed long ago is about the patch, untouched
    # for months is about the attention it got — and a change that is both is what
    # a review session is looking for. This is the direction "find old reviews worth
    # working off" takes, and the filters beside it are what "small", "has votes"
    # and "still applies" mean: maxSize, minCodeReview, negativeVotes and mergeable.
    # The changes their own authors marked work in progress are left out of every
    # one of these, since a draft is not offered for review; query says so. Narrow
    # with branch, updatedBefore, owner, reviewedBy and involving. A call carries
    # issue, change, commit, a search by query and path, or backlog, never two of
    # those.
    backlog: string  # optional
    # Only changes whose insertions and deletions add up to at most this, for
    # example 60. That is what "small in scope" comes to, and it is the filter that
    # decides whether a review fits into a session at all. Narrows backlog and is
    # ignored by every other way in.
    maxSize: integer  # optional
    # Only changes somebody holds at least this Code-Review vote on: 1 for a change
    # a reviewer has already been through once, 2 for one that is approved. Paired
    # with negativeVotes false this is "almost ready" — somebody is for it and
    # nobody is against it. Narrows backlog and is ignored by every other way in.
    minCodeReview: integer  # optional
    # Whether changes carrying a Code-Review-1 or a Verified-1 are in the answer.
    # True, the default, keeps them. False drops both, which is a reviewer objecting
    # and a pipeline failing — the two reasons a change is not one to pick up now.
    # Narrows backlog and is ignored by every other way in.
    negativeVotes: boolean  # optional
    # True answers only the changes that still merge into their target branch. It is
    # the review server's own last computation of that and not a merge run now, so
    # it is which changes are worth fetching rather than a promise one will apply.
    # False, the default, keeps every change, and the ones that no longer merge are
    # usually the oldest — which is what makes an unfiltered "oldest first" page a
    # list of conflicts. Narrows backlog and is ignored by every other way in.
    mergeable: boolean  # optional
    # Only changes targeting this branch, spelled as the branch is: "main", "13.4".
    # Worth setting when the checkout in front of you is on one line, since a patch
    # for another branch is reviewed against code you do not have. Narrows backlog
    # and is ignored by every other way in.
    branch: string  # optional
    # Only changes nobody has touched since this day, as YYYY-MM-DD. This is what
    # finds the review everybody has walked past, which age alone does not: a change
    # pushed in 2023 and commented on last week is being worked. It reads the last
    # update and never the push date — the review server indexes no created date,
    # which is also why backlog "oldest" is ordered here. Narrows backlog and is
    # ignored by every other way in.
    updatedBefore: string  # optional
    # Only changes this person pushed, by their name or their e-mail address:
    # "Benjamin Kott", "benjamin.kott@outlook.com", or part of either. This is what
    # answers "which open changes are mine" — query cannot, because it matches the
    # commit message and a name there is as often somebody else writing it. The
    # review server resolves the name; a name it does not know answers no changes,
    # which looks exactly like a person with none. Narrows backlog and is ignored by
    # every other way in.
    owner: string  # optional
    # Only changes this person has voted on, resolved the same way as owner. It is
    # the other half of a person and a different question: what somebody pushed is
    # theirs to finish, what they voted on is theirs to have judged already. Narrows
    # backlog and is ignored by every other way in.
    reviewedBy: string  # optional
    # Only changes this person is on either side of — pushed or voted on, as one
    # set. Passed instead of owner and reviewedBy, not beside them: passing those
    # two together means changes somebody pushed AND voted on, which is a set nobody
    # wants. Narrows backlog and is ignored by every other way in.
    involving: string  # optional
    limit: integer  # optional

The call carries exactly one of these sets of arguments: ``issue`` — or
``change`` — or ``commit`` — or ``query`` — or ``path`` — or ``backlog``.

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
    # The changes that matched, newest activity first — and oldest or longest
    # untouched first where backlog asked for an enumeration.
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
        # When it was pushed, which is the other date and the one that says how long
        # it has been waiting. A change pushed years ago and touched last week is
        # being worked on; one where the two dates are far apart is not.
        created: string  # optional
        # Lines the current patch set adds. Null where the review server stated
        # none. With deletions this is the size a reviewer picks a change by, and it
        # is the diff of the whole change rather than of what is left to read.
        insertions: integer or null  # optional
        # Lines the current patch set removes. Null where the review server stated
        # none.
        deletions: integer or null  # optional
        # Whether the current patch set still merges into its target branch. It is
        # the review server's own last computation and not a merge run now, so false
        # is grounds to expect a rebase rather than a finding. Null where it
        # computed none, which is not "it does not merge".
        mergeable: boolean or null  # optional
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
        # What the change stands at, one entry per label. Every answer carries the
        # state of each label, which the review server states on every row; the
        # voters behind it are asked for by reading one change, so votes is null on
        # a search and a list there.
        labels: array or null  # optional
        # How many comments the change carries, which the review server states
        # whether or not they were read.
        commentCount: integer  # optional
        # How many of those sit on a thread nobody has marked resolved, which the
        # review server states whether or not the comments were read. It is the flag
        # as its last writer left it rather than a count of unanswered questions,
        # and on a change to pick up it is the work still owed to the last reviewer.
        unresolvedCommentCount: integer  # optional
        # The comments left on the change, oldest first. Empty means it carries
        # none. Null means they were not read: a search asks for none, and a change
        # lookup whose comment call did not answer says so here rather than with an
        # empty list — hold it against commentCount.
        comments: array or null  # optional
        # The relation chain this change sits in, child first: above it the changes
        # stacked on it, then itself, then the changes it is built on. This is the
        # other relation and not the Change-Id one — a chain is different changes
        # built on one another, a shared Change-Id is one patch on several branches,
        # and reading the two as one set overstates both. Empty means the change
        # stands alone, which is the ordinary case. Null means the chain was not
        # read: a search asks for none, and a change lookup whose call did not
        # answer says so here rather than with an empty list.
        chain: array or null  # optional
        # The Forge issues this change's commit message names in its Resolves: and
        # Related: trailers, each filled with what says whether to read it. That is
        # the join between the patch and the tracker, and it is where a second issue
        # nobody mentioned elsewhere is seen. Empty means the message names none.
        # Null means the message was not read: a search asks for none of this, and
        # reading one hit by name is what answers it there.
        issues: array or null  # optional
        # The branches this change's commit message names in its Releases: trailer,
        # spelled as the trailer spells them. It is the author's claim about which
        # branches the patch belongs on, written before it went to any of them —
        # what was pushed is the changes above sharing a Change-Id, one per branch
        # and each with its own status, so a branch named here with no change
        # targeting it is a backport nobody has pushed. Empty means the message
        # carries no such trailer, which every change outside the core project is.
        # Null means the message was not read, which is a search by words or path.
        releases: array or null  # optional
        # The review log, oldest first, where messages asked for it. Null otherwise,
        # which is the default and every hit a search answers.
        messages: array or null  # optional
        # How many of the log a service user wrote, which messages: "people" is what
        # drops. Answered whichever way it was asked, so a log full of pipeline
        # reports answering zero here is Gerrit no longer tagging its service users
        # rather than a change no bot has been near. Null where the log was not
        # read.
        botMessageCount: integer or null  # optional
    # What the enumeration read, where backlog asked for one. Null on every other
    # way in. The review server states no total for a query and offers no created
    # date to sort by, so the matched set is read whole and ordered here — which
    # is what these two numbers are about.
    backlog:
      # One of: oldest, stale. oldest: by when the change was pushed. stale: by when
      # it last moved.
      order: string
      # How many changes the filters matched and this answer sorted, of which
      # changes above carries at most limit. Where the two differ this is a page,
      # and what reaches the rest of it is a narrower filter rather than a larger
      # limit.
      read: integer
      # Whether read is the whole matched set. False where the read stopped at the
      # bound, and then the ordering is over one end of the set rather than over all
      # of it — narrow the filters before reading the page as the oldest changes
      # there are.
      complete: boolean
    # The branches that take a patch today, from a list this server ships rather
    # than from the review server, so it is answered whatever the status above says.
    # It is what a Releases: trailer may name, and a core clone supplies it nowhere:
    # git branch -r reaches back to TYPO3_3-6 and says nothing about which of those
    # is still maintained. Which of these lines a change belongs on is not here —
    # that is the author's claim, and typo3_commit_message_guide with
    # workflow="core" is what reads a trailer against them.
    releaseLines:
      # Newest first, the development line at the head.
      branches:
        - # The branch, spelled as a Releases: trailer spells it and as the branch
          # field of a change above does.
          branch: string
          # One of: development, maintained. development: the line every core change
          # is written against first. maintained: in regular support, so a patch
          # pushed here is released from this branch. A line out of regular support
          # is not in this list at all — what it releases comes from the ELTS
          # partners rather than from the branch.
          state: string
          # The day regular support ends, as the release calendar states it. Null on
          # the development line, which has no such date.
          maintainedUntil: string or null
      # Where the calendar was read, so it can be read again rather than trusted.
      source: string
      # The day it was read. A branch released since is one this list could not
      # carry, and a change above targeting a branch that is absent here is either
      # that or a line out of regular support.
      readAt: string
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

Recorded on 2026-08-25 by ``bin/cli tools:record``. Answered against
core-checkout, TYPO3 15.0.0-dev, the main core checkout below .checkouts/,
whose console could not be reached: <installation> has no TYPO3 console —
none of bin/typo3, vendor/bin/typo3 exists. Its dependencies are not installed
— vendor/autoload.php is not there either, and composer install writes both.
Nothing checks what is below this heading; everything above it is derived from
the class that answers the call, and ``bin/cli tools:check`` holds it.

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
    +396 -69 · pushed 2026-07-31
    Releases: main
    Verified: satisfied
    Code-Review: satisfied

    A `Releases:` line is the author's claim about which branches the patch belongs on, written before it went to any of them. What was pushed is the changes above sharing a Change-Id, one per branch and each with a status of its own — so a branch the trailer names with no change targeting it is a backport nobody has pushed, and a merged change is on its branch whatever the trailer says. Which release carries it is neither: that is the first release cut from the branch after the change merged, which this server does not read.

    The branches that take a patch today, whichever one the change above targets: main is the development line, which every core change is written against first; 14.3 is in regular support until 2029-06-30; 13.4 is in regular support until 2027-12-31. Read from https://get.typo3.org/api/v1/major/ on 2026-08-05; a core clone carries no such list, since "git branch -r" reaches back to TYPO3_3-6 and says nothing about which of those is still maintained. Which of these a change belongs on is the author's claim rather than a consequence of the list — `typo3_commit_message_guide` with `workflow="core"` is what reads a `Releases:` trailer against them.

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
                "created": "2026-07-31 19:23:22.000000000",
                "insertions": 396,
                "deletions": 69,
                "mergeable": null,
                "url": "https://review.typo3.org/c/Packages/TYPO3.CMS/+/95040",
                "fetch": {
                    "ref": "refs/changes/40/95040/3",
                    "remote": "https://review.typo3.org/Packages/TYPO3.CMS"
                },
                "labels": [
                    {
                        "label": "Verified",
                        "state": "OK",
                        "satisfied": true,
                        "votes": null
                    },
                    {
                        "label": "Code-Review",
                        "state": "OK",
                        "satisfied": true,
                        "votes": null
                    }
                ],
                "commentCount": 0,
                "unresolvedCommentCount": 0,
                "comments": null,
                "chain": null,
                "issues": null,
                "releases": [
                    "main"
                ],
                "messages": null,
                "botMessageCount": null
            }
        ],
        "backlog": null,
        "releaseLines": {
            "branches": [
                {
                    "branch": "main",
                    "state": "development",
                    "maintainedUntil": null
                },
                {
                    "branch": "14.3",
                    "state": "maintained",
                    "maintainedUntil": "2029-06-30"
                },
                {
                    "branch": "13.4",
                    "state": "maintained",
                    "maintainedUntil": "2027-12-31"
                }
            ],
            "source": "https://get.typo3.org/api/v1/major/",
            "readAt": "2026-08-05"
        },
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
    +148 -92 · pushed 2025-04-09
    Releases: main, 13.4
    Verified: satisfied · Stefan Bürk +1 · Christian Kuhn +2 · core-ci +1 · Benni Mack +1
    Code-Review: satisfied · Stefan Bürk +1 · Christian Kuhn +2 · core-ci 0 · Benni Mack +1

    ### Issues named in the commit message (1)
    - resolves #106535 — Task · Closed · Raise --dev phpunit/phpunit:^11.5.17 -w · https://forge.typo3.org/issues/106535

    ### Comments (1, 0 unresolved)

    - Christian Kuhn · patch set 3 · resolved
      temp -1: backport pushed, will run nightly on both.

    ## [TASK] Raise --dev phpunit/phpunit:^11.5.17 (MERGED)
    Change 89012 · 13.4 · https://review.typo3.org/c/Packages/TYPO3.CMS/+/89012
    Change-Id: If7a109358c5432f55cc2947a1f6d0f437b830183
    Patch set 2 · fc13415b1744d6cefea5241449d61d4a06a09980
    Fetch: git fetch https://review.typo3.org/Packages/TYPO3.CMS refs/changes/12/89012/2
    Last moved: 2025-04-09 19:01:53.000000000
    +150 -94 · pushed 2025-04-09
    Releases: main, 13.4
    Verified: satisfied · Christian Kuhn +2 · core-ci +1
    Code-Review: satisfied · Christian Kuhn +2 · core-ci 0

    ### Issues named in the commit message (1)
    - resolves #106535 — Task · Closed · Raise --dev phpunit/phpunit:^11.5.17 -w · https://forge.typo3.org/issues/106535

    The issues above are what the commit message names, and a status there is the issue's own rather than this change's. Pass one to `typo3_forge_lookup` as `issue` to read it whole, which is where a maintainer said why something was closed or reassigned.

    `unresolved` is the flag on the thread as its last writer left it, not a judgement that nobody answered: a comment can carry a reply and stay unresolved, and one can be resolved with nothing written under it. Which of them this review would otherwise make a second time is yours to read.

    A vote a later patch set dropped is absent here rather than zero, and the copy condition that dropped it is written in the review log alone — ask again with `messages: "people"` where a label stands at nothing and you need to know whether it ever stood elsewhere.

    More than one change above carries the same Change-Id. That is what a backport keeps, so they are one patch on the branches each of them names. Gerrit relates them by nothing else, and the state of one says nothing about the state of the other.

    A `Releases:` line is the author's claim about which branches the patch belongs on, written before it went to any of them. What was pushed is the changes above sharing a Change-Id, one per branch and each with a status of its own — so a branch the trailer names with no change targeting it is a backport nobody has pushed, and a merged change is on its branch whatever the trailer says. Which release carries it is neither: that is the first release cut from the branch after the change merged, which this server does not read.

    The branches that take a patch today, whichever one the change above targets: main is the development line, which every core change is written against first; 14.3 is in regular support until 2029-06-30; 13.4 is in regular support until 2027-12-31. Read from https://get.typo3.org/api/v1/major/ on 2026-08-05; a core clone carries no such list, since "git branch -r" reaches back to TYPO3_3-6 and says nothing about which of those is still maintained. Which of these a change belongs on is the author's claim rather than a consequence of the list — `typo3_commit_message_guide` with `workflow="core"` is what reads a `Releases:` trailer against them.

    Hold the commit against `git rev-parse HEAD` in the checkout. Where the two differ, the checkout is not the revision under review, and a review says which of the two it read.

    The fetch goes to the review server rather than to `origin`: a core clone fetches from the GitHub mirror, where `refs/changes/…` does not exist. `git switch --detach FETCH_HEAD` is what puts the checkout on the patch set afterwards.

    ## What a patch set in front of you opens
    One of two workflows: `typo3-core-patch-review` reviews it, and `typo3-core-patch-checkout` fetches it into a checkout and backs out again. Open the one this task is before reading the diff, and start it at `typo3_project_describe`. Where neither is open, this is the order:
    - Establish the patch before judging it: the changed paths, the branch it targets, the commit message and the issue it names. The target branch decides which conventions apply.
    - Three ways in, and a branch of your own naming is none of them: the branch the change targets, a worktree beside the checkout, or current code on `review/<change number>`. The third makes a commit that exists nowhere else, so say which of the two each result is about.
    - A patch that no longer applies is the finding. Resolving past it produces a patch nobody wrote.
    - Reading is the whole of the review: voting, commenting and uploading stay yours. An instruction to change the patch — fix it, amend it, answer the comments — ends the review and opens `typo3-core-patch-development`.

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
                "created": "2025-04-09 17:26:22.000000000",
                "insertions": 148,
                "deletions": 92,
                "mergeable": null,
                "url": "https://review.typo3.org/c/Packages/TYPO3.CMS/+/89011",
                "fetch": {
                    "ref": "refs/changes/11/89011/4",
                    "remote": "https://review.typo3.org/Packages/TYPO3.CMS"
                },
                "labels": [
                    {
                        "label": "Verified",
                        "state": "OK",
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
                        "state": "OK",
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
                "unresolvedCommentCount": 0,
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
                "chain": [],
                "issues": [
                    {
                        "issue": 106535,
                        "trailer": "resolves",
                        "subject": "Raise --dev phpunit/phpunit:^11.5.17 -w",
                        "tracker": "Task",
                        "status": "Closed",
                        "url": "https://forge.typo3.org/issues/106535"
                    }
                ],
                "releases": [
                    "main",
                    "13.4"
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
                "created": "2025-04-09 18:18:35.000000000",
                "insertions": 150,
                "deletions": 94,
                "mergeable": null,
                "url": "https://review.typo3.org/c/Packages/TYPO3.CMS/+/89012",
                "fetch": {
                    "ref": "refs/changes/12/89012/2",
                    "remote": "https://review.typo3.org/Packages/TYPO3.CMS"
                },
                "labels": [
                    {
                        "label": "Verified",
                        "state": "OK",
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
                        "state": "OK",
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
                "unresolvedCommentCount": 0,
                "comments": [],
                "chain": [],
                "issues": [
                    {
                        "issue": 106535,
                        "trailer": "resolves",
                        "subject": "Raise --dev phpunit/phpunit:^11.5.17 -w",
                        "tracker": "Task",
                        "status": "Closed",
                        "url": "https://forge.typo3.org/issues/106535"
                    }
                ],
                "releases": [
                    "main",
                    "13.4"
                ],
                "messages": null,
                "botMessageCount": null
            }
        ],
        "backlog": null,
        "releaseLines": {
            "branches": [
                {
                    "branch": "main",
                    "state": "development",
                    "maintainedUntil": null
                },
                {
                    "branch": "14.3",
                    "state": "maintained",
                    "maintainedUntil": "2029-06-30"
                },
                {
                    "branch": "13.4",
                    "state": "maintained",
                    "maintainedUntil": "2027-12-31"
                }
            ],
            "source": "https://get.typo3.org/api/v1/major/",
            "readAt": "2026-08-05"
        },
        "indistinguishable": null,
        "unavailable": null
    }

gerrit: a change that is one part of a stack
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Called with:

.. code-block:: json

    {
        "change": "91563"
    }

Text:

.. code-block:: text

    TYPO3 core review server: https://review.typo3.org
    Query: change:I242eedc16bb7ca1e5c83adeaa0526a9e68f275e2

    ## [WIP][FEATURE] Introduce Action API (NEW)
    Change 91563 · main · https://review.typo3.org/c/Packages/TYPO3.CMS/+/91563
    Change-Id: I242eedc16bb7ca1e5c83adeaa0526a9e68f275e2
    Patch set 46 · ad7dc9be5e9bda8ecaa1d2dedb5c946eedfbe251
    Fetch: git fetch https://review.typo3.org/Packages/TYPO3.CMS refs/changes/63/91563/46
    Last moved: 2026-06-29 13:41:27.000000000
    +1753 -151 · no longer merges · pushed 2025-11-11
    Releases: main
    Verified: needs a vote · core-ci +1
    Code-Review: needs a vote · core-ci 0

    ### Relation chain (15 changes, 13 stacked on this one and 1 under it)
    - 92197 · NEW · [WIP][FEATURE] Provide Record Actions · https://review.typo3.org/c/Packages/TYPO3.CMS/+/92197
    - 92196 · NEW · [WIP][TASK] Add record serializer · https://review.typo3.org/c/Packages/TYPO3.CMS/+/92196
    - 88507 · NEW · [WIP][FEATURE] AI suggest demo using tools API · https://review.typo3.org/c/Packages/TYPO3.CMS/+/88507
    - 93599 · NEW · [WIP][TASK] Migrate resource endpoints to Actions API · https://review.typo3.org/c/Packages/TYPO3.CMS/+/93599
    - 92191 · NEW · [TASK] Migrate PageTree to Action API · https://review.typo3.org/c/Packages/TYPO3.CMS/+/92191
    - 92322 · NEW · [TASK] Migrate dashboard to Actions API · https://review.typo3.org/c/Packages/TYPO3.CMS/+/92322
    - 92724 · ABANDONED · [WIP][FEATURE] Implement OAuth authorization server · https://review.typo3.org/c/Packages/TYPO3.CMS/+/92724
    - 92323 · MERGED · [TASK] Avoid `json_encode()` workarounds in Settings API · chained at patch set 8, now at 10 · https://review.typo3.org/c/Packages/TYPO3.CMS/+/92323
    - 92224 · NEW · [WIP][FEATURE] Add MCP Server demo based on Actions API · https://review.typo3.org/c/Packages/TYPO3.CMS/+/92224
    - 92223 · NEW · [WIP][FEATURE] Provide AI Tool provider based on Actions API · https://review.typo3.org/c/Packages/TYPO3.CMS/+/92223
    - 91486 · NEW · [WIP][FEATURE] Implement API Hub · https://review.typo3.org/c/Packages/TYPO3.CMS/+/91486
    - 93423 · NEW · [TASK] Implement standalone redirect route option · https://review.typo3.org/c/Packages/TYPO3.CMS/+/93423
    - 91666 · NEW · [WIP][FEATURE] Provide OpenAPI spec w/ Swagger UI for Actions API · https://review.typo3.org/c/Packages/TYPO3.CMS/+/91666
    - 91563 · NEW · [WIP][FEATURE] Introduce Action API · this change · https://review.typo3.org/c/Packages/TYPO3.CMS/+/91563
    - 93064 · NEW · [TASK] Introduce JSON SchemaBuilder and Schema based Hydrator · https://review.typo3.org/c/Packages/TYPO3.CMS/+/93064

    A relation chain is a stack of different changes built on one another, listed child first: what stands above a change is stacked on it, and what stands below it is what it is built on. Each entry's status is that entry's own, so a MERGED entry says that change landed and says nothing about the change you asked for. Gerrit relates a chain by the commits, which is not the Change-Id relation a backport keeps, and neither set contains the other.

    An entry chained at an earlier patch set than it stands at now has moved on since the stack was built on it. Read it by its number rather than acting on the patch set the chain names.

    A vote a later patch set dropped is absent here rather than zero, and the copy condition that dropped it is written in the review log alone — ask again with `messages: "people"` where a label stands at nothing and you need to know whether it ever stood elsewhere.

    A `Releases:` line is the author's claim about which branches the patch belongs on, written before it went to any of them. What was pushed is the changes above sharing a Change-Id, one per branch and each with a status of its own — so a branch the trailer names with no change targeting it is a backport nobody has pushed, and a merged change is on its branch whatever the trailer says. Which release carries it is neither: that is the first release cut from the branch after the change merged, which this server does not read.

    The branches that take a patch today, whichever one the change above targets: main is the development line, which every core change is written against first; 14.3 is in regular support until 2029-06-30; 13.4 is in regular support until 2027-12-31. Read from https://get.typo3.org/api/v1/major/ on 2026-08-05; a core clone carries no such list, since "git branch -r" reaches back to TYPO3_3-6 and says nothing about which of those is still maintained. Which of these a change belongs on is the author's claim rather than a consequence of the list — `typo3_commit_message_guide` with `workflow="core"` is what reads a `Releases:` trailer against them.

    Hold the commit against `git rev-parse HEAD` in the checkout. Where the two differ, the checkout is not the revision under review, and a review says which of the two it read.

    The fetch goes to the review server rather than to `origin`: a core clone fetches from the GitHub mirror, where `refs/changes/…` does not exist. `git switch --detach FETCH_HEAD` is what puts the checkout on the patch set afterwards.

    ## What a patch set in front of you opens
    One of two workflows: `typo3-core-patch-review` reviews it, and `typo3-core-patch-checkout` fetches it into a checkout and backs out again. Open the one this task is before reading the diff, and start it at `typo3_project_describe`. Where neither is open, this is the order:
    - Establish the patch before judging it: the changed paths, the branch it targets, the commit message and the issue it names. The target branch decides which conventions apply.
    - Three ways in, and a branch of your own naming is none of them: the branch the change targets, a worktree beside the checkout, or current code on `review/<change number>`. The third makes a commit that exists nowhere else, so say which of the two each result is about.
    - A patch that no longer applies is the finding. Resolving past it produces a patch nobody wrote.
    - Reading is the whole of the review: voting, commenting and uploading stay yours. An instruction to change the patch — fix it, amend it, answer the comments — ends the review and opens `typo3-core-patch-development`.

Data:

.. code-block:: json

    {
        "status": "answered",
        "source": "https://review.typo3.org",
        "query": "change:I242eedc16bb7ca1e5c83adeaa0526a9e68f275e2",
        "changes": [
            {
                "number": 91563,
                "changeId": "I242eedc16bb7ca1e5c83adeaa0526a9e68f275e2",
                "subject": "[WIP][FEATURE] Introduce Action API",
                "status": "NEW",
                "branch": "main",
                "patchSet": 46,
                "commit": "ad7dc9be5e9bda8ecaa1d2dedb5c946eedfbe251",
                "project": "Packages/TYPO3.CMS",
                "updated": "2026-06-29 13:41:27.000000000",
                "created": "2025-11-11 17:18:39.000000000",
                "insertions": 1753,
                "deletions": 151,
                "mergeable": false,
                "url": "https://review.typo3.org/c/Packages/TYPO3.CMS/+/91563",
                "fetch": {
                    "ref": "refs/changes/63/91563/46",
                    "remote": "https://review.typo3.org/Packages/TYPO3.CMS"
                },
                "labels": [
                    {
                        "label": "Verified",
                        "state": "NEED",
                        "satisfied": false,
                        "votes": [
                            {
                                "voter": "core-ci",
                                "value": 1,
                                "on": "2026-06-29 13:41:27.000000000"
                            }
                        ]
                    },
                    {
                        "label": "Code-Review",
                        "state": "NEED",
                        "satisfied": false,
                        "votes": [
                            {
                                "voter": "core-ci",
                                "value": 0,
                                "on": ""
                            }
                        ]
                    }
                ],
                "commentCount": 0,
                "unresolvedCommentCount": 0,
                "comments": [],
                "chain": [
                    {
                        "number": 92197,
                        "status": "NEW",
                        "subject": "[WIP][FEATURE] Provide Record Actions",
                        "thisChange": false,
                        "patchSet": 9,
                        "chainedAt": 9,
                        "url": "https://review.typo3.org/c/Packages/TYPO3.CMS/+/92197"
                    },
                    {
                        "number": 92196,
                        "status": "NEW",
                        "subject": "[WIP][TASK] Add record serializer",
                        "thisChange": false,
                        "patchSet": 9,
                        "chainedAt": 9,
                        "url": "https://review.typo3.org/c/Packages/TYPO3.CMS/+/92196"
                    },
                    {
                        "number": 88507,
                        "status": "NEW",
                        "subject": "[WIP][FEATURE] AI suggest demo using tools API",
                        "thisChange": false,
                        "patchSet": 13,
                        "chainedAt": 13,
                        "url": "https://review.typo3.org/c/Packages/TYPO3.CMS/+/88507"
                    },
                    {
                        "number": 93599,
                        "status": "NEW",
                        "subject": "[WIP][TASK] Migrate resource endpoints to Actions API",
                        "thisChange": false,
                        "patchSet": 2,
                        "chainedAt": 2,
                        "url": "https://review.typo3.org/c/Packages/TYPO3.CMS/+/93599"
                    },
                    {
                        "number": 92191,
                        "status": "NEW",
                        "subject": "[TASK] Migrate PageTree to Action API",
                        "thisChange": false,
                        "patchSet": 18,
                        "chainedAt": 18,
                        "url": "https://review.typo3.org/c/Packages/TYPO3.CMS/+/92191"
                    },
                    {
                        "number": 92322,
                        "status": "NEW",
                        "subject": "[TASK] Migrate dashboard to Actions API",
                        "thisChange": false,
                        "patchSet": 11,
                        "chainedAt": 11,
                        "url": "https://review.typo3.org/c/Packages/TYPO3.CMS/+/92322"
                    },
                    {
                        "number": 92724,
                        "status": "ABANDONED",
                        "subject": "[WIP][FEATURE] Implement OAuth authorization server",
                        "thisChange": false,
                        "patchSet": 6,
                        "chainedAt": 6,
                        "url": "https://review.typo3.org/c/Packages/TYPO3.CMS/+/92724"
                    },
                    {
                        "number": 92323,
                        "status": "MERGED",
                        "subject": "[TASK] Avoid `json_encode()` workarounds in Settings API",
                        "thisChange": false,
                        "patchSet": 10,
                        "chainedAt": 8,
                        "url": "https://review.typo3.org/c/Packages/TYPO3.CMS/+/92323"
                    },
                    {
                        "number": 92224,
                        "status": "NEW",
                        "subject": "[WIP][FEATURE] Add MCP Server demo based on Actions API",
                        "thisChange": false,
                        "patchSet": 19,
                        "chainedAt": 19,
                        "url": "https://review.typo3.org/c/Packages/TYPO3.CMS/+/92224"
                    },
                    {
                        "number": 92223,
                        "status": "NEW",
                        "subject": "[WIP][FEATURE] Provide AI Tool provider based on Actions API",
                        "thisChange": false,
                        "patchSet": 16,
                        "chainedAt": 16,
                        "url": "https://review.typo3.org/c/Packages/TYPO3.CMS/+/92223"
                    },
                    {
                        "number": 91486,
                        "status": "NEW",
                        "subject": "[WIP][FEATURE] Implement API Hub",
                        "thisChange": false,
                        "patchSet": 29,
                        "chainedAt": 29,
                        "url": "https://review.typo3.org/c/Packages/TYPO3.CMS/+/91486"
                    },
                    {
                        "number": 93423,
                        "status": "NEW",
                        "subject": "[TASK] Implement standalone redirect route option",
                        "thisChange": false,
                        "patchSet": 7,
                        "chainedAt": 7,
                        "url": "https://review.typo3.org/c/Packages/TYPO3.CMS/+/93423"
                    },
                    {
                        "number": 91666,
                        "status": "NEW",
                        "subject": "[WIP][FEATURE] Provide OpenAPI spec w/ Swagger UI for Actions API",
                        "thisChange": false,
                        "patchSet": 23,
                        "chainedAt": 23,
                        "url": "https://review.typo3.org/c/Packages/TYPO3.CMS/+/91666"
                    },
                    {
                        "number": 91563,
                        "status": "NEW",
                        "subject": "[WIP][FEATURE] Introduce Action API",
                        "thisChange": true,
                        "patchSet": 46,
                        "chainedAt": 46,
                        "url": "https://review.typo3.org/c/Packages/TYPO3.CMS/+/91563"
                    },
                    {
                        "number": 93064,
                        "status": "NEW",
                        "subject": "[TASK] Introduce JSON SchemaBuilder and Schema based Hydrator",
                        "thisChange": false,
                        "patchSet": 16,
                        "chainedAt": 16,
                        "url": "https://review.typo3.org/c/Packages/TYPO3.CMS/+/93064"
                    }
                ],
                "issues": [],
                "releases": [
                    "main"
                ],
                "messages": null,
                "botMessageCount": null
            }
        ],
        "backlog": null,
        "releaseLines": {
            "branches": [
                {
                    "branch": "main",
                    "state": "development",
                    "maintainedUntil": null
                },
                {
                    "branch": "14.3",
                    "state": "maintained",
                    "maintainedUntil": "2029-06-30"
                },
                {
                    "branch": "13.4",
                    "state": "maintained",
                    "maintainedUntil": "2027-12-31"
                }
            ],
            "source": "https://get.typo3.org/api/v1/major/",
            "readAt": "2026-08-05"
        },
        "indistinguishable": null,
        "unavailable": null
    }

gerrit: the open review backlog
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Called with:

.. code-block:: json

    {
        "backlog": "oldest",
        "maxSize": 60,
        "minCodeReview": 1,
        "negativeVotes": false,
        "mergeable": true,
        "limit": 3
    }

Text:

.. code-block:: text

    TYPO3 core review server: https://review.typo3.org
    Query: project:"Packages/TYPO3.CMS" status:open -is:wip delta:<=60 label:Code-Review>=1 -label:Code-Review<=-1 -label:Verified<=-1 is:mergeable
    3 of 38 open core changes, oldest pushed first.
    This is a page and not the set, and limit stops at 25. What comes after it is reached by a narrower filter — a smaller maxSize, a branch, an earlier updatedBefore — rather than by a larger limit, because more of one order is more of the same end.
    Age is a candidate and never a finding. The oldest changes are regularly the largest and the ones that no longer merge, so maxSize and mergeable are what turn this order into a shortlist — and what a change is actually waiting on is read by passing its number back as change, which answers the votes with their voters and the comments with their threads.
    `typo3-core-patch-review` is the workflow a change picked off this page opens, and `typo3-core-patch-checkout` is what gets the patch set into a checkout. Open the one this task is before reading a diff.

    ## [BUGFIX] Ensure invalid pages should not stop DataHandler (NEW)
    Change 90384 · main · https://review.typo3.org/c/Packages/TYPO3.CMS/+/90384
    Change-Id: Idd97aba049114c733f3b5f3367ba7845a6b081db
    Patch set 2
    Fetch: git fetch https://review.typo3.org/Packages/TYPO3.CMS refs/changes/84/90384/2
    Last moved: 2026-06-18 23:04:54.000000000
    +7 -1 · merges · 2 unresolved of 5 comments · pushed 2025-08-13
    Verified: needs a vote
    Code-Review: needs a vote

    ## [FEATURE] Add H6 in header layouts (NEW)
    Change 91431 · main · https://review.typo3.org/c/Packages/TYPO3.CMS/+/91431
    Change-Id: Iefe08029a0dc95c061ce94d39ed7781d02640b20
    Patch set 9
    Fetch: git fetch https://review.typo3.org/Packages/TYPO3.CMS refs/changes/31/91431/9
    Last moved: 2026-08-04 14:32:30.000000000
    +37 -0 · merges · pushed 2025-11-06
    Verified: needs a vote
    Code-Review: needs a vote

    ## [TASK] Change appearance and position of Install Tool login buttons (NEW)
    Change 91606 · main · https://review.typo3.org/c/Packages/TYPO3.CMS/+/91606
    Change-Id: Ifdb6cc9273a837a6cce72ce310ffcd05e800acf4
    Patch set 4
    Fetch: git fetch https://review.typo3.org/Packages/TYPO3.CMS refs/changes/06/91606/4
    Last moved: 2026-08-06 16:20:31.000000000
    +2 -2 · merges · pushed 2025-11-13
    Verified: needs a vote
    Code-Review: needs a vote

    The branches that take a patch today, whichever one the change above targets: main is the development line, which every core change is written against first; 14.3 is in regular support until 2029-06-30; 13.4 is in regular support until 2027-12-31. Read from https://get.typo3.org/api/v1/major/ on 2026-08-05; a core clone carries no such list, since "git branch -r" reaches back to TYPO3_3-6 and says nothing about which of those is still maintained. Which of these a change belongs on is the author's claim rather than a consequence of the list — `typo3_commit_message_guide` with `workflow="core"` is what reads a `Releases:` trailer against them.

    The fetch goes to the review server rather than to `origin`: a core clone fetches from the GitHub mirror, where `refs/changes/…` does not exist. `git switch --detach FETCH_HEAD` is what puts the checkout on the patch set afterwards.

Data:

.. code-block:: json

    {
        "status": "answered",
        "source": "https://review.typo3.org",
        "query": "project:\"Packages/TYPO3.CMS\" status:open -is:wip delta:<=60 label:Code-Review>=1 -label:Code-Review<=-1 -label:Verified<=-1 is:mergeable",
        "changes": [
            {
                "number": 90384,
                "changeId": "Idd97aba049114c733f3b5f3367ba7845a6b081db",
                "subject": "[BUGFIX] Ensure invalid pages should not stop DataHandler",
                "status": "NEW",
                "branch": "main",
                "patchSet": 2,
                "commit": "",
                "project": "Packages/TYPO3.CMS",
                "updated": "2026-06-18 23:04:54.000000000",
                "created": "2025-08-13 22:31:24.000000000",
                "insertions": 7,
                "deletions": 1,
                "mergeable": true,
                "url": "https://review.typo3.org/c/Packages/TYPO3.CMS/+/90384",
                "fetch": {
                    "ref": "refs/changes/84/90384/2",
                    "remote": "https://review.typo3.org/Packages/TYPO3.CMS"
                },
                "labels": [
                    {
                        "label": "Verified",
                        "state": "NEED",
                        "satisfied": false,
                        "votes": null
                    },
                    {
                        "label": "Code-Review",
                        "state": "NEED",
                        "satisfied": false,
                        "votes": null
                    }
                ],
                "commentCount": 5,
                "unresolvedCommentCount": 2,
                "comments": null,
                "chain": null,
                "issues": null,
                "releases": null,
                "messages": null,
                "botMessageCount": null
            },
            {
                "number": 91431,
                "changeId": "Iefe08029a0dc95c061ce94d39ed7781d02640b20",
                "subject": "[FEATURE] Add H6 in header layouts",
                "status": "NEW",
                "branch": "main",
                "patchSet": 9,
                "commit": "",
                "project": "Packages/TYPO3.CMS",
                "updated": "2026-08-04 14:32:30.000000000",
                "created": "2025-11-06 09:59:04.000000000",
                "insertions": 37,
                "deletions": 0,
                "mergeable": true,
                "url": "https://review.typo3.org/c/Packages/TYPO3.CMS/+/91431",
                "fetch": {
                    "ref": "refs/changes/31/91431/9",
                    "remote": "https://review.typo3.org/Packages/TYPO3.CMS"
                },
                "labels": [
                    {
                        "label": "Verified",
                        "state": "NEED",
                        "satisfied": false,
                        "votes": null
                    },
                    {
                        "label": "Code-Review",
                        "state": "NEED",
                        "satisfied": false,
                        "votes": null
                    }
                ],
                "commentCount": 29,
                "unresolvedCommentCount": 0,
                "comments": null,
                "chain": null,
                "issues": null,
                "releases": null,
                "messages": null,
                "botMessageCount": null
            },
            {
                "number": 91606,
                "changeId": "Ifdb6cc9273a837a6cce72ce310ffcd05e800acf4",
                "subject": "[TASK] Change appearance and position of Install Tool login buttons",
                "status": "NEW",
                "branch": "main",
                "patchSet": 4,
                "commit": "",
                "project": "Packages/TYPO3.CMS",
                "updated": "2026-08-06 16:20:31.000000000",
                "created": "2025-11-13 09:18:48.000000000",
                "insertions": 2,
                "deletions": 2,
                "mergeable": true,
                "url": "https://review.typo3.org/c/Packages/TYPO3.CMS/+/91606",
                "fetch": {
                    "ref": "refs/changes/06/91606/4",
                    "remote": "https://review.typo3.org/Packages/TYPO3.CMS"
                },
                "labels": [
                    {
                        "label": "Verified",
                        "state": "NEED",
                        "satisfied": false,
                        "votes": null
                    },
                    {
                        "label": "Code-Review",
                        "state": "NEED",
                        "satisfied": false,
                        "votes": null
                    }
                ],
                "commentCount": 2,
                "unresolvedCommentCount": 0,
                "comments": null,
                "chain": null,
                "issues": null,
                "releases": null,
                "messages": null,
                "botMessageCount": null
            }
        ],
        "backlog": {
            "order": "oldest",
            "read": 38,
            "complete": true
        },
        "releaseLines": {
            "branches": [
                {
                    "branch": "main",
                    "state": "development",
                    "maintainedUntil": null
                },
                {
                    "branch": "14.3",
                    "state": "maintained",
                    "maintainedUntil": "2029-06-30"
                },
                {
                    "branch": "13.4",
                    "state": "maintained",
                    "maintainedUntil": "2027-12-31"
                }
            ],
            "source": "https://get.typo3.org/api/v1/major/",
            "readAt": "2026-08-05"
        },
        "indistinguishable": null,
        "unavailable": null
    }

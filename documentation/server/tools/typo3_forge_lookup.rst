.. _typo3_forge_lookup:

``typo3_forge_lookup``
======================

Read the TYPO3 issue tracker at forge.typo3.org before writing a patch. Pass
issue with a number to read that one: subject, tracker, status, target version,
the TYPO3 and PHP versions it was reported against, the related issues with
their subjects, the review changes its comments name, the files hanging off it —
which on a report about rendering is where the evidence usually is — and the
comments, where a maintainer who closed or reassigned it said why, which the
description never says. Or pass query with words to find out which other issues
describe the same thing, which the relations of one issue only answer for what
somebody linked by hand. Or pass open to enumerate the core project's unresolved
issues without holding a number or a wording — oldest filed or longest
untouched, narrowed by tracker and by date, which is where a triage of the
backlog starts; the count of everything that matched comes back with the page,
so a limited answer says whether it is the whole set. Each entry carries its
number, subject, tracker, status and URL, and an enumerated one also carries the
issues it is filed against with their subjects, the files hanging off it, and
the changes on review.typo3.org whose commit message names it — the three that
say a row was answered elsewhere or already attempted, without reading it whole.
A call carries issue, query or open, never two of them. An issue that does not
exist is answered as such, and so is a tracker that could not be reached.
Reading only, and no credential: commenting, assigning and closing stay yours.
Answers from: network.

``readOnlyHint: true`` · ``destructiveHint: false`` · ``idempotentHint: true`` · ``openWorldHint: true``

Answers from :ref:`network <answer-sources-network>`.

Takes
-----

.. code-block:: yaml

    # Forge issue number, with or without the leading #, for example "110348". Reads
    # that one issue whole, comments included — narrow those with notes when
    # reading many. A call carries issue, query or open, never two of them.
    issue: string  # optional
    # Words to search the tracker for, for example "image cache busting". Answers
    # the issues whose text matches them — which is how a duplicate nobody has
    # linked is found at all, since the relations of an issue only carry what
    # somebody linked by hand. Nothing is ranked and one wording does not settle it:
    # ask again in the reporter's words as well as your own, because an issue worded
    # differently is invisible to this. A call carries issue, query or open, never
    # two of them.
    query: string  # optional
    # One of: oldest, stale. Enumerate the core project's unresolved issues instead
    # of reading one or matching words: "oldest" orders them by when they were
    # filed, "stale" by how long nobody has touched them. The two answer different
    # questions about one backlog — filed long ago is about the report, untouched
    # for years is about the attention it got — and an issue that is both is the
    # candidate a triage is looking for. Unresolved is the tracker's own set of open
    # statuses, so New, Accepted, Under Review, Needs Feedback, On Hold and
    # Postponed are all in it. Narrow with tracker, createdBefore and updatedBefore.
    # A call carries issue, query or open, never two of them.
    open: string  # optional
    # One of: all, people. Which comments come back with an issue. "all" is every
    # one of them and is what you want when reading a single issue — the comments
    # are where the decision is, and on a report worth reading the one that settles
    # it is regularly the last of sixteen. "people" drops the patch-set pings a
    # review bot wrote, which on some issues is half the volume and carries nothing
    # a reader was going to use; the change numbers in them are lifted into reviews
    # either way, so nothing is lost by it. Ask for it when sweeping candidates,
    # where the cost of reading ten issues is what decides whether the comments get
    # read at all. How many were dropped is answered whichever you ask for. Narrows
    # issue and is ignored by query and open.
    notes: string  # optional
    # One of: Bug, Feature, Major Feature, Support, Task, Story, Suggestion,
    # Impediment, Epic, Work Package, Topic. Only issues filed under this tracker,
    # for example "Bug". Worth setting before reading a set: an old Bug and an old
    # Feature are two different findings, since one claims something is broken today
    # and the other that something was wanted once. Narrows open and is ignored by
    # issue and query.
    tracker: string  # optional
    # Only issues the core files under this area, in your own words: "rte", "backend
    # ui", "workspaces", "fluid". Matched against the project's own category names
    # one word at a time, so a half-remembered name reaches the right area and a
    # word naming several — "backend" — selects all of them and says which. That
    # is the way in for "are there known bugs in the RTE" and "the oldest issues in
    # the backend UI", which no wording of the report itself reaches. The categories
    # that exist come back with every answer, so a word matching none is corrected
    # without a second call. Narrows open and is ignored by issue and query.
    category: string  # optional
    # Only issues filed before this day, as YYYY-MM-DD. Narrows open and is ignored
    # by issue and query.
    createdBefore: string  # optional
    # Only issues nobody has touched since this day, as YYYY-MM-DD. This is the one
    # that finds a report everybody has walked past, which age alone does not: an
    # issue filed in 2009 and commented on last month is being worked. Narrows open
    # and is ignored by issue and query.
    updatedBefore: string  # optional
    # How many entries come back. A search answers with at most 25 whatever is asked
    # for, because a set that has to be paged through is answered by other words
    # rather than by more of these.
    limit: integer  # optional

The call carries exactly one of these sets of arguments: ``issue`` — or
``query`` — or ``open``.

Answers with
------------

.. code-block:: yaml

    # One of: answered, empty, unavailable.
    status: string
    # The tracker the answer came from.
    source: string
    # What was read, so the same question can be asked again by hand.
    url: string
    # The words the tracker was searched for, so a set that looks too narrow can be
    # asked again in other words. Empty where an issue was read by number and where
    # the open issues were enumerated.
    query: string
    # How many issues matched in total, of which results carries at most limit.
    # Where the two differ the answer is a page and not the set, and asking for more
    # of it is a narrower filter rather than a bigger limit. Zero where an issue was
    # read by number.
    total: integer
    # Every area the core files its issues under, read from the project itself.
    # Answered on every enumeration, so a category word that matched none is
    # corrected from the answer rather than from a second call. Empty where an issue
    # was read by number and where words were searched for.
    categories: [string]
    # The categories the category word resolved to, in the tracker's own spelling.
    # Empty where none was asked for — and empty where the word matched none,
    # which is answered as no issues and is a statement about the word rather than
    # about the backlog.
    categoriesUsed: [string]
    # The issue, where status says answered and a number was asked for. Null
    # otherwise.
    issue:
      id: integer
      subject: string
      # New, Accepted, Resolved, Closed, Rejected — the tracker's own word.
      status: string
      # Bug, Feature, Task, Epic.
      tracker: string
      priority: string
      # Who the tracker says holds this, empty where nobody does. An assignee is not
      # a promise that somebody is working on it — on an issue nothing has moved
      # on for years it is usually who last did.
      assignedTo: string
      # The release it is scheduled for, empty where none is set.
      targetVersion: string
      # The TYPO3 version it was reported against, which is not the version it still
      # reproduces on.
      typo3Version: string
      phpVersion: string
      createdOn: string
      updatedOn: string
      # Where a person reads it.
      url: string
      # The report as it was written, which is what the reporter saw and not what
      # was decided.
      description: string
      # Issues this one is filed against, which is where a duplicate, a blocker, and
      # the issue a revert was filed under are named. Each carries its subject, so
      # which of them is worth reading is decided from here rather than from one
      # call each.
      relations:
        - # The other issue.
          issue: integer
          # duplicates, relates, blocked, precedes.
          relation: string
          # What the other issue is about, so it can be judged without being read.
          # Empty where the tracker did not answer the one call that fills the whole
          # set.
          subject: string
          # Bug, Feature, Task.
          tracker: string
          # Where the other issue stands.
          status: string
          # Where a person reads it.
          url: string
      # The review changes the journal names, lifted out of the prose that carries
      # them. Nothing here says what state a change is in: a note says what was true
      # the day it was written, and typo3_gerrit_lookup answers what is true now.
      # Empty where no note named one.
      reviews:
        - # The change number on review.typo3.org, which is what typo3_gerrit_lookup
          # takes as change.
          change: integer
          # The Change-Id the commit message carries, empty where no note named one.
          # typo3_gerrit_lookup takes this too, and it is what survives a rebase
          # onto another branch.
          changeId: string
          # The highest patch set a note mentioned, zero where none did. The review
          # server may be further along.
          patchSet: integer
          # When the last note naming this change was written, which is how old the
          # reference is and not when the change last moved.
          on: string
          # Where a person reads the change.
          url: string
      # The files hanging off the issue. On a report about rendering these are
      # usually screenshots, and they are regularly where the evidence is: a comment
      # that consists of !image.jpg! references reads as an empty comment otherwise.
      # Empty where the issue carries none.
      attachments:
        - # The name the file was uploaded under, which is also how a comment refers
          # to it: Redmine writes an inline image as !name.png! and the text around
          # it says nothing else about it.
          filename: string
          # image/png, image/jpeg, text/plain.
          contentType: string
          # Bytes.
          size: integer
          # When it was uploaded, which is what says which comment it belongs to.
          on: string
          # Where the file itself is. It answers without a credential, and reading
          # it is the caller's: nothing here fetches or transcribes one.
          url: string
      # How many comments the issue carries in total.
      noteCount: integer
      # How many of those a review bot wrote, which notes: "people" is what drops.
      # Answered whichever way notes was asked, so a journal full of patch-set pings
      # answering zero here is the list of bot names gone stale rather than an issue
      # nobody pushed a patch for.
      botNoteCount: integer
      # The most recent comments, oldest first. A closure, a reassignment and a "we
      # will not do this" are here rather than in the description.
      notes:
        - author: string
          on: string
          note: string
    # The issues the query matched or the enumeration selected, in the tracker's own
    # order — nothing here ranks them, and what an entry is worth is the caller's
    # to judge. Empty where an issue was read by number.
    results:
      - # The issue number, which is what this tool reads whole.
        issue: integer
        subject: string
        # Bug, Feature, Task, Epic.
        tracker: string
        # Where it stands: New, Accepted, Under Review, Resolved, Closed, Rejected.
        status: string
        # The area the core files it under, empty where none is set. A search hit is
        # a title and carries none of the four fields below, so they are read for
        # the whole page in one further call — and empty here means that call did
        # not reach the tracker rather than that the issue has no area.
        category: string
        # Who the tracker says holds this, empty where nobody does. What it decides
        # for a triage is whether the issue is free to take, and on an old one it is
        # usually who last touched it rather than who is on it.
        assignedTo: string
        # When it was filed.
        createdOn: string
        # When anything last moved on it, which is the measure of neglect rather
        # than of age.
        updatedOn: string
        # Where a person reads it.
        url: string
        # The issues this one is filed against, each with its subject, so a row that
        # duplicates something already decided is seen without being read. Answered
        # on an enumeration and empty on a search hit, where nothing asked for them.
        relations:
          - # The other issue.
            issue: integer
            # duplicates, relates, blocked, precedes.
            relation: string
            # What the other issue is about, so it can be judged without being read.
            # Empty where the tracker did not answer the one call that fills the
            # whole set.
            subject: string
            # Bug, Feature, Task.
            tracker: string
            # Where the other issue stands.
            status: string
            # Where a person reads it.
            url: string
        # The files hanging off the issue, which on a report about rendering are
        # usually where the evidence is — and a report whose evidence is a
        # screenshot is a different candidate to one whose evidence is prose.
        # Answered on an enumeration and empty on a search hit, where nothing asked
        # for them.
        attachments:
          - # The name the file was uploaded under, which is also how a comment
            # refers to it: Redmine writes an inline image as !name.png! and the
            # text around it says nothing else about it.
            filename: string
            # image/png, image/jpeg, text/plain.
            contentType: string
            # Bytes.
            size: integer
            # When it was uploaded, which is what says which comment it belongs to.
            on: string
            # Where the file itself is. It answers without a credential, and reading
            # it is the caller's: nothing here fetches or transcribes one.
            url: string
        # The changes whose commit message names this issue, asked of the review
        # server in one query for the whole page. A handle and not a verdict:
        # whether a change is merged, open or abandoned is a typo3_gerrit_lookup
        # call, and a change named here is what makes that call worth one. Empty
        # where nothing on the review server names the issue and where the review
        # server did not answer, which this does not separate — and empty on a
        # search hit, where it is not asked.
        reviews:
          - # The change number on review.typo3.org, which is what
            # typo3_gerrit_lookup takes as change.
            change: integer
            # Where a person reads the change.
            url: string
    # Why nothing was answered, where status says unavailable. Null otherwise.
    unavailable:
      # One of: source-not-answering, source-not-parseable. source-not-answering:
      # the tracker did not answer this time. source-not-parseable: something
      # answered with a page rather than with the API, which is what the bot
      # protection in front of it looks like from here.
      cause: string
      reason: string

Answered
--------

Recorded on 2026-08-09 by ``bin/cli tools:record``. Answered against
core-checkout, TYPO3 14.3.6-dev, the 14.3 core checkout below .checkouts/,
whose console could not be reached: <installation> has no TYPO3 console —
none of bin/typo3, vendor/bin/typo3 exists. Nothing checks what is below this
heading; everything above it is derived from the class that answers the call,
and ``bin/cli tools:check`` holds it.

forge: what an issue says and what was decided
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Called with:

.. code-block:: json

    {
        "issue": "110348"
    }


Text:

.. code-block:: text

    #110348 Rework AdminPanel "imagesOnPage" feature
    Task · Resolved · priority Should have · https://forge.typo3.org/issues/110348
    Assigned to Benni Mack — which says who holds it and not that somebody is working on it.
    Target version: 15.0
    Reported against TYPO3 15 — which is what the reporter had, not what it still reproduces on.

    ## Reported
    The "imagesOnPage" feature is older than git. It needs to be revised to be integrated into FAL.

    ## Changes on review.typo3.org (1)
    Named in the comments below and lifted out of them. What state one is in now is a typo3_gerrit_lookup call — pass the number as change, or the Change-Id, which is what survives a rebase onto another branch. A comment says what was true the day it was written.
    - change 95040 · patch set 2 · named 2026-08-01 · https://review.typo3.org/c/95040

    ## Comments (3 of 3, oldest first)
    What was decided is here rather than above.

    **Gerrit Code Review**, 2026-07-31T19:23:24Z
    Patch set 1 for branch *main* of project *Packages/TYPO3.CMS* has been pushed to the review server.
    It is available at https://review.typo3.org/c/Packages/TYPO3.CMS/+/95040

    **Gerrit Code Review**, 2026-08-01T06:13:04Z
    Patch set 2 for branch *main* of project *Packages/TYPO3.CMS* has been pushed to the review server.
    It is available at https://review.typo3.org/c/Packages/TYPO3.CMS/+/95040

    **Benni Mack**, 2026-08-02T20:45:10Z
    Applied in changeset commit:e82b930e6e0587842427496c5ce01f625b27fb66.


Data:

.. code-block:: json

    {
        "status": "answered",
        "source": "https://forge.typo3.org",
        "url": "https://forge.typo3.org/issues/110348.json?include=journals,relations,attachments",
        "query": "",
        "total": 0,
        "categories": [],
        "categoriesUsed": [],
        "issue": {
            "id": 110348,
            "subject": "Rework AdminPanel \"imagesOnPage\" feature",
            "status": "Resolved",
            "tracker": "Task",
            "priority": "Should have",
            "assignedTo": "Benni Mack",
            "targetVersion": "15.0",
            "typo3Version": "15",
            "phpVersion": "",
            "createdOn": "2026-07-31T18:06:11Z",
            "updatedOn": "2026-08-02T20:45:10Z",
            "url": "https://forge.typo3.org/issues/110348",
            "description": "The \"imagesOnPage\" feature is older than git. It needs to be revised to be integrated into FAL.",
            "relations": [],
            "attachments": [],
            "reviews": [
                {
                    "change": 95040,
                    "changeId": "",
                    "patchSet": 2,
                    "on": "2026-08-01T06:13:04Z",
                    "url": "https://review.typo3.org/c/95040"
                }
            ],
            "noteCount": 3,
            "botNoteCount": 2,
            "notes": [
                {
                    "author": "Gerrit Code Review",
                    "on": "2026-07-31T19:23:24Z",
                    "note": "Patch set 1 for branch *main* of project *Packages/TYPO3.CMS* has been pushed to the review server.\nIt is available at https://review.typo3.org/c/Packages/TYPO3.CMS/+/95040"
                },
                {
                    "author": "Gerrit Code Review",
                    "on": "2026-08-01T06:13:04Z",
                    "note": "Patch set 2 for branch *main* of project *Packages/TYPO3.CMS* has been pushed to the review server.\nIt is available at https://review.typo3.org/c/Packages/TYPO3.CMS/+/95040"
                },
                {
                    "author": "Benni Mack",
                    "on": "2026-08-02T20:45:10Z",
                    "note": "Applied in changeset commit:e82b930e6e0587842427496c5ce01f625b27fb66."
                }
            ]
        },
        "results": [],
        "unavailable": null
    }


forge: an issue whose evidence hangs off it
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Called with:

.. code-block:: json

    {
        "issue": "88556"
    }


Text:

.. code-block:: text

    #88556 One line break in DB field causes 3 rendered p-tags in CKEditor
    Bug · Resolved · priority Should have · https://forge.typo3.org/issues/88556
    Assigned to nobody.
    Target version: Candidate for patchlevel
    Reported against TYPO3 12, PHP 8.2 — which is what the reporter had, not what it still reproduces on.
    Relation: relates #96466 — Bug · Rejected · RTE parse func paragraph duplication bug

    ## Reported
    <pre><code class="html">
    <p>Hello World
    </p><ul><li>foo bar</li></ul>
    </code></pre>
    
    When writing this into a DB field with enabled RTE it causes 3 additional empty p-tags in CKEditor. These can be saved too. See attachment for a sample.
    
    Not sure whether this is a CKEditor or TYPO3 related issue.

    ## Changes on review.typo3.org (3)
    Named in the comments below and lifted out of them. What state one is in now is a typo3_gerrit_lookup call — pass the number as change, or the Change-Id, which is what survives a rebase onto another branch. A comment says what was true the day it was written.
    - change 95108 · patch set 1 · named 2026-08-05 · https://review.typo3.org/c/95108
    - change 95131 · patch set 1 · named 2026-08-06 · https://review.typo3.org/c/95131
    - change 95132 · patch set 1 · named 2026-08-06 · https://review.typo3.org/c/95132

    ## Attachments (7)
    On a report about rendering these are usually where the evidence is, and Redmine writes an inline image into a comment as !filename! — so a comment below that is nothing but a filename is referring to one of these. Read the ones the report turns on; this server does not fetch them.
    - ckeditor-3-p-tags.png · image/png · 15 kB · 2019-06-13 · https://forge.typo3.org/attachments/download/34363/ckeditor-3-p-tags.png
    - db_field_value.jpg · image/jpeg · 17 kB · 2023-08-07 · https://forge.typo3.org/attachments/download/37897/db_field_value.jpg
    - rte_view.jpg · image/jpeg · 11 kB · 2023-08-07 · https://forge.typo3.org/attachments/download/37898/rte_view.jpg
    - rte_view_sourcecode.jpg · image/jpeg · 21 kB · 2023-08-07 · https://forge.typo3.org/attachments/download/37899/rte_view_sourcecode.jpg
    - fe_output.jpg · image/jpeg · 17 kB · 2023-08-07 · https://forge.typo3.org/attachments/download/37900/fe_output.jpg
    - fe_output_sourcecode.jpg · image/jpeg · 32 kB · 2023-08-07 · https://forge.typo3.org/attachments/download/37901/fe_output_sourcecode.jpg
    - db_field_value_wo_linebreak.jpg · image/jpeg · 15 kB · 2023-08-07 · https://forge.typo3.org/attachments/download/37902/db_field_value_wo_linebreak.jpg

    ## Comments (15 of 15, oldest first)
    What was decided is here rather than above.

    **Benni Mack**, 2019-06-13T13:37:33Z
    Are you using the RTE in a FlexForm?

    **Alex Nostadt**, 2019-06-13T13:40:26Z
    Benni Mack wrote:
    > Are you using the RTE in a FlexForm?
    
    No, I use it in a TCA.
    
    When removing the single existing line break between "World" and closing p-tag no additional line breaks are created. I have switched temporary to T3's default preset but the behaviour was the same.
    
    That's my TCA field config:
    <pre><code class="php">
    <?php
            'myField' => [
                'exclude' => 1,
                'label' => $ll . 'myField',
                'config' => [
                    'type' => 'text',
                    'cols' => 40,
                    'rows' => 15,
                    'eval' => 'trim',
                    'enableRichtext' => true,
                ],
            ],
    </code></pre>

    **Riccardo De Contardi**, 2019-06-29T21:50:09Z
    Is this somehow related? #88655

    **Alex Nostadt**, 2019-07-02T12:07:18Z
    Riccardo De Contardi wrote:
    > Is this somehow related? #88655
    
    I will check within this week.

    **Alex Nostadt**, 2019-07-08T18:57:16Z
    I don't think this is related as #88655 is about ignored RTE config. whereas this issue is rather related to DB=>CKEditor parse-process.

    **Benni Mack**, 2019-08-16T12:46:29Z
    Alex Nostadt wrote:
    > I don't think this is related as #88655 is about ignored RTE config. whereas this issue is rather related to DB=>CKEditor parse-process.
    
    Can you share your CKEditor config? (if you have a manually configured RTE configuration) just to be sure.

    **Alex Nostadt**, 2019-08-17T09:05:59Z
    Benni Mack wrote:
    > Alex Nostadt wrote:
    > > I don't think this is related as #88655 is about ignored RTE config. whereas this issue is rather related to DB=>CKEditor parse-process.
    > 
    > Can you share your CKEditor config? (if you have a manually configured RTE configuration) just to be sure.
    
    I could reproduce it with the default T3 config as well but can provide it as well within the next days.

    **Alex Nostadt**, 2019-08-26T15:38:15Z
    Alex Nostadt wrote:
    > Benni Mack wrote:
    > > Alex Nostadt wrote:
    > > > I don't think this is related as #88655 is about ignored RTE config. whereas this issue is rather related to DB=>CKEditor parse-process.
    > > 
    > > Can you share your CKEditor config? (if you have a manually configured RTE configuration) just to be sure.
    > 
    > I could reproduce it with the default T3 config as well but can provide it as well within the next days.
    
    Sorry, forgot this ticket. Add this to my priority list now.

    **Alex Nostadt**, 2019-08-30T08:42:51Z
    That's the RTE configuration:
    (I include Specific.yaml. We have multiple "Specific.yaml" files and our Default.yaml is our common denominator)
    *Default.yaml*
    <pre><code class="yaml">
    imports:
        - { resource: "EXT:rte_ckeditor/Configuration/RTE/Processing.yaml" }
        - { resource: "EXT:rte_ckeditor/Configuration/RTE/Editor/Base.yaml" }
        - { resource: "EXT:rte_ckeditor/Configuration/RTE/Editor/Plugins.yaml" }
        - { resource: "EXT:rte_ckeditor_image/Configuration/RTE/Plugin.yaml" }
    
    processing:
        ## allowed default attributes (added by us is..: "style")
        allowAttributes: [class, id, title, dir, lang, xml:lang, itemscope, itemtype, itemprop, style]
    
    editor:
        externalPlugins:
            find:
                resource: "EXT:provider/Resources/Public/CKEditor/Plugins/find/"
    
        config:
            # can be "default", but a custom stylesSet can be defined here, which fits TYPO3 best
            stylesSet:
                - { name: "LLL:EXT:provider/Resources/Private/Language/rte.xlf:align-center", element: "p", styles: { text-align: "center"} }
                - { name: "LLL:EXT:provider/Resources/Private/Language/rte.xlf:justify", element: "p", styles: { text-align: "justify"} }
                - { name: "LLL:EXT:provider/Resources/Private/Language/rte.xlf:author", element: "p", attributes: { class: 'author' } }
                - { name: "LLL:EXT:provider/Resources/Private/Language/rte.xlf:citation", element: "p", attributes: { class: 'citation' } }
                - { name: "LLL:EXT:provider/Resources/Private/Language/rte.xlf:check", element: ["ul"], attributes: { class: 'check' } }
    
            toolbarGroups:
                - { name: styles, groups: [ styles ] }
                - "/"
                - { name: editing, groups: [ find, selection, spellchecker, editing ] }
                - { name: forms, groups: [ forms ] }
                - { name: basicstyles, groups: [ basicstyles, cleanup ] }
                - { name: paragraph, groups: [ list, indent, blocks, align, bidi, paragraph ] }
                - { name: links, groups: [ links ] }
                - { name: insert, groups: [ insert ] }
                - { name: colors, groups: [ colors ] }
                - { name: others, groups: [ others ] }
                - "/"
                - { name: clipboard, groups: [ clipboard, undo ] }
                - { name: document, groups: [ mode, document, doctools ] }
                - { name: tools, groups: [ tools ] }
                - { name: about, groups: [ about ] }
    
            format_tags: "p;h1;h2;h3;h4"
    
            justifyClasses:
                - text-left
                - text-center
                - text-right
                - text-justify
    
            buttons:
                link:
                    url:
                        properties:
                            class:
                                default: 'external-link'
                    properties:
                        class:
                            allowedClasses: 'external-link'
    
            classes:
                external-link:
                    name: 'External Link'
    
            classesAnchor:
                externalLink:
                    class: 'external-link'
                    type: 'url'
                    target: '_blank'
    
            removeButtons:
                - Save
                - NewPage
                - Preview
                - Print
                - Templates
                - Cut
                - Copy
                - Paste
                - PasteFromWord
                - Form
                - Checkbox
                - Radio,
                - TextField
                - Textarea
                - Select
                - Button
                - ImageButton
                - HiddenField
                - Outdent
                - Indent
                - Flash
                - HorizontalRule
                - Smiley
                - PageBreak
                - Iframe
                - Font
                - FontSize
                - TextColor
                - BGColor
                - ShowBlocks
                - Blockquote
                - About
    </code></pre>
    
    *Specific.yaml*
    <pre><code class="yaml">
    imports:
        - { resource: "EXT:provider/Configuration/RTE/Default.yaml" }
    
    editor:
        externalPlugins:
            placeholder_select:
                resource: "EXT:provider/Resources/Public/CKEditor/Plugins/placeholder_select/"
    
        config:
            contentsCss: ["EXT:rte_ckeditor/Resources/Public/Css/contents.css", "EXT:provider/Resources/Public/Css/editor.css"]
            # can be "default", but a custom stylesSet can be defined here, which fits TYPO3 best
            stylesSet:
                - { name: "LLL:EXT:provider/Resources/Private/Language/rte.xlf:large", element:  ["p", "ul", "ol", "h1", "h2", "h3"], attributes: { class: 'large' } }
                - { name: "LLL:EXT:provider/Resources/Private/Language/rte.xlf:teaser-text", element: "p", attributes: { class: 'teaser-text' } }
                - { name: "LLL:EXT:provider/Resources/Private/Language/rte.xlf:light-gray", element: ["p", "ul", "ol"], attributes: { class: 'light-gray' } }
                - { name: "LLL:EXT:provider/Resources/Private/Language/rte.xlf:all-borders", element: "table", attributes: { class: 'all-borders' } }
                - { name: "LLL:EXT:provider/Resources/Private/Language/rte.xlf:horizontal-borders", element: "table", attributes: { class: 'horizontal-borders' } }
                - { name: "LLL:EXT:provider/Resources/Private/Language/rte.xlf:vertical-borders", element: "table", attributes: { class: 'vertical-borders' } }
    
    </code></pre>

    **David Menzel**, 2023-08-07T18:58:28Z
    We have the same problem since (at least) TYPO3 10. Now using TYPO3 11.
    
    A bodytext database field has the following text:
    
    !db_field_value.jpg!
    
    The RTE ckeditor looks like this:
    
    !rte_view.jpg!
    
    RTE ckeditor Source code view:
    
    !rte_view_sourcecode.jpg!
    
    Output in FE:
    
    !fe_output.jpg!
    
    FE source code:
    
    !fe_output_sourcecode.jpg!
    
    You notice the additional empty p-Tags before/after the pre-tag?
    
    However, when I remove the linebreaks in the database field:
    !db_field_value_wo_linebreak.jpg!
    
    the FE works and there are no additional empty p-tags.
    When I save the RTE field again, the empty p-tags are back again because the linebreaks are back in the db field.
    
    Not sure why it only happens before/after the pre-tag.
    
    TYPO3 11.5.30 and PHP 7.4

    **David Menzel**, 2025-03-06T05:47:19Z
    Problem still exists in TYPO3 12.4.27 and CKEditor 5.
    
    There's an additional empty <p>-Tag before and after the codeblock.

    **Gerrit Code Review**, 2026-08-05T03:25:06Z
    Patch set 1 for branch *main* of project *Packages/TYPO3.CMS* has been pushed to the review server.
    It is available at https://review.typo3.org/c/Packages/TYPO3.CMS/+/95108

    **Gerrit Code Review**, 2026-08-06T19:26:43Z
    Patch set 1 for branch *14.3* of project *Packages/TYPO3.CMS* has been pushed to the review server.
    It is available at https://review.typo3.org/c/Packages/TYPO3.CMS/+/95131

    **Gerrit Code Review**, 2026-08-06T19:26:58Z
    Patch set 1 for branch *13.4* of project *Packages/TYPO3.CMS* has been pushed to the review server.
    It is available at https://review.typo3.org/c/Packages/TYPO3.CMS/+/95132

    **Benjamin Kott**, 2026-08-06T20:15:08Z
    Applied in changeset commit:b406a9416431d1945756ce418d9c3726844f5325.


Data:

.. code-block:: json

    {
        "status": "answered",
        "source": "https://forge.typo3.org",
        "url": "https://forge.typo3.org/issues/88556.json?include=journals,relations,attachments",
        "query": "",
        "total": 0,
        "categories": [],
        "categoriesUsed": [],
        "issue": {
            "id": 88556,
            "subject": "One line break in DB field causes 3 rendered p-tags in CKEditor",
            "status": "Resolved",
            "tracker": "Bug",
            "priority": "Should have",
            "assignedTo": "",
            "targetVersion": "Candidate for patchlevel",
            "typo3Version": "12",
            "phpVersion": "8.2",
            "createdOn": "2019-06-13T13:35:40Z",
            "updatedOn": "2026-08-06T20:15:08Z",
            "url": "https://forge.typo3.org/issues/88556",
            "description": "<pre><code class=\"html\">\r\n<p>Hello World\r\n</p><ul><li>foo bar</li></ul>\r\n</code></pre>\r\n\r\nWhen writing this into a DB field with enabled RTE it causes 3 additional empty p-tags in CKEditor. These can be saved too. See attachment for a sample.\r\n\r\nNot sure whether this is a CKEditor or TYPO3 related issue.",
            "relations": [
                {
                    "issue": 96466,
                    "relation": "relates",
                    "url": "https://forge.typo3.org/issues/96466",
                    "subject": "RTE parse func paragraph duplication bug",
                    "tracker": "Bug",
                    "status": "Rejected"
                }
            ],
            "attachments": [
                {
                    "filename": "ckeditor-3-p-tags.png",
                    "contentType": "image/png",
                    "size": 15472,
                    "on": "2019-06-13T13:31:29Z",
                    "url": "https://forge.typo3.org/attachments/download/34363/ckeditor-3-p-tags.png"
                },
                {
                    "filename": "db_field_value.jpg",
                    "contentType": "image/jpeg",
                    "size": 17301,
                    "on": "2023-08-07T18:36:48Z",
                    "url": "https://forge.typo3.org/attachments/download/37897/db_field_value.jpg"
                },
                {
                    "filename": "rte_view.jpg",
                    "contentType": "image/jpeg",
                    "size": 11397,
                    "on": "2023-08-07T18:39:23Z",
                    "url": "https://forge.typo3.org/attachments/download/37898/rte_view.jpg"
                },
                {
                    "filename": "rte_view_sourcecode.jpg",
                    "contentType": "image/jpeg",
                    "size": 21339,
                    "on": "2023-08-07T18:40:49Z",
                    "url": "https://forge.typo3.org/attachments/download/37899/rte_view_sourcecode.jpg"
                },
                {
                    "filename": "fe_output.jpg",
                    "contentType": "image/jpeg",
                    "size": 17378,
                    "on": "2023-08-07T18:42:14Z",
                    "url": "https://forge.typo3.org/attachments/download/37900/fe_output.jpg"
                },
                {
                    "filename": "fe_output_sourcecode.jpg",
                    "contentType": "image/jpeg",
                    "size": 31557,
                    "on": "2023-08-07T18:46:05Z",
                    "url": "https://forge.typo3.org/attachments/download/37901/fe_output_sourcecode.jpg"
                },
                {
                    "filename": "db_field_value_wo_linebreak.jpg",
                    "contentType": "image/jpeg",
                    "size": 15438,
                    "on": "2023-08-07T18:50:25Z",
                    "url": "https://forge.typo3.org/attachments/download/37902/db_field_value_wo_linebreak.jpg"
                }
            ],
            "reviews": [
                {
                    "change": 95108,
                    "changeId": "",
                    "patchSet": 1,
                    "on": "2026-08-05T03:25:06Z",
                    "url": "https://review.typo3.org/c/95108"
                },
                {
                    "change": 95131,
                    "changeId": "",
                    "patchSet": 1,
                    "on": "2026-08-06T19:26:43Z",
                    "url": "https://review.typo3.org/c/95131"
                },
                {
                    "change": 95132,
                    "changeId": "",
                    "patchSet": 1,
                    "on": "2026-08-06T19:26:58Z",
                    "url": "https://review.typo3.org/c/95132"
                }
            ],
            "noteCount": 15,
            "botNoteCount": 3,
            "notes": [
                {
                    "author": "Benni Mack",
                    "on": "2019-06-13T13:37:33Z",
                    "note": "Are you using the RTE in a FlexForm?"
                },
                {
                    "author": "Alex Nostadt",
                    "on": "2019-06-13T13:40:26Z",
                    "note": "Benni Mack wrote:\r\n> Are you using the RTE in a FlexForm?\r\n\r\nNo, I use it in a TCA.\r\n\r\nWhen removing the single existing line break between \"World\" and closing p-tag no additional line breaks are created. I have switched temporary to T3's default preset but the behaviour was the same.\r\n\r\nThat's my TCA field config:\r\n<pre><code class=\"php\">\r\n<?php\r\n        'myField' => [\r\n            'exclude' => 1,\r\n            'label' => $ll . 'myField',\r\n            'config' => [\r\n                'type' => 'text',\r\n                'cols' => 40,\r\n                'rows' => 15,\r\n                'eval' => 'trim',\r\n                'enableRichtext' => true,\r\n            ],\r\n        ],\r\n</code></pre>"
                },
                {
                    "author": "Riccardo De Contardi",
                    "on": "2019-06-29T21:50:09Z",
                    "note": "Is this somehow related? #88655"
                },
                {
                    "author": "Alex Nostadt",
                    "on": "2019-07-02T12:07:18Z",
                    "note": "Riccardo De Contardi wrote:\r\n> Is this somehow related? #88655\r\n\r\nI will check within this week."
                },
                {
                    "author": "Alex Nostadt",
                    "on": "2019-07-08T18:57:16Z",
                    "note": "I don't think this is related as #88655 is about ignored RTE config. whereas this issue is rather related to DB=>CKEditor parse-process."
                },
                {
                    "author": "Benni Mack",
                    "on": "2019-08-16T12:46:29Z",
                    "note": "Alex Nostadt wrote:\r\n> I don't think this is related as #88655 is about ignored RTE config. whereas this issue is rather related to DB=>CKEditor parse-process.\r\n\r\nCan you share your CKEditor config? (if you have a manually configured RTE configuration) just to be sure."
                },
                {
                    "author": "Alex Nostadt",
                    "on": "2019-08-17T09:05:59Z",
                    "note": "Benni Mack wrote:\r\n> Alex Nostadt wrote:\r\n> > I don't think this is related as #88655 is about ignored RTE config. whereas this issue is rather related to DB=>CKEditor parse-process.\r\n> \r\n> Can you share your CKEditor config? (if you have a manually configured RTE configuration) just to be sure.\r\n\r\nI could reproduce it with the default T3 config as well but can provide it as well within the next days."
                },
                {
                    "author": "Alex Nostadt",
                    "on": "2019-08-26T15:38:15Z",
                    "note": "Alex Nostadt wrote:\r\n> Benni Mack wrote:\r\n> > Alex Nostadt wrote:\r\n> > > I don't think this is related as #88655 is about ignored RTE config. whereas this issue is rather related to DB=>CKEditor parse-process.\r\n> > \r\n> > Can you share your CKEditor config? (if you have a manually configured RTE configuration) just to be sure.\r\n> \r\n> I could reproduce it with the default T3 config as well but can provide it as well within the next days.\r\n\r\nSorry, forgot this ticket. Add this to my priority list now."
                },
                {
                    "author": "Alex Nostadt",
                    "on": "2019-08-30T08:42:51Z",
                    "note": "That's the RTE configuration:\r\n(I include Specific.yaml. We have multiple \"Specific.yaml\" files and our Default.yaml is our common denominator)\r\n*Default.yaml*\r\n<pre><code class=\"yaml\">\r\nimports:\r\n    - { resource: \"EXT:rte_ckeditor/Configuration/RTE/Processing.yaml\" }\r\n    - { resource: \"EXT:rte_ckeditor/Configuration/RTE/Editor/Base.yaml\" }\r\n    - { resource: \"EXT:rte_ckeditor/Configuration/RTE/Editor/Plugins.yaml\" }\r\n    - { resource: \"EXT:rte_ckeditor_image/Configuration/RTE/Plugin.yaml\" }\r\n\r\nprocessing:\r\n    ## allowed default attributes (added by us is..: \"style\")\r\n    allowAttributes: [class, id, title, dir, lang, xml:lang, itemscope, itemtype, itemprop, style]\r\n\r\neditor:\r\n    externalPlugins:\r\n        find:\r\n            resource: \"EXT:provider/Resources/Public/CKEditor/Plugins/find/\"\r\n\r\n    config:\r\n        # can be \"default\", but a custom stylesSet can be defined here, which fits TYPO3 best\r\n        stylesSet:\r\n            - { name: \"LLL:EXT:provider/Resources/Private/Language/rte.xlf:align-center\", element: \"p\", styles: { text-align: \"center\"} }\r\n            - { name: \"LLL:EXT:provider/Resources/Private/Language/rte.xlf:justify\", element: \"p\", styles: { text-align: \"justify\"} }\r\n            - { name: \"LLL:EXT:provider/Resources/Private/Language/rte.xlf:author\", element: \"p\", attributes: { class: 'author' } }\r\n            - { name: \"LLL:EXT:provider/Resources/Private/Language/rte.xlf:citation\", element: \"p\", attributes: { class: 'citation' } }\r\n            - { name: \"LLL:EXT:provider/Resources/Private/Language/rte.xlf:check\", element: [\"ul\"], attributes: { class: 'check' } }\r\n\r\n        toolbarGroups:\r\n            - { name: styles, groups: [ styles ] }\r\n            - \"/\"\r\n            - { name: editing, groups: [ find, selection, spellchecker, editing ] }\r\n            - { name: forms, groups: [ forms ] }\r\n            - { name: basicstyles, groups: [ basicstyles, cleanup ] }\r\n            - { name: paragraph, groups: [ list, indent, blocks, align, bidi, paragraph ] }\r\n            - { name: links, groups: [ links ] }\r\n            - { name: insert, groups: [ insert ] }\r\n            - { name: colors, groups: [ colors ] }\r\n            - { name: others, groups: [ others ] }\r\n            - \"/\"\r\n            - { name: clipboard, groups: [ clipboard, undo ] }\r\n            - { name: document, groups: [ mode, document, doctools ] }\r\n            - { name: tools, groups: [ tools ] }\r\n            - { name: about, groups: [ about ] }\r\n\r\n        format_tags: \"p;h1;h2;h3;h4\"\r\n\r\n        justifyClasses:\r\n            - text-left\r\n            - text-center\r\n            - text-right\r\n            - text-justify\r\n\r\n        buttons:\r\n            link:\r\n                url:\r\n                    properties:\r\n                        class:\r\n                            default: 'external-link'\r\n                properties:\r\n                    class:\r\n                        allowedClasses: 'external-link'\r\n\r\n        classes:\r\n            external-link:\r\n                name: 'External Link'\r\n\r\n        classesAnchor:\r\n            externalLink:\r\n                class: 'external-link'\r\n                type: 'url'\r\n                target: '_blank'\r\n\r\n        removeButtons:\r\n            - Save\r\n            - NewPage\r\n            - Preview\r\n            - Print\r\n            - Templates\r\n            - Cut\r\n            - Copy\r\n            - Paste\r\n            - PasteFromWord\r\n            - Form\r\n            - Checkbox\r\n            - Radio,\r\n            - TextField\r\n            - Textarea\r\n            - Select\r\n            - Button\r\n            - ImageButton\r\n            - HiddenField\r\n            - Outdent\r\n            - Indent\r\n            - Flash\r\n            - HorizontalRule\r\n            - Smiley\r\n            - PageBreak\r\n            - Iframe\r\n            - Font\r\n            - FontSize\r\n            - TextColor\r\n            - BGColor\r\n            - ShowBlocks\r\n            - Blockquote\r\n            - About\r\n</code></pre>\r\n\r\n*Specific.yaml*\r\n<pre><code class=\"yaml\">\r\nimports:\r\n    - { resource: \"EXT:provider/Configuration/RTE/Default.yaml\" }\r\n\r\neditor:\r\n    externalPlugins:\r\n        placeholder_select:\r\n            resource: \"EXT:provider/Resources/Public/CKEditor/Plugins/placeholder_select/\"\r\n\r\n    config:\r\n        contentsCss: [\"EXT:rte_ckeditor/Resources/Public/Css/contents.css\", \"EXT:provider/Resources/Public/Css/editor.css\"]\r\n        # can be \"default\", but a custom stylesSet can be defined here, which fits TYPO3 best\r\n        stylesSet:\r\n            - { name: \"LLL:EXT:provider/Resources/Private/Language/rte.xlf:large\", element:  [\"p\", \"ul\", \"ol\", \"h1\", \"h2\", \"h3\"], attributes: { class: 'large' } }\r\n            - { name: \"LLL:EXT:provider/Resources/Private/Language/rte.xlf:teaser-text\", element: \"p\", attributes: { class: 'teaser-text' } }\r\n            - { name: \"LLL:EXT:provider/Resources/Private/Language/rte.xlf:light-gray\", element: [\"p\", \"ul\", \"ol\"], attributes: { class: 'light-gray' } }\r\n            - { name: \"LLL:EXT:provider/Resources/Private/Language/rte.xlf:all-borders\", element: \"table\", attributes: { class: 'all-borders' } }\r\n            - { name: \"LLL:EXT:provider/Resources/Private/Language/rte.xlf:horizontal-borders\", element: \"table\", attributes: { class: 'horizontal-borders' } }\r\n            - { name: \"LLL:EXT:provider/Resources/Private/Language/rte.xlf:vertical-borders\", element: \"table\", attributes: { class: 'vertical-borders' } }\r\n\r\n</code></pre>"
                },
                {
                    "author": "David Menzel",
                    "on": "2023-08-07T18:58:28Z",
                    "note": "We have the same problem since (at least) TYPO3 10. Now using TYPO3 11.\r\n\r\nA bodytext database field has the following text:\r\n\r\n!db_field_value.jpg!\r\n\r\nThe RTE ckeditor looks like this:\r\n\r\n!rte_view.jpg!\r\n\r\nRTE ckeditor Source code view:\r\n\r\n!rte_view_sourcecode.jpg!\r\n\r\nOutput in FE:\r\n\r\n!fe_output.jpg!\r\n\r\nFE source code:\r\n\r\n!fe_output_sourcecode.jpg!\r\n\r\nYou notice the additional empty p-Tags before/after the pre-tag?\r\n\r\nHowever, when I remove the linebreaks in the database field:\r\n!db_field_value_wo_linebreak.jpg!\r\n\r\nthe FE works and there are no additional empty p-tags.\r\nWhen I save the RTE field again, the empty p-tags are back again because the linebreaks are back in the db field.\r\n\r\nNot sure why it only happens before/after the pre-tag.\r\n\r\nTYPO3 11.5.30 and PHP 7.4"
                },
                {
                    "author": "David Menzel",
                    "on": "2025-03-06T05:47:19Z",
                    "note": "Problem still exists in TYPO3 12.4.27 and CKEditor 5.\r\n\r\nThere's an additional empty <p>-Tag before and after the codeblock."
                },
                {
                    "author": "Gerrit Code Review",
                    "on": "2026-08-05T03:25:06Z",
                    "note": "Patch set 1 for branch *main* of project *Packages/TYPO3.CMS* has been pushed to the review server.\nIt is available at https://review.typo3.org/c/Packages/TYPO3.CMS/+/95108"
                },
                {
                    "author": "Gerrit Code Review",
                    "on": "2026-08-06T19:26:43Z",
                    "note": "Patch set 1 for branch *14.3* of project *Packages/TYPO3.CMS* has been pushed to the review server.\nIt is available at https://review.typo3.org/c/Packages/TYPO3.CMS/+/95131"
                },
                {
                    "author": "Gerrit Code Review",
                    "on": "2026-08-06T19:26:58Z",
                    "note": "Patch set 1 for branch *13.4* of project *Packages/TYPO3.CMS* has been pushed to the review server.\nIt is available at https://review.typo3.org/c/Packages/TYPO3.CMS/+/95132"
                },
                {
                    "author": "Benjamin Kott",
                    "on": "2026-08-06T20:15:08Z",
                    "note": "Applied in changeset commit:b406a9416431d1945756ce418d9c3726844f5325."
                }
            ]
        },
        "results": [],
        "unavailable": null
    }


forge: an issue without the patch-set pings
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Called with:

.. code-block:: json

    {
        "issue": "14858",
        "notes": "people"
    }


Text:

.. code-block:: text

    #14858 extended clipboard: setCopyMode can`t be set to copy by default
    Bug · New · priority Should have · https://forge.typo3.org/issues/14858
    Assigned to nobody.
    Target version: Candidate for patchlevel
    Reported against TYPO3 8, PHP 7.2 — which is what the reporter had, not what it still reproduces on.
    Relation: relates #90676 — Epic · Accepted · Clipboard related bugs and features
    Relation: duplicates #70759 — Feature · Closed · Changing the default clipboard option from  "move elements"  to "copy elements"

    ## Reported
    Hi,
    
    I couldn`t find any TCAdefaults or other TSconfig option to switch the copy mode of the extended clipboard to copy by default.
    
    At the moment the default is "move" which can be very annoying.
    
    Please add the possibility to choose the default behaviour of the "setCopyMode" button.
    
    Thanks,
    Sacha
    
    
    
    
    (issue imported from #M1277)

    ## Changes on review.typo3.org (2)
    Named in the comments below and lifted out of them. What state one is in now is a typo3_gerrit_lookup call — pass the number as change, or the Change-Id, which is what survives a rebase onto another branch. A comment says what was true the day it was written.
    - change 38419 · patch set 3 · named 2015-04-01 · https://review.typo3.org/c/38419
    - change 70962 · patch set 5 · named 2023-01-14 · https://review.typo3.org/c/70962

    ## Comments (8 of 16, oldest first)
    What was decided is here rather than above.
    8 of them a review bot wrote and they were dropped. The changes they named are above. Ask for notes "all" to read them; a count of 0 on an issue with patch-set pings means this filter does not know the bot that wrote them.

    **Sebastian Kurfuerst**, 2005-10-22T19:07:47Z
    This issue is more complicated than it seems, because there setting for "move" is empty - and there is no easy condition to find out whether the default should apply or not. I will have a deeper look into it.
    Greets, Sebastian

    **Sebastian Kurfuerst**, 2005-10-23T20:16:15Z
    this issue is not so easy to fix and I don't see a nice solution at the moment. A patch is welcome, but currently I cannot have a deeper look into it.
    Greets, Sebastian

    **Oliver Hader**, 2011-09-19T12:55:23Z
    Should be a UserTS configuration and maybe an additional setting in the user preferences.

    **Gabriel Kaufmann TYPOworx GmbH | NewMedia**, 2013-04-11T09:11:03Z
    Is there anything new for the TYPO3 4.6 oder 4.7 Tree?

    **Tilo Baller**, 2015-10-21T15:28:17Z
    What was the reason for abandoning the patch in review and what is the current progress of this feature?
    
    I got several requests from customers for adjusting the default behaviour to 'copy' instead of 'move'.

    **Daxboeck no-lastname-given**, 2018-06-19T14:18:23Z
    I did now ask my developers to create a patch as the default of "move" when someone selects the 2nd or 3rd clipboard is very annoying.
    It is against a thought of safety, it is against common sense.
    I just had too many cases where by accident stuff has been moved instead of copied.
    "copy" must be the default, there should be no doubt about it.

    **Sybille Peters**, 2023-01-14T19:27:42Z
    Patch https://review.typo3.org/c/Packages/TYPO3.CMS/+/70962 was abandoned.

    **Benni Mack**, 2026-01-23T08:30:36Z
    Summarizing the current state:
    
    - Currently, the option is set to "move" hardcoded without any possibility to change this.
    - Right now, this is debateble is the setCopyMode should be "copy" or "move"
    - If this should be configurable, then we need a new option, making this not a bug, but actually a feature


Data:

.. code-block:: json

    {
        "status": "answered",
        "source": "https://forge.typo3.org",
        "url": "https://forge.typo3.org/issues/14858.json?include=journals,relations,attachments",
        "query": "",
        "total": 0,
        "categories": [],
        "categoriesUsed": [],
        "issue": {
            "id": 14858,
            "subject": "extended clipboard: setCopyMode can`t be set to copy by default",
            "status": "New",
            "tracker": "Bug",
            "priority": "Should have",
            "assignedTo": "",
            "targetVersion": "Candidate for patchlevel",
            "typo3Version": "8",
            "phpVersion": "7.2",
            "createdOn": "2005-07-11T23:31:03Z",
            "updatedOn": "2026-01-23T08:30:36Z",
            "url": "https://forge.typo3.org/issues/14858",
            "description": "Hi,\r\n\r\nI couldn`t find any TCAdefaults or other TSconfig option to switch the copy mode of the extended clipboard to copy by default.\r\n\r\nAt the moment the default is \"move\" which can be very annoying.\r\n\r\nPlease add the possibility to choose the default behaviour of the \"setCopyMode\" button.\r\n\r\nThanks,\r\nSacha\r\n\r\n\r\n\r\n\r\n(issue imported from #M1277)",
            "relations": [
                {
                    "issue": 90676,
                    "relation": "relates",
                    "url": "https://forge.typo3.org/issues/90676",
                    "subject": "Clipboard related bugs and features",
                    "tracker": "Epic",
                    "status": "Accepted"
                },
                {
                    "issue": 70759,
                    "relation": "duplicates",
                    "url": "https://forge.typo3.org/issues/70759",
                    "subject": "Changing the default clipboard option from  \"move elements\"  to \"copy elements\"",
                    "tracker": "Feature",
                    "status": "Closed"
                }
            ],
            "attachments": [],
            "reviews": [
                {
                    "change": 38419,
                    "changeId": "",
                    "patchSet": 3,
                    "on": "2015-04-01T20:54:18Z",
                    "url": "https://review.typo3.org/c/38419"
                },
                {
                    "change": 70962,
                    "changeId": "",
                    "patchSet": 5,
                    "on": "2023-01-14T19:27:42Z",
                    "url": "https://review.typo3.org/c/70962"
                }
            ],
            "noteCount": 16,
            "botNoteCount": 8,
            "notes": [
                {
                    "author": "Sebastian Kurfuerst",
                    "on": "2005-10-22T19:07:47Z",
                    "note": "This issue is more complicated than it seems, because there setting for \"move\" is empty - and there is no easy condition to find out whether the default should apply or not. I will have a deeper look into it.\r\nGreets, Sebastian"
                },
                {
                    "author": "Sebastian Kurfuerst",
                    "on": "2005-10-23T20:16:15Z",
                    "note": "this issue is not so easy to fix and I don't see a nice solution at the moment. A patch is welcome, but currently I cannot have a deeper look into it.\r\nGreets, Sebastian"
                },
                {
                    "author": "Oliver Hader",
                    "on": "2011-09-19T12:55:23Z",
                    "note": "Should be a UserTS configuration and maybe an additional setting in the user preferences."
                },
                {
                    "author": "Gabriel Kaufmann TYPOworx GmbH | NewMedia",
                    "on": "2013-04-11T09:11:03Z",
                    "note": "Is there anything new for the TYPO3 4.6 oder 4.7 Tree?"
                },
                {
                    "author": "Tilo Baller",
                    "on": "2015-10-21T15:28:17Z",
                    "note": "What was the reason for abandoning the patch in review and what is the current progress of this feature?\r\n\r\nI got several requests from customers for adjusting the default behaviour to 'copy' instead of 'move'."
                },
                {
                    "author": "Daxboeck no-lastname-given",
                    "on": "2018-06-19T14:18:23Z",
                    "note": "I did now ask my developers to create a patch as the default of \"move\" when someone selects the 2nd or 3rd clipboard is very annoying.\r\nIt is against a thought of safety, it is against common sense.\r\nI just had too many cases where by accident stuff has been moved instead of copied.\r\n\"copy\" must be the default, there should be no doubt about it."
                },
                {
                    "author": "Sybille Peters",
                    "on": "2023-01-14T19:27:42Z",
                    "note": "Patch https://review.typo3.org/c/Packages/TYPO3.CMS/+/70962 was abandoned."
                },
                {
                    "author": "Benni Mack",
                    "on": "2026-01-23T08:30:36Z",
                    "note": "Summarizing the current state:\r\n\r\n- Currently, the option is set to \"move\" hardcoded without any possibility to change this.\r\n- Right now, this is debateble is the setCopyMode should be \"copy\" or \"move\"\r\n- If this should be configurable, then we need a new option, making this not a bug, but actually a feature"
                }
            ]
        },
        "results": [],
        "unavailable": null
    }


forge: no such issue
~~~~~~~~~~~~~~~~~~~~

Called with:

.. code-block:: json

    {
        "issue": "99999999"
    }


Text:

.. code-block:: text

    TYPO3 issue tracker: no issue 99999999 at https://forge.typo3.org.


Data:

.. code-block:: json

    {
        "status": "empty",
        "source": "https://forge.typo3.org",
        "url": "https://forge.typo3.org/issues/99999999.json?include=journals,relations,attachments",
        "query": "",
        "total": 0,
        "categories": [],
        "categoriesUsed": [],
        "issue": null,
        "results": [],
        "unavailable": null
    }


forge: which other issues describe this
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Called with:

.. code-block:: json

    {
        "query": "cache busting",
        "limit": 3
    }


Text:

.. code-block:: text

    TYPO3 issue tracker: 3 issues match "cache busting"
    These words, in the tracker's own order and unranked. Another wording finds another set, so this is which issues mention it rather than which one it duplicates. Read one whole by passing its number as issue.

    ## #107904 Cache-busting applied to folder paths
    Bug · Closed · Frontend · filed 2025-10-29 · last touched 2025-12-02 · https://forge.typo3.org/issues/107904

    ## #107869 Add option to not add cache busting to generated URIs
    Bug · Closed · filed 2025-10-27 · last touched 2025-12-02 · https://forge.typo3.org/issues/107869

    ## #105953 f:uri.resource cache busting not working and in addition causing PHP warninigs when open_basedir is enabled
    Bug · Closed · Fluid · filed 2025-01-16 · last touched 2025-08-12 · https://forge.typo3.org/issues/105953


Data:

.. code-block:: json

    {
        "status": "answered",
        "source": "https://forge.typo3.org",
        "url": "https://forge.typo3.org/search.json?q=cache%20busting&issues=1&limit=3",
        "query": "cache busting",
        "total": 15,
        "categories": [],
        "categoriesUsed": [],
        "issue": null,
        "results": [
            {
                "issue": 107904,
                "subject": "Cache-busting applied to folder paths",
                "tracker": "Bug",
                "status": "Closed",
                "category": "Frontend",
                "assignedTo": "",
                "createdOn": "2025-10-29T11:00:18Z",
                "updatedOn": "2025-12-02T12:04:43Z",
                "url": "https://forge.typo3.org/issues/107904",
                "relations": [],
                "attachments": [],
                "reviews": []
            },
            {
                "issue": 107869,
                "subject": "Add option to not add cache busting to generated URIs",
                "tracker": "Bug",
                "status": "Closed",
                "category": "",
                "assignedTo": "",
                "createdOn": "2025-10-27T19:48:27Z",
                "updatedOn": "2025-12-02T12:04:41Z",
                "url": "https://forge.typo3.org/issues/107869",
                "relations": [],
                "attachments": [],
                "reviews": []
            },
            {
                "issue": 105953,
                "subject": "f:uri.resource cache busting not working and in addition causing PHP warninigs when open_basedir is enabled",
                "tracker": "Bug",
                "status": "Closed",
                "category": "Fluid",
                "assignedTo": "",
                "createdOn": "2025-01-16T20:23:02Z",
                "updatedOn": "2025-08-12T14:36:32Z",
                "url": "https://forge.typo3.org/issues/105953",
                "relations": [],
                "attachments": [],
                "reviews": []
            }
        ],
        "unavailable": null
    }


forge: nothing matches these words
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Called with:

.. code-block:: json

    {
        "query": "quantumflux transponder"
    }


Text:

.. code-block:: text

    TYPO3 issue tracker: no issue matches "quantumflux transponder" at https://forge.typo3.org.
    These words matched nothing, which is not that nobody reported it: an issue worded differently is invisible to a word search. Ask again in the words a reporter would have used.


Data:

.. code-block:: json

    {
        "status": "empty",
        "source": "https://forge.typo3.org",
        "url": "https://forge.typo3.org/search.json?q=quantumflux%20transponder&issues=1&limit=15",
        "query": "quantumflux transponder",
        "total": 0,
        "categories": [],
        "categoriesUsed": [],
        "issue": null,
        "results": [],
        "unavailable": null
    }


forge: the oldest issues nobody has resolved
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Called with:

.. code-block:: json

    {
        "open": "oldest",
        "limit": 3
    }


Text:

.. code-block:: text

    TYPO3 issue tracker: 3 of 2474 open issues of the TYPO3 Core project, oldest filed first
    This is a page and not the set. What comes after it is reached by a narrower filter — an earlier date, one tracker — rather than by a larger limit, because the order is the tracker's own and more of it is more of the same end.
    Age is a candidate and never a finding: read one whole by passing its number as issue, and what it still claims is established in the checkout rather than off this list.
    A row carries what the page came back with: the issues it is filed against, the files hanging off it, and the changes on review.typo3.org whose commit message names it. A change named here is a handle for typo3_gerrit_lookup and not a statement about its state, and a row with no such line is one nothing there names — or one the review server did not answer for, which this list does not separate.

    ## #14277 Start/Stop time for pages is ignored in standard menu objects
    Feature · Accepted · Frontend · unassigned · filed 2004-08-20 · last touched 2025-04-04 · https://forge.typo3.org/issues/14277
    Relation: relates #16815 — Bug · Closed · Sitemap ignoring "Start" and "End" flags
    Relation: relates #98964 — Bug · Closed · Menu object caching creates too many records resulting in huge cache_hash table
    Review: change 61395 · https://review.typo3.org/c/Packages/TYPO3.CMS/+/61395

    ## #14858 extended clipboard: setCopyMode can`t be set to copy by default
    Bug · New · Backend User Interface · unassigned · filed 2005-07-11 · last touched 2026-01-23 · https://forge.typo3.org/issues/14858
    Relation: relates #90676 — Epic · Accepted · Clipboard related bugs and features
    Relation: duplicates #70759 — Feature · Closed · Changing the default clipboard option from  "move elements"  to "copy elements"
    Review: change 70962 · https://review.typo3.org/c/Packages/TYPO3.CMS/+/70962
    Review: change 38419 · https://review.typo3.org/c/Packages/TYPO3.CMS/+/38419

    ## #15984 menu.showAccessRestrictedPages doesn't replace link for  "include subpages"
    Bug · Accepted · Frontend · unassigned · filed 2006-04-05 · last touched 2026-04-15 · https://forge.typo3.org/issues/15984
    Relation: relates #22860 — Bug · Closed · typolinkLinkAccessRestrictedPages_addParams doesn't work on restricted subpages
    Relation: relates #26484 — Bug · Closed · extend to subpages in page properties in access tab does not work correctly
    Relation: relates #78825 — Bug · Closed · Wrong pid determination when opening a nested access restriced page
    Relation: precedes #32756 — Bug · Closed · Massive Memory Leak in 4.5.8+ / 4.6
    Files (1): 3129.diff
    Review: change 2545 · https://review.typo3.org/c/Packages/TYPO3.CMS/+/2545
    Review: change 2544 · https://review.typo3.org/c/Packages/TYPO3.CMS/+/2544
    Review: change 1186 · https://review.typo3.org/c/Packages/TYPO3.CMS/+/1186


Data:

.. code-block:: json

    {
        "status": "answered",
        "source": "https://forge.typo3.org",
        "url": "https://forge.typo3.org/projects/typo3cms-core/issues.json?status_id=open&sort=created_on%3Aasc&limit=3&include=relations%2Cattachments",
        "query": "",
        "total": 2474,
        "categories": [
            "AdminPanel",
            "Authentication",
            "Backend API",
            "Backend JavaScript",
            "Backend User Interface",
            "Caching",
            "Categorization API",
            "CLI",
            "Code Cleanup",
            "composer",
            "Content Rendering",
            "Content Security Policy",
            "Dashboard",
            "Database API (Doctrine DBAL)",
            "DataHandler aka TCEmain",
            "Documentation",
            "Extbase",
            "Extbase + l10n",
            "Extension Manager",
            "felogin",
            "File Abstraction Layer (FAL)",
            "Fluid",
            "Fluid Styled Content",
            "Form Framework",
            "FormEngine aka TCEforms",
            "Frontend",
            "Image Cropping",
            "Image Generation / GIFBUILDER",
            "Import/Export (T3D)",
            "Indexed Search",
            "Install Tool",
            "Language Manager (backend)",
            "Link Handling & Redirect Handling",
            "Linkvalidator",
            "Localization",
            "Locking / Session Handling",
            "Logging",
            "Mailer API",
            "Miscellaneous",
            "Pagetree",
            "Performance",
            "Recycler",
            "Reports",
            "RTE (rtehtmlarea + ckeditor)",
            "scheduler",
            "Security",
            "SEO",
            "Site Handling, Site Sets & Routing",
            "System/Bootstrap/Configuration",
            "t3editor",
            "Tests",
            "Themes",
            "TypoScript",
            "WebHooks - Incoming = Reactions + Outgoing",
            "Workspaces"
        ],
        "categoriesUsed": [],
        "issue": null,
        "results": [
            {
                "issue": 14277,
                "subject": "Start/Stop time for pages is ignored in standard menu objects",
                "tracker": "Feature",
                "status": "Accepted",
                "category": "Frontend",
                "assignedTo": "",
                "createdOn": "2004-08-20T08:45:13Z",
                "updatedOn": "2025-04-04T06:59:33Z",
                "url": "https://forge.typo3.org/issues/14277",
                "relations": [
                    {
                        "issue": 16815,
                        "relation": "relates",
                        "url": "https://forge.typo3.org/issues/16815",
                        "subject": "Sitemap ignoring \"Start\" and \"End\" flags",
                        "tracker": "Bug",
                        "status": "Closed"
                    },
                    {
                        "issue": 98964,
                        "relation": "relates",
                        "url": "https://forge.typo3.org/issues/98964",
                        "subject": "Menu object caching creates too many records resulting in huge cache_hash table",
                        "tracker": "Bug",
                        "status": "Closed"
                    }
                ],
                "attachments": [],
                "reviews": [
                    {
                        "change": 61395,
                        "url": "https://review.typo3.org/c/Packages/TYPO3.CMS/+/61395"
                    }
                ]
            },
            {
                "issue": 14858,
                "subject": "extended clipboard: setCopyMode can`t be set to copy by default",
                "tracker": "Bug",
                "status": "New",
                "category": "Backend User Interface",
                "assignedTo": "",
                "createdOn": "2005-07-11T23:31:03Z",
                "updatedOn": "2026-01-23T08:30:36Z",
                "url": "https://forge.typo3.org/issues/14858",
                "relations": [
                    {
                        "issue": 90676,
                        "relation": "relates",
                        "url": "https://forge.typo3.org/issues/90676",
                        "subject": "Clipboard related bugs and features",
                        "tracker": "Epic",
                        "status": "Accepted"
                    },
                    {
                        "issue": 70759,
                        "relation": "duplicates",
                        "url": "https://forge.typo3.org/issues/70759",
                        "subject": "Changing the default clipboard option from  \"move elements\"  to \"copy elements\"",
                        "tracker": "Feature",
                        "status": "Closed"
                    }
                ],
                "attachments": [],
                "reviews": [
                    {
                        "change": 70962,
                        "url": "https://review.typo3.org/c/Packages/TYPO3.CMS/+/70962"
                    },
                    {
                        "change": 38419,
                        "url": "https://review.typo3.org/c/Packages/TYPO3.CMS/+/38419"
                    }
                ]
            },
            {
                "issue": 15984,
                "subject": "menu.showAccessRestrictedPages doesn't replace link for  \"include subpages\"",
                "tracker": "Bug",
                "status": "Accepted",
                "category": "Frontend",
                "assignedTo": "",
                "createdOn": "2006-04-05T03:07:50Z",
                "updatedOn": "2026-04-15T09:44:14Z",
                "url": "https://forge.typo3.org/issues/15984",
                "relations": [
                    {
                        "issue": 22860,
                        "relation": "relates",
                        "url": "https://forge.typo3.org/issues/22860",
                        "subject": "typolinkLinkAccessRestrictedPages_addParams doesn't work on restricted subpages",
                        "tracker": "Bug",
                        "status": "Closed"
                    },
                    {
                        "issue": 26484,
                        "relation": "relates",
                        "url": "https://forge.typo3.org/issues/26484",
                        "subject": "extend to subpages in page properties in access tab does not work correctly",
                        "tracker": "Bug",
                        "status": "Closed"
                    },
                    {
                        "issue": 78825,
                        "relation": "relates",
                        "url": "https://forge.typo3.org/issues/78825",
                        "subject": "Wrong pid determination when opening a nested access restriced page",
                        "tracker": "Bug",
                        "status": "Closed"
                    },
                    {
                        "issue": 32756,
                        "relation": "precedes",
                        "url": "https://forge.typo3.org/issues/32756",
                        "subject": "Massive Memory Leak in 4.5.8+ / 4.6",
                        "tracker": "Bug",
                        "status": "Closed"
                    }
                ],
                "attachments": [
                    {
                        "filename": "3129.diff",
                        "contentType": "application/octet-stream",
                        "size": 905,
                        "on": "2010-06-10T22:46:58Z",
                        "url": "https://forge.typo3.org/attachments/download/6964/3129.diff"
                    }
                ],
                "reviews": [
                    {
                        "change": 2545,
                        "url": "https://review.typo3.org/c/Packages/TYPO3.CMS/+/2545"
                    },
                    {
                        "change": 2544,
                        "url": "https://review.typo3.org/c/Packages/TYPO3.CMS/+/2544"
                    },
                    {
                        "change": 1186,
                        "url": "https://review.typo3.org/c/Packages/TYPO3.CMS/+/1186"
                    }
                ]
            }
        ],
        "unavailable": null
    }


forge: what is known about one area
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Called with:

.. code-block:: json

    {
        "open": "stale",
        "category": "rte",
        "tracker": "Bug",
        "limit": 3
    }


Text:

.. code-block:: text

    TYPO3 issue tracker: 3 of 22 open issues of the TYPO3 Core project, tracker Bug, in RTE (rtehtmlarea + ckeditor), longest untouched first
    This is a page and not the set. What comes after it is reached by a narrower filter — an earlier date, one tracker — rather than by a larger limit, because the order is the tracker's own and more of it is more of the same end.
    Age is a candidate and never a finding: read one whole by passing its number as issue, and what it still claims is established in the checkout rather than off this list.
    A row carries what the page came back with: the issues it is filed against, the files hanging off it, and the changes on review.typo3.org whose commit message names it. A change named here is a handle for typo3_gerrit_lookup and not a statement about its state, and a row with no such line is one nothing there names — or one the review server did not answer for, which this list does not separate.
    An area is where an issue was filed and not everything it is about. A report about this one regularly sits under another area, so what came back is a floor rather than the set — query the words as well where the question is about a subject.

    ## #87400 CKEditor: assign correct CSS class to tags with entryHTMLparser_db
    Bug · New · RTE (rtehtmlarea + ckeditor) · unassigned · filed 2019-01-11 · last touched 2019-01-11 · https://forge.typo3.org/issues/87400
    Relation: relates #87314 — Feature · New · allowedAttribs / allowAttributes usage in config
    Relation: relates #92943 — Bug · Closed · RTE ckeditor does not respect YAML configuration
    Files (1): RTE Bug.mov

    ## #97817 RTE removes line with empty, allowed tags when saving
    Bug · New · RTE (rtehtmlarea + ckeditor) · unassigned · filed 2022-06-28 · last touched 2022-06-28 · https://forge.typo3.org/issues/97817

    ## #88690 Translated content elements are not available in linkbrowser of the ckeditor in free mode
    Bug · New · RTE (rtehtmlarea + ckeditor) · unassigned · filed 2019-07-05 · last touched 2023-03-05 · https://forge.typo3.org/issues/88690
    Relation: relates #89701 — Bug · Closed · Link wizard lists only content elements of the default language
    Relation: relates #90138 — Feature · Closed · Language and mode (free or connected) should be handled in the links module when creating an anchor to content
    Relation: relates #91160 — Bug · Closed · Links to content element (anchor) in link wizard not possible when not in default language
    Relation: relates #88382 — Bug · Closed · Link wizard lists all content elements of a page regardless of source language
    Relation: relates #92809 — Bug · Accepted · Anchor Links in Link Wizard not translated correctly


Data:

.. code-block:: json

    {
        "status": "answered",
        "source": "https://forge.typo3.org",
        "url": "https://forge.typo3.org/projects/typo3cms-core/issues.json?status_id=open&sort=updated_on%3Aasc&limit=3&include=relations%2Cattachments&tracker_id=1&category_id=1001",
        "query": "",
        "total": 22,
        "categories": [
            "AdminPanel",
            "Authentication",
            "Backend API",
            "Backend JavaScript",
            "Backend User Interface",
            "Caching",
            "Categorization API",
            "CLI",
            "Code Cleanup",
            "composer",
            "Content Rendering",
            "Content Security Policy",
            "Dashboard",
            "Database API (Doctrine DBAL)",
            "DataHandler aka TCEmain",
            "Documentation",
            "Extbase",
            "Extbase + l10n",
            "Extension Manager",
            "felogin",
            "File Abstraction Layer (FAL)",
            "Fluid",
            "Fluid Styled Content",
            "Form Framework",
            "FormEngine aka TCEforms",
            "Frontend",
            "Image Cropping",
            "Image Generation / GIFBUILDER",
            "Import/Export (T3D)",
            "Indexed Search",
            "Install Tool",
            "Language Manager (backend)",
            "Link Handling & Redirect Handling",
            "Linkvalidator",
            "Localization",
            "Locking / Session Handling",
            "Logging",
            "Mailer API",
            "Miscellaneous",
            "Pagetree",
            "Performance",
            "Recycler",
            "Reports",
            "RTE (rtehtmlarea + ckeditor)",
            "scheduler",
            "Security",
            "SEO",
            "Site Handling, Site Sets & Routing",
            "System/Bootstrap/Configuration",
            "t3editor",
            "Tests",
            "Themes",
            "TypoScript",
            "WebHooks - Incoming = Reactions + Outgoing",
            "Workspaces"
        ],
        "categoriesUsed": [
            "RTE (rtehtmlarea + ckeditor)"
        ],
        "issue": null,
        "results": [
            {
                "issue": 87400,
                "subject": "CKEditor: assign correct CSS class to tags with entryHTMLparser_db",
                "tracker": "Bug",
                "status": "New",
                "category": "RTE (rtehtmlarea + ckeditor)",
                "assignedTo": "",
                "createdOn": "2019-01-11T11:07:13Z",
                "updatedOn": "2019-01-11T11:07:13Z",
                "url": "https://forge.typo3.org/issues/87400",
                "relations": [
                    {
                        "issue": 87314,
                        "relation": "relates",
                        "url": "https://forge.typo3.org/issues/87314",
                        "subject": "allowedAttribs / allowAttributes usage in config",
                        "tracker": "Feature",
                        "status": "New"
                    },
                    {
                        "issue": 92943,
                        "relation": "relates",
                        "url": "https://forge.typo3.org/issues/92943",
                        "subject": "RTE ckeditor does not respect YAML configuration",
                        "tracker": "Bug",
                        "status": "Closed"
                    }
                ],
                "attachments": [
                    {
                        "filename": "RTE Bug.mov",
                        "contentType": "video/quicktime",
                        "size": 3779702,
                        "on": "2019-01-11T11:01:30Z",
                        "url": "https://forge.typo3.org/attachments/download/34053/RTE%20Bug.mov"
                    }
                ],
                "reviews": []
            },
            {
                "issue": 97817,
                "subject": "RTE removes line with empty, allowed tags when saving",
                "tracker": "Bug",
                "status": "New",
                "category": "RTE (rtehtmlarea + ckeditor)",
                "assignedTo": "",
                "createdOn": "2022-06-28T07:46:04Z",
                "updatedOn": "2022-06-28T07:46:04Z",
                "url": "https://forge.typo3.org/issues/97817",
                "relations": [],
                "attachments": [],
                "reviews": []
            },
            {
                "issue": 88690,
                "subject": "Translated content elements are not available in linkbrowser of the ckeditor in free mode",
                "tracker": "Bug",
                "status": "New",
                "category": "RTE (rtehtmlarea + ckeditor)",
                "assignedTo": "",
                "createdOn": "2019-07-05T11:01:00Z",
                "updatedOn": "2023-03-05T17:47:02Z",
                "url": "https://forge.typo3.org/issues/88690",
                "relations": [
                    {
                        "issue": 89701,
                        "relation": "relates",
                        "url": "https://forge.typo3.org/issues/89701",
                        "subject": "Link wizard lists only content elements of the default language",
                        "tracker": "Bug",
                        "status": "Closed"
                    },
                    {
                        "issue": 90138,
                        "relation": "relates",
                        "url": "https://forge.typo3.org/issues/90138",
                        "subject": "Language and mode (free or connected) should be handled in the links module when creating an anchor to content",
                        "tracker": "Feature",
                        "status": "Closed"
                    },
                    {
                        "issue": 91160,
                        "relation": "relates",
                        "url": "https://forge.typo3.org/issues/91160",
                        "subject": "Links to content element (anchor) in link wizard not possible when not in default language",
                        "tracker": "Bug",
                        "status": "Closed"
                    },
                    {
                        "issue": 88382,
                        "relation": "relates",
                        "url": "https://forge.typo3.org/issues/88382",
                        "subject": "Link wizard lists all content elements of a page regardless of source language",
                        "tracker": "Bug",
                        "status": "Closed"
                    },
                    {
                        "issue": 92809,
                        "relation": "relates",
                        "url": "https://forge.typo3.org/issues/92809",
                        "subject": "Anchor Links in Link Wizard not translated correctly",
                        "tracker": "Bug",
                        "status": "Accepted"
                    }
                ],
                "attachments": [],
                "reviews": []
            }
        ],
        "unavailable": null
    }


forge: a word that names no area
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Called with:

.. code-block:: json

    {
        "open": "oldest",
        "category": "quantumflux"
    }


Text:

.. code-block:: text

    TYPO3 issue tracker: "quantumflux" names no area the core files issues under, so nothing was read. That is about the word and not about the backlog.
    The areas are: AdminPanel, Authentication, Backend API, Backend JavaScript, Backend User Interface, Caching, Categorization API, CLI, Code Cleanup, composer, Content Rendering, Content Security Policy, Dashboard, Database API (Doctrine DBAL), DataHandler aka TCEmain, Documentation, Extbase, Extbase + l10n, Extension Manager, felogin, File Abstraction Layer (FAL), Fluid, Fluid Styled Content, Form Framework, FormEngine aka TCEforms, Frontend, Image Cropping, Image Generation / GIFBUILDER, Import/Export (T3D), Indexed Search, Install Tool, Language Manager (backend), Link Handling & Redirect Handling, Linkvalidator, Localization, Locking / Session Handling, Logging, Mailer API, Miscellaneous, Pagetree, Performance, Recycler, Reports, RTE (rtehtmlarea + ckeditor), scheduler, Security, SEO, Site Handling, Site Sets & Routing, System/Bootstrap/Configuration, t3editor, Tests, Themes, TypoScript, WebHooks - Incoming = Reactions + Outgoing, Workspaces


Data:

.. code-block:: json

    {
        "status": "empty",
        "source": "https://forge.typo3.org",
        "url": "https://forge.typo3.org/projects/typo3cms-core/issues.json?status_id=open&sort=created_on%3Aasc&limit=15&include=relations%2Cattachments",
        "query": "",
        "total": 0,
        "categories": [
            "AdminPanel",
            "Authentication",
            "Backend API",
            "Backend JavaScript",
            "Backend User Interface",
            "Caching",
            "Categorization API",
            "CLI",
            "Code Cleanup",
            "composer",
            "Content Rendering",
            "Content Security Policy",
            "Dashboard",
            "Database API (Doctrine DBAL)",
            "DataHandler aka TCEmain",
            "Documentation",
            "Extbase",
            "Extbase + l10n",
            "Extension Manager",
            "felogin",
            "File Abstraction Layer (FAL)",
            "Fluid",
            "Fluid Styled Content",
            "Form Framework",
            "FormEngine aka TCEforms",
            "Frontend",
            "Image Cropping",
            "Image Generation / GIFBUILDER",
            "Import/Export (T3D)",
            "Indexed Search",
            "Install Tool",
            "Language Manager (backend)",
            "Link Handling & Redirect Handling",
            "Linkvalidator",
            "Localization",
            "Locking / Session Handling",
            "Logging",
            "Mailer API",
            "Miscellaneous",
            "Pagetree",
            "Performance",
            "Recycler",
            "Reports",
            "RTE (rtehtmlarea + ckeditor)",
            "scheduler",
            "Security",
            "SEO",
            "Site Handling, Site Sets & Routing",
            "System/Bootstrap/Configuration",
            "t3editor",
            "Tests",
            "Themes",
            "TypoScript",
            "WebHooks - Incoming = Reactions + Outgoing",
            "Workspaces"
        ],
        "categoriesUsed": [],
        "issue": null,
        "results": [],
        "unavailable": null
    }

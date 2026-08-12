.. _typo3_backend_module_lookup:

``typo3_backend_module_lookup``
===============================

List the backend modules registered in the TYPO3 installation you are working
in, with the extension that declares each one, its place in the module tree, its
labels and its route. A project extension's modules are in it, because the
installation is asked rather than a snapshot. Answers from: installation.

``readOnlyHint: true`` · ``destructiveHint: false`` · ``idempotentHint: true`` · ``openWorldHint: false``

Answers from :ref:`installation <answer-sources-installation>`.

Takes
-----

.. code-block:: yaml

    # Module identifier, label, route, or extension name to filter by. Omit to list
    # every module.
    query: string  # optional

Answers with
------------

.. code-block:: yaml

    query: string
    matchCount: integer  # optional
    # One of: installation. installation: its assembled runtime state answered.
    answeredBy: string  # optional
    modules:  # optional
      - identifier: string
        # The modules it sits under, outermost first.
        parents: [string]
        # The package that declares it.
        extension: string
        # Its label, with the translation domain reference behind it.
        labels: string  # optional
        # The backend route it answers on.
        path: string
        # Its declared before/after position, if any.
        position: string  # optional
    unsupported:  # optional
      # One of: no-installation, misconfigured, installation-not-answering.
      # no-installation: nothing to ask from here, and searched says where it
      # looked. misconfigured: an installation was named and could not be used, so
      # nothing was searched for. installation-not-answering: one was found and its
      # console did not answer — a stopped container or a database with no schema,
      # which is a state that ends without reinstalling anything.
      cause: string
      # What stopped it, in the words the attempt produced.
      reason: string
      # What the reason means where the message alone does not say it — a console
      # that starts and then fails on a missing table has a database without a
      # schema, not a broken installation. Empty where nothing beyond the reason is
      # known.
      diagnosis: string  # optional
      # Every directory the discovery walked, in order. "Nothing was found" and "the
      # server was started somewhere else" wear one sentence, and only this tells
      # them apart. Empty where discovery never ran.
      searched: [string]
      # What was set and could not be used. Null where nothing was set.
      misconfiguration: string or null  # optional
      settings:
        # Environment variable that names the installation root.
        root: string
        # Environment variable that names the console command.
        console: string

The answer carries exactly one of these sets of fields: ``query``,
``matchCount``, ``answeredBy``, ``modules`` — or ``query``, ``unsupported``.

Answered
--------

Recorded on 2026-08-08 by ``bin/cli tools:record``. Of two working directories,
because what this server answers depends on which one a client is standing in,
and neither fills the whole surface. Answered against core-checkout, TYPO3
14.3.6-dev, the 14.3 core checkout below .checkouts/, whose console could not
be reached: <installation> has no TYPO3 console — none of bin/typo3,
vendor/bin/typo3 exists. Answered against composer-project, TYPO3 14.3.0, the
installation this repository writes below .fixtures/, whose console answers.
The tools that declare ``answeredBy`` carry an answer from each, under a heading
naming which; every other answer is from the first alone, because nothing in it
would differ. Nothing checks what is below this heading; everything above it is
derived from the class that answers the call, and ``bin/cli tools:check`` holds
it.

modules
~~~~~~~

Called with:

.. code-block:: json

    {}


From the 14.3 core checkout below .checkouts/, whose console could not be reached
"""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""

Text:

.. code-block:: text

    This is not answerable here, which is not the same as an empty answer: <installation> has no TYPO3 console — none of bin/typo3, vendor/bin/typo3 exists.
    typo3_server_scope reports the installation and its console.


Data:

.. code-block:: json

    {
        "query": "",
        "unsupported": {
            "cause": "installation-not-answering",
            "reason": "<installation> has no TYPO3 console — none of bin/typo3, vendor/bin/typo3 exists",
            "diagnosis": "",
            "searched": [
                "<installation>"
            ],
            "misconfiguration": null,
            "settings": {
                "root": "TYPO3_DEV_COMPANION_ROOT",
                "console": "TYPO3_DEV_COMPANION_CONSOLE"
            }
        }
    }


From the installation this repository writes below .fixtures/, whose console answers
""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""

Text:

.. code-block:: text

    4 backend module(s):
    - web
      /module/web  (backend)
      Web
    - web > web_list
      /module/web/list  (backend)
      Records
    - web > acme_events
      /module/web/acme-events  (acme_events)
      Events
    - site
      /module/site  (backend)
      Site Management

    A module is declared in its extension's Configuration/Backend/Modules.php; the label in brackets is a translation domain reference.


Data:

.. code-block:: json

    {
        "query": "",
        "matchCount": 4,
        "modules": [
            {
                "identifier": "web",
                "parents": [],
                "extension": "backend",
                "labels": "Web",
                "path": "/module/web",
                "position": ""
            },
            {
                "identifier": "web_list",
                "parents": [
                    "web"
                ],
                "extension": "backend",
                "labels": "Records",
                "path": "/module/web/list",
                "position": "after:web_layout"
            },
            {
                "identifier": "acme_events",
                "parents": [
                    "web"
                ],
                "extension": "acme_events",
                "labels": "Events",
                "path": "/module/web/acme-events",
                "position": "after:web_list"
            },
            {
                "identifier": "site",
                "parents": [],
                "extension": "backend",
                "labels": "Site Management",
                "path": "/module/site",
                "position": ""
            }
        ],
        "answeredBy": "installation"
    }

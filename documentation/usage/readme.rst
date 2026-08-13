:navigation-title: Usage

Using the server
================

What it takes to have this server answering in your own project, and what you
are agreeing to when it does.

* :doc:`installing` — every client and layout
  ``bin/typo3-dev-companion install`` supports, and the configuration each one
  gets.
* :doc:`checking-it-answers` — the ladder from a written entry to a session that
  really has the tools, and what an answer says when the installation could not
  be reached.

It is a local subprocess, started by the client over stdio, and it reads. It
writes nothing into the TYPO3 installation it is pointed at — the one exception
is the feedback channel, which writes into this server's own checkout and is
offered from a standalone checkout alone. What it covers and what it declines to
answer is ``typo3_server_scope``, which a client is handed at connect.

Two things arrive with the install and are worth knowing before it runs. The
skills are copied into the client's own skills directory, so they are files in
your project from then on and a later release of this server does not correct
them. And what the install writes into a client's configuration is guarded, so
it can be written again without a second copy appearing beside the first.

What the server can then be asked is :doc:`the server <../server/index>`.

.. toctree::
    :hidden:

    installing
    checking-it-answers

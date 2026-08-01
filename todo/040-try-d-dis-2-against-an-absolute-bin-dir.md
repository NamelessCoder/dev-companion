# Try `D-DIS-2` against an absolute `bin-dir`

**Serves:** decisions/

The entry names two shapes it accepts and ignores: an absolute `bin-dir`, which
Composer allows, and a console invoked from somewhere other than the root — a
DDEV project whose container working directory is the docroot. The first is
answerable here with a fixture `composer.json` and no installation at all. What
would hold it is an `InstanceTest` case per shape; the DDEV half needs one of
the environments below and is that case's second half rather than a todo of its
own.

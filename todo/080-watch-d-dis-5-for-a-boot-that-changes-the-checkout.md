# Watch `D-DIS-5` for a boot that changes the checkout

**Serves:** decisions/

Reading a registry by booting the installation buys the only complete answer and
risks a side effect a lookup must not have: an `ext_localconf.php` that writes
outside the cache, or a hard failure on a database that is not running. The
symptom is concrete — a lookup that modifies the checkout, or one that takes the
full 90 seconds. Run the registry lookups against one environment below with a
clean tree and check `git status` afterwards. What would hold it is that check
as a recorded step of the run rather than a test here: this repository has no
installation to boot, and a test that mocks one would be measuring the mock.

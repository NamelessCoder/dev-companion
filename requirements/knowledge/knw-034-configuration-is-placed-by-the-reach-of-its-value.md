---
id: R-KNW-034
title: 'Configuration is placed by the reach of its value'
status: held
---

# R-KNW-034 — Configuration is placed by the reach of its value

**Configuration is placed by the reach of its value: site-specific values are
site settings, one-per-installation values are extension configuration, and a
scheduler task's parameters are stored with that task.**

The site-settings form is bound to the versions that have it.

## From

A per-site storage pid put into a new `ext_conf_template.txt` after the same
sitepackage's set settings had already been read (2026-07-30).

## Held by

- `HintsTest::aSettingIsPlacedByTheReachOfItsValue`
- `HintsTest::siteScopedConfigurationIsOfferedOnlyWhereSiteSettingsExist`

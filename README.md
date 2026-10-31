# Mautic - Cronfig plugin

This plugin will add a new menu item to the right hand side Mautic menu called *Cronfig*. Under this menu item there will be a dashboard which will let you create cron tasks in the [Cronfig.io](https://cronfig.io) service which will take care of the Mautic background tasks.

## Documentation

Find how to install this plugin and all related info in the [official documentation](https://docs.cronfig.io/integrations/mautic)

## Compatibility

This branch prepares plugin 4.0.0 for Mautic 7.2+ within 7.x. Composer requires `mautic/core-lib: ^7.2`; use a PHP version supported by your Mautic release. Suggested tasks are filtered against the commands installed in each instance.

| Branch | Plugin | Mautic | Compatibility fixture |
| --- | --- | --- | --- |
| `master` | 1.0.0 | 4.x | Legacy release |
| `5.x` | 2.1.0 (unreleased) | 5.2.x | 5.2.11 |
| `6.x` | 3.0.0 (unreleased) | 6.x | 6.0.9 |
| `7.x` | 4.0.0 (unreleased) | 7.2+ within 7.x | 7.2.1 |

### Upgrade notes

Back up the plugin and Mautic configuration, replace `plugins/CronfigBundle`, and clear Mautic's cache. Preserve your existing Cronfig API and secret keys. Existing Cronfig URLs continue to authenticate with those keys.

Review already scheduled tasks in Cronfig after a Mautic upgrade: filtering the dashboard does **not** change existing schedules in the service. In Mautic 5+, replace the removed `mautic:emails:send` task with `messenger:consume email --time-limit=50 --limit=100` if queued email delivery is configured. Avoid scheduling this in addition to an existing persistent email worker. The limit bounds each invocation; choose frequency/capacity for your queue volume. Remove jobs for plugins that are no longer installed. A failing console command now returns HTTP 500, so previously hidden failures may become visible.

### Validation

The compatibility workflow installs an empty official Mautic 7.2.1 release with a verified checksum. It uses no production data or credentials and does not publish releases. Local rehearsal checks have also run with PHP 8.3.

Inside an isolated Mautic installation with this plugin installed, run:

```sh
php plugins/CronfigBundle/Tests/Compatibility/smoke.php
phpunit --no-configuration --bootstrap plugins/CronfigBundle/Tests/Compatibility/bootstrap.php plugins/CronfigBundle/Tests/Unit
```

The smoke test boots the actual Mautic kernel, calls the registered public controller with in-memory test credentials, executes only console help/list operations, checks suggested tasks, and renders the Twig configuration with escaping checks. It never saves credentials or invokes cron tasks. It does not test the external Cronfig service or its browser dashboard; verify that integration before production rollout.

To prepare a distribution locally, run `python3 tools/package.py --output /tmp/CronfigBundle-4.0.0.zip`. The allowlisted archive contains only plugin runtime files and documentation, under `CronfigBundle/`, with a companion SHA256 checksum. Packaging does not tag, upload or publish anything. Publish/tag only after the compatibility workflow passes and the dashboard/service integration is accepted.

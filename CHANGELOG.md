# Changelog

## 2.1.0 — unreleased

- Prepare compatibility with Mautic 5.2+ within 5.x, maintained on the `5.x` branch.
- Advertise only installed console commands and use a bounded Messenger email worker.
- Respect console exit codes and quoted arguments; retry with `--force` only when supported.
- Compare secret keys without logging either key.
- Safely encode inline dashboard configuration and replace empty or outdated task secrets after saving an API key.
- Report unsuccessful API key saves correctly.
- Add a reproducible compatibility matrix using empty Mautic installations.

Existing remote schedules need review after upgrading Mautic; this release does not rewrite them.

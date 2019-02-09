Please refer first to [UPGRADE.md](UPGRADE.md) for the most important items that should be addressed before attempting to upgrade or during the upgrade of a vanilla Oro application.

The current file describes significant changes in the code that may affect the upgrade of your customizations.

## 3.0.0-beta (2018-03-30)
[Show detailed list of changes](incompatibilities-3-0-beta.md)

## 2.1.0 (2017-03-30)
[Show detailed list of changes](incompatibilities-2-1.md)
### Changed
- The following services were marked as `private`:
    - `oro_call.twig.call_extension`
### Removed
- Removed the following parameters from DIC:
    - `oro_call.twig.class`

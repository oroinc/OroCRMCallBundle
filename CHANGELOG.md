The upgrade instructions are available at [Oro documentation website](https://doc.oroinc.com/master/backend/setup/upgrade-to-new-version/).

The current file describes significant changes in the code that may affect the upgrade of your customizations.

## Changes in the Call package versions

- [5.1.0](#510-2023-03-31)
- [5.0.0](#500-2022-01-26)
- [4.2.0](#420-2020-01-29)
- [4.1.0](#410-2020-01-31)
- [4.0.0](#400-2019-07-31)
- [3.0.0](#300-2018-07-27)
- [2.1.0](#210-2017-03-30)

## 5.1.0 (2023-03-31)
[Show detailed list of changes](incompatibilities-5-1.md)

## 5.0.0 (2022-01-26)
[Show detailed list of changes](incompatibilities-5-0.md)

## 4.2.0 (2020-01-29)
[Show detailed list of changes](incompatibilities-4-2.md)

## 4.1.0 (2020-01-31)
[Show detailed list of changes](incompatibilities-4-1.md)

### Removed
* The `*.class` parameters for all entities were removed from the dependency injection container.
The entity class names should be used directly, e.g., `'Oro\Bundle\EmailBundle\Entity\Email'`
instead of `'%oro_email.email.entity.class%'` (in service definitions, datagrid config files, placeholders, etc.), and
`\Oro\Bundle\EmailBundle\Entity\Email::class` instead of `$container->getParameter('oro_email.email.entity.class')`
(in PHP code).

* All `*.class` parameters for service definitions were removed from the dependency injection container.

## 4.0.0 (2019-07-31)
[Show detailed list of changes](incompatibilities-4-0.md)

## 3.0.0 (2018-07-27)
[Show detailed list of changes](incompatibilities-3-0.md)

## 2.1.0 (2017-03-30)
[Show detailed list of changes](incompatibilities-2-1.md)

### Changed
- The following services were marked as `private`:
    - `oro_call.twig.call_extension`
    
### Removed
- The following parameters are removed from DIC:
    - `oro_call.twig.class`

UPGRADE FROM 2.0 to 2.1
=======================

- Removed the following parameters from DIC:
    - `oro_call.twig.class`
- The following services were marked as `private`:
    - `oro_call.twig.call_extension`
- Class `Oro\Bundle\CallBundle\Twig\OroCallExtension`
    - the construction signature of was changed. Now the constructor has only `ContainerInterface $container` parameter
    - removed property `protected $logCallPlaceholderFilter`

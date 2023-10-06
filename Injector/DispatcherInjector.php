<?php

namespace Bazinga\Bundle\PropelEventDispatcherBundle\Injector;

use Bazinga\Bundle\PropelEventDispatcherBundle\EventDispatcher\LazyEventDispatcher;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Psr\Log\LoggerInterface;

class DispatcherInjector
{
    public const MODEL_INTERFACE = 'EventDispatcherAwareModelInterface';

    private ContainerInterface $container;
    private array $classes;
    private ?LoggerInterface $logger;

    public function __construct(ContainerInterface $container, array $classes, LoggerInterface $logger = null)
    {
        $this->classes   = $classes;
        $this->container = $container;
        $this->logger    = $logger;
    }

    /**
     * Initializes the EventDispatcher-aware models.
     *
     * This method has to accept unknown classes as it is triggered during
     * the boot and so will be called before running the propel:build command.
     */
    public function initializeModels(): void
    {
        foreach ($this->classes as $id => $class) {
            $baseClass = sprintf('%s\\Base\\%s',
                substr($class, 0, strrpos($class, '\\')),
                substr($class, strrpos($class, '\\') + 1, strlen($class))
            );

            try {
                $ref = new \ReflectionClass($baseClass);
            } catch (\ReflectionException) {
                $this->log(sprintf('The class "%s" does not exist.', $baseClass));

                continue;
            }

            try {
                $ref = new \ReflectionClass($class);
            } catch (\ReflectionException) {
                $this->log(sprintf(
                    'The class "%s" does not exist. Either your model is not generated yet or you have an error in your listener configuration.',
                    $class
                ));

                continue;
            }

            if (!$ref->implementsInterface(self::MODEL_INTERFACE)) {
                $this->log(sprintf(
                    'The class "%s" does not implement "%s". Either your model is outdated or you forgot to add the EventDispatcherBehavior.',
                    $class,
                    self::MODEL_INTERFACE
                ));

                continue;
            }

            $class::setEventDispatcher(new LazyEventDispatcher($this->container, $id));
        }
    }

    private function log($message): void
    {
        $this->logger?->warning($message);
    }
}

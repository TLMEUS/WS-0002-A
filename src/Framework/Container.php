<?php
/**
 * This file contains the src/Framework/Container.php class for project WS-0000-A.
 * Based on work learned in the Udemy class "Write PHP Like a Pro: Build a
 * PHP MVC Framework From Scratch" taught by Dave Hollingworth.
 *
 * File information:
 * Project Name: WS-0000-A
 * Module Name: Source
 * Group Name: Framework
 * File Name: Container.php
 * File Author: Troy L Marker
 * Language: PHP 8.3
 *
 * File Copyright: 01/2024
 */
declare(strict_types=1);

namespace Framework;

use ReflectionClass;
use Closure;
use ReflectionNamedType;
use InvalidArgumentException;

/**
 * Class Container
 *
 * The Container class is responsible for managing dependencies and creating instances of objects.
 */
class Container {
    private array $registry = [];

    /**
     * Sets a value in the registry.
     *
     * @param string $name The name of the value to be stored.
     * @param Closure $value The value to be stored.
     *
     * @return void
     */
    public function set(string $name, Closure $value): void {
        $this->registry[$name] = $value;
    }

    /**
     * Retrieves an instance of the specified class.
     *
     * @param string $class_name The fully qualified name of the class.
     *
     * @return object The instance of the specified class.
     *
     * @throws \ReflectionException
     * @throws InvalidArgumentException If the constructor parameter in the $class_name class has no type declaration,
     * or if the constructor parameter in the $class_name class is an invalid type (only single named types are supported),
     * or if unable to resolve the constructor parameter of type '$type' in the $class_name class,
     * or if the $class_name is not found in the registry.
     */
    public function get(string $class_name): object {
        if (array_key_exists($class_name, $this->registry)) {
            return $this->registry[$class_name]();
        }
        $reflector = new ReflectionClass($class_name);
        $constructor = $reflector->getConstructor();
        $dependencies = [];
        if ($constructor === null) {
            return new $class_name;
        }
        foreach ($constructor->getParameters() as $parameter) {
            $type = $parameter->getType();
            if ($type === null) {
                throw new InvalidArgumentException("Constructor parameter 
                      '{$parameter->getName()}' 
                      in the $class_name class 
                      has no type declaration");
            }
            if ( ! ($type instanceof ReflectionNamedType)) {
                throw new InvalidArgumentException("Constructor parameter
                      '{$parameter->getName()}' 
                      in the $class_name class is an invalid type: '$type' 
                      - only single named types supported");
            }
            if ($type->isBuiltIn()) {
                throw new InvalidArgumentException("Unable to resolve
                      constructor parameter '{$parameter->getName()}'
                      of type '$type' in the $class_name class");
            }
            $dependencies[] = $this->get((string) $type);
        }
        return new $class_name(...$dependencies);
    }
}
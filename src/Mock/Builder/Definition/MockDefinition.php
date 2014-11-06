<?php

/*
 * This file is part of the Phony package.
 *
 * Copyright © 2014 Erin Millard
 *
 * For the full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Eloquent\Phony\Mock\Builder\Definition;

use Eloquent\Phony\Feature\FeatureDetector;
use Eloquent\Phony\Feature\FeatureDetectorInterface;
use Eloquent\Phony\Mock\Builder\Definition\Method\CustomMethodDefinition;
use Eloquent\Phony\Mock\Builder\Definition\Method\MethodDefinitionCollection;
use Eloquent\Phony\Mock\Builder\Definition\Method\MethodDefinitionCollectionInterface;
use Eloquent\Phony\Mock\Builder\Definition\Method\RealMethodDefinition;
use Eloquent\Phony\Mock\Builder\Definition\Method\TraitMethodDefinition;
use ReflectionClass;

/**
 * Represents a mock class definition.
 *
 * @internal
 */
class MockDefinition implements MockDefinitionInterface
{
    /**
     * Construct a new mock definition.
     *
     * @param array<string,ReflectionClass>|null $types                  The types.
     * @param array<string,callable|null>|null   $customMethods          The custom methods.
     * @param array<string,mixed>|null           $customProperties       The custom properties.
     * @param array<string,callable|null>|null   $customStaticMethods    The custom static methods.
     * @param array<string,mixed>|null           $customStaticProperties The custom static properties.
     * @param array<string,mixed>|null           $customConstants        The custom constants.
     * @param string|null                        $className              The class name.
     * @param FeatureDetectorInterface|null      $featureDetector        The feature detector to use.
     */
    public function __construct(
        array $types = null,
        array $customMethods = null,
        array $customProperties = null,
        array $customStaticMethods = null,
        array $customStaticProperties = null,
        array $customConstants = null,
        $className = null,
        FeatureDetectorInterface $featureDetector = null
    ) {
        if (null === $types) {
            $types = array();
        }
        if (null === $customMethods) {
            $customMethods = array();
        }
        if (null === $customProperties) {
            $customProperties = array();
        }
        if (null === $customStaticMethods) {
            $customStaticMethods = array();
        }
        if (null === $customStaticProperties) {
            $customStaticProperties = array();
        }
        if (null === $customConstants) {
            $customConstants = array();
        }
        if (null === $featureDetector) {
            $featureDetector = FeatureDetector::instance();
        }

        $this->types = $types;
        $this->customMethods = $customMethods;
        $this->customProperties = $customProperties;
        $this->customStaticMethods = $customStaticMethods;
        $this->customStaticProperties = $customStaticProperties;
        $this->customConstants = $customConstants;
        $this->className = $className;
        $this->featureDetector = $featureDetector;

        $this->isTraitSupported = $this->featureDetector->isSupported('trait');
    }

    /**
     * Get the types.
     *
     * @return array<string,ReflectionClass> The types.
     */
    public function types()
    {
        return $this->types;
    }

    /**
     * Get the custom methods.
     *
     * @return array<string,callable|null> The custom methods.
     */
    public function customMethods()
    {
        return $this->customMethods;
    }

    /**
     * Get the custom properties.
     *
     * @return array<string,mixed> The custom properties.
     */
    public function customProperties()
    {
        return $this->customProperties;
    }

    /**
     * Get the custom static methods.
     *
     * @return array<string,callable|null> The custom static methods.
     */
    public function customStaticMethods()
    {
        return $this->customStaticMethods;
    }

    /**
     * Get the custom static properties.
     *
     * @return array<string,mixed> The custom static properties.
     */
    public function customStaticProperties()
    {
        return $this->customStaticProperties;
    }

    /**
     * Get the custom constants.
     *
     * @return array<string,mixed> The custom constants.
     */
    public function customConstants()
    {
        return $this->customConstants;
    }

    /**
     * Get the class name.
     *
     * @return string|null The class name.
     */
    public function className()
    {
        return $this->className;
    }

    /**
     * Get the feature detector.
     *
     * @return FeatureDetectorInterface The feature detector.
     */
    public function featureDetector()
    {
        return $this->featureDetector;
    }

    /**
     * Get the type names.
     *
     * @return array<string> The type names.
     */
    public function typeNames()
    {
        $this->inspectTypes();

        return $this->typeNames;
    }

    /**
     * Get the parent class name.
     *
     * @return string|null The parent class name, or null if the mock will not extend a class.
     */
    public function parentClassName()
    {
        $this->inspectTypes();

        return $this->parentClassName;
    }

    /**
     * Get the interface names.
     *
     * @return array<string> The interface names.
     */
    public function interfaceNames()
    {
        $this->inspectTypes();

        return $this->interfaceNames;
    }

    /**
     * Get the trait names.
     *
     * @return array<string> The trait names.
     */
    public function traitNames()
    {
        $this->inspectTypes();

        return $this->traitNames;
    }

    /**
     * Get the method definitions.
     *
     * Calling this method will finalize the mock builder.
     *
     * @return MethodDefinitionCollectionInterface The method definitions.
     */
    public function methods()
    {
        $this->buildMethods();

        return $this->methods;
    }

    /**
     * Check if the supplied definition is equal to this definition.
     *
     * @return boolean True if equal.
     */
    public function isEqualTo(MockDefinitionInterface $definition)
    {
        return
            $definition->className() === $this->className &&
            $definition->typeNames() === $this->typeNames() &&
            $definition->customMethods() === $this->customMethods &&
            $definition->customProperties() === $this->customProperties &&
            $definition->customStaticMethods() === $this->customStaticMethods &&
            $definition->customStaticProperties() ===
                $this->customStaticProperties &&
            $definition->customConstants() === $this->customConstants;
    }

    /**
     * Inspect the supplied types and build caches of useful information.
     */
    protected function inspectTypes()
    {
        if (null !== $this->typeNames) {
            return;
        }

        $this->typeNames = array();
        $this->interfaceNames = array();
        $this->traitNames = array();

        foreach ($this->types as $type) {
            $this->typeNames[] = $typeName = $type->getName();

            if ($type->isInterface()) {
                $this->interfaceNames[] = $typeName;
            } elseif ($this->isTraitSupported && $type->isTrait()) {
                $this->traitNames[] = $typeName;
            } else {
                $this->parentClassName = $typeName;
            }
        }
    }

    /**
     * Build the method definitions.
     */
    protected function buildMethods()
    {
        if (null !== $this->methods) {
            return;
        }

        $methods = array();
        $traitMethods = array();

        foreach ($this->traitNames() as $typeName) {
            foreach ($this->types[$typeName]->getMethods() as $method) {
                $methodDefinition = new TraitMethodDefinition($method);
                $methods[$method->getName()] = $methodDefinition;
                $traitMethods[] = $methodDefinition;
            }
        }

        $parameterCounts = array();

        foreach ($this->interfaceNames() as $typeName) {
            foreach ($this->types[$typeName]->getMethods() as $method) {
                $methodName = $method->getName();
                $parameterCount = $method->getNumberOfParameters();

                if (
                    !isset($parameterCounts[$methodName]) ||
                    $parameterCount > $parameterCounts[$methodName]
                ) {
                    $methods[$methodName] = new RealMethodDefinition($method);
                    $parameterCounts[$methodName] = $parameterCount;
                }
            }
        }

        if ($typeName = $this->parentClassName()) {
            foreach ($this->types[$typeName]->getMethods() as $method) {
                if (
                    $method->isPrivate() ||
                    $method->isConstructor() ||
                    $method->isFinal()
                ) {
                    continue;
                }

                $methodName = $method->getName();
                $parameterCount = $method->getNumberOfParameters();

                if (
                    !isset($parameterCounts[$methodName]) ||
                    $parameterCount >= $parameterCounts[$methodName]
                ) {
                    $methods[$methodName] = new RealMethodDefinition($method);
                    $parameterCounts[$methodName] = $parameterCount;
                }
            }
        }

        $methodNames = array_keys($methods);
        $tokens = token_get_all('<?php ' . implode(' ', $methodNames));

        foreach ($methodNames as $index => $methodName) {
            $tokenIndex = $index * 2 + 1;

            if (
                !is_array($tokens[$tokenIndex]) ||
                $tokens[$tokenIndex][0] !== T_STRING
            ) {
                unset($methods[$methodName]);
            }
        }

        foreach ($this->customStaticMethods as $methodName => $callback) {
            $methods[$methodName] =
                new CustomMethodDefinition(true, $methodName, $callback);
        }

        foreach ($this->customMethods as $methodName => $callback) {
            $methods[$methodName] =
                new CustomMethodDefinition(false, $methodName, $callback);
        }

        $this->methods =
            new MethodDefinitionCollection($methods, $traitMethods);
    }

    private $types;
    private $customMethods;
    private $customProperties;
    private $customStaticMethods;
    private $customStaticProperties;
    private $customConstants;
    private $className;
    private $featureDetector;
    private $isTraitSupported;
    private $typeNames;
    private $parentClassName;
    private $interfaceNames;
    private $traitNames;
    private $methods;
}

<?php

/*
 * This file is part of the RollerworksSearch package.
 *
 * (c) Sebastiaan Stok <s.stok@rollerscapes.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Rollerworks\Component\Search\Metadata;

use Rollerworks\Component\Metadata\PropertyMetadata as BasePropertyMetadata;

/**
 * @internal
 */
final class PropertyMetadata implements BasePropertyMetadata
{
    private $propertyName;
    private $className;
    private $fieldName;
    private $fieldType;
    private $options;
    private $reflection;

    public function __construct($className, $propertyName, $fieldName, $fieldType, array $options = [])
    {
        $this->propertyName = $propertyName;
        $this->className = $className;
        $this->fieldName = $fieldName;
        $this->fieldType = $fieldType;
        $this->options = $options;
    }

    public function getClassName()
    {
        return $this->className;
    }

    public function getName()
    {
        return $this->propertyName;
    }

    public function getReflection()
    {
        if (null === $this->reflection) {
            $this->reflection = new \ReflectionProperty($this->className, $this->propertyName);
        }

        return $this->reflection;
    }

    /**
     * @return string
     */
    public function serialize()
    {
        return serialize(
            [
                $this->propertyName,
                $this->className,
                $this->fieldName,
                $this->fieldType,
                $this->options,
            ]
        );
    }

    /**
     * @param string $str
     *
     * @return array
     */
    public function unserialize($str)
    {
        list(
            $this->propertyName,
            $this->className,
            $this->fieldName,
            $this->fieldType,
            $this->options,
        ) = unserialize($str);
    }

    /**
     * @return string
     */
    public function getFieldName()
    {
        return $this->fieldName;
    }

    /**
     * @return string
     */
    public function getFieldType()
    {
        return $this->fieldType;
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }
}

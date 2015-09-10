<?php

/*
 * This file is part of the RollerworksSearch Component package.
 *
 * (c) Sebastiaan Stok <s.stok@rollerscapes.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Rollerworks\Component\Search\Metadata\Driver;

use Rollerworks\Component\Metadata\FileTrackingClassMetadata;
use Rollerworks\Component\Search\Exception\InvalidArgumentException;
use Rollerworks\Component\Search\Metadata\PropertyMetadata;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

/**
 * YamlFileDriver.
 *
 * @author Sebastiaan Stok <s.stok@rollerscapes.net>
 */
class YamlFileDriver extends AbstractFileDriver
{
    /**
     * {@inheritdoc}
     */
    protected function loadMetadataFromFile(\ReflectionClass $class, $file)
    {
        $className = $class->name;
        $properties = [];

        try {

            $data = Yaml::parse(file_get_contents($file));
        } catch (ParseException $e) {
            $e->setParsedFile($file);

            throw $e;
        }

        foreach ($data as $propertyName => $property) {
            $properties[$propertyName] = $this->createPropertyMetadata($file, $className, $propertyName, $property);
        }

        return new FileTrackingClassMetadata($className, $properties, [], null, [$class->getFileName(), $file]);
    }

    private function createPropertyMetadata($file, $className, $propertyName, array $property)
    {
        $this->assertArrayKeysExists($property, ['name', 'type'], $file, $className, $propertyName);

        return new PropertyMetadata(
            $className,
            $propertyName,
            $property['name'],
            $property['type'],
            (isset($property['options']) ? $property['options'] : [])
        );
    }

    private function assertArrayKeysExists(array $property, array $requiredKeys, $file, $className, $propertyName)
    {
        $missingKeys = [];

        foreach ($requiredKeys as $key) {
            if (!array_key_exists($key, $property)) {
                $missingKeys[] = $key;
            }
        }

        if ($missingKeys) {
            throw new InvalidArgumentException(
                sprintf(
                    'Expected property metadata of class "%s" property "%s", loaded from file "%s" to contain '.
                    'the following keys: %s. But the following keys are missing: %s.',
                    $className,
                    $propertyName,
                    $file,
                    implode(', ', $requiredKeys),
                    implode(', ', $missingKeys)
                )
            );
        }
    }
}

<?php

/*
 * This file is part of the RollerworksSearch package.
 *
 * (c) Sebastiaan Stok <s.stok@rollerscapes.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Rollerworks\Component\Search\Metadata\Driver;

use Rollerworks\Component\Metadata\Driver\FileLocator;
use Rollerworks\Component\Metadata\Driver\MappingDriver;
use Rollerworks\Component\Metadata\FileTrackingClassMetadata;

/**
 * Base file driver implementation.
 *
 * @author Sebastiaan Stok <s.stok@rollerscapes.net>
 */
abstract class AbstractFileDriver implements MappingDriver
{
    /**
     * @var FileLocator
     */
    private $locator;

    /**
     * @param FileLocator $locator
     */
    public function __construct(FileLocator $locator)
    {
        $this->locator = $locator;
    }

    /**
     * {@inheritdoc}
     */
    public function loadMetadataForClass(\ReflectionClass $class)
    {
        if (null === $path = $this->locator->findMappingFile($class->name)) {
            return;
        }

        return $this->loadMetadataFromFile($class, $path);
    }

    /**
     * {@inheritdoc}
     */
    public function getAllClassNames()
    {
        return $this->locator->getAllClassNames();
    }

    /**
     * {@inheritdoc}
     */
    public function isTransient($className)
    {
        return null !== $this->locator->findMappingFile($className);
    }

    /**
     * Parses the content of the file, and converts it to the desired metadata.
     *
     * @param \ReflectionClass $class
     * @param string           $file
     *
     * @return FileTrackingClassMetadata
     */
    abstract protected function loadMetadataFromFile(\ReflectionClass $class, $file);
}

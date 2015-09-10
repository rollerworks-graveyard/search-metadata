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

use Doctrine\Common\Annotations\Reader;
use ReflectionClass;
use Rollerworks\Component\Metadata\Driver\MappingDriver;
use Rollerworks\Component\Metadata\FileTrackingClassMetadata;
use Rollerworks\Component\Metadata\MappingException;
use Rollerworks\Component\Search\Metadata\Field;
use Rollerworks\Component\Search\Metadata\PropertyMetadata;

/**
 * AnnotationDriver.
 *
 * @author Sebastiaan Stok <s.stok@rollerscapes.net>
 */
class AnnotationDriver implements MappingDriver
{
    /**
     * @var Reader
     */
    private $reader;

    /**
     * The paths where to look for mapping files.
     *
     * @var array
     */
    private $paths = [];

    /**
     * The paths excluded from path where to look for mapping files.
     *
     * @var array
     */
    private $excludePaths = [];

    /**
     * @var string[]|null
     */
    private $classNames;

    /**
     * Constructor.
     *
     * @param Reader $reader
     * @param array  $paths
     */
    public function __construct(Reader $reader, $paths = null)
    {
        $this->reader = $reader;

        if ($paths) {
            $this->addPaths((array) $paths);
        }
    }

    /**
     * Appends lookup paths to metadata driver.
     *
     * @param array $paths
     */
    public function addPaths(array $paths)
    {
        $this->paths = array_unique(array_merge($this->paths, $paths));
    }

    /**
     * Retrieves the defined metadata lookup paths.
     *
     * @return array
     */
    public function getPaths()
    {
        return $this->paths;
    }

    /**
     * Append exclude lookup paths to metadata driver.
     *
     * @param array $paths
     */
    public function addExcludePaths(array $paths)
    {
        $this->excludePaths = array_unique(array_merge($this->excludePaths, $paths));
    }

    /**
     * Retrieve the defined metadata lookup exclude paths.
     *
     * @return array
     */
    public function getExcludePaths()
    {
        return $this->excludePaths;
    }

    /**
     * {@inheritdoc}
     */
    public function loadMetadataForClass(ReflectionClass $class)
    {
        $className = $class->name;
        $properties = [];

        foreach ($class->getProperties() as $reflectionProperty) {
            $annotation = $this->reader->getPropertyAnnotation(
                $reflectionProperty,
                'Rollerworks\Component\Search\Metadata\Field'
            );

            if (null !== $annotation) {
                /** @var Field $annotation */
                $properties[] = new PropertyMetadata(
                    $className,
                    $reflectionProperty->name,
                    $annotation->getName(),
                    $annotation->getType(),
                    $annotation->getOptions()
                );
            }
        }

        return new FileTrackingClassMetadata($className, $properties, [], null, [$class->getFileName()]);
    }

    /**
     * {@inheritdoc}
     *
     * Borrowed from Doctrine Common AnnotationDriver.
     */
    public function getAllClassNames()
    {
        if ($this->classNames !== null) {
            return $this->classNames;
        }

        if (!$this->paths) {
            return [];
        }

        $classes = [];
        $includedFiles = [];

        foreach ($this->paths as $path) {
            if (!is_dir($path)) {
                throw MappingException::fileMappingDriversRequireConfiguredDirectoryPath($path);
            }

            $iterator = new \RegexIterator(
                new \RecursiveIteratorIterator(
                    new \RecursiveDirectoryIterator($path, \FilesystemIterator::SKIP_DOTS),
                    \RecursiveIteratorIterator::LEAVES_ONLY
                ),
                '/^.+\.php$/i',
                \RecursiveRegexIterator::GET_MATCH
            );

            foreach ($iterator as $file) {
                $sourceFile = $file[0];

                if (!preg_match('(^phar:)i', $sourceFile)) {
                    $sourceFile = realpath($sourceFile);
                }

                $current = str_replace('\\', '/', $sourceFile);

                foreach ($this->excludePaths as $excludePath) {
                    $exclude = str_replace('\\', '/', realpath($excludePath));

                    if (strpos($current, $exclude) !== false) {
                        continue 2;
                    }
                }

                require_once $sourceFile;

                $includedFiles[] = $sourceFile;
            }
        }

        $declared = get_declared_classes();

        foreach ($declared as $className) {
            $rc = new \ReflectionClass($className);
            $sourceFile = $rc->getFileName();

            if (in_array($sourceFile, $includedFiles, true) && $this->isTransient($className)) {
                $classes[] = $className;
            }
        }

        $this->classNames = $classes;

        return $classes;
    }

    /**
     * {@inheritdoc}
     */
    public function isTransient($className)
    {
        $r = new ReflectionClass($className);

        foreach ($r->getProperties() as $reflectionProperty) {
            $annotation = $this->reader->getPropertyAnnotation(
                $reflectionProperty,
                'Rollerworks\Component\Search\Metadata\Field'
            );

            if (null !== $annotation) {
                return true;
            }
        }

        return false;
    }
}

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

use Rollerworks\Component\Metadata\FileTrackingClassMetadata;
use Rollerworks\Component\Search\Exception\InvalidArgumentException;
use Rollerworks\Component\Search\Metadata\PropertyMetadata;
use Rollerworks\Component\Search\Metadata\SimpleXMLElement;
use Rollerworks\Component\Search\Util\XmlUtil;

/**
 * XmlFileDriver.
 *
 * @author Sebastiaan Stok <s.stok@rollerscapes.net>
 */
class XmlFileDriver extends AbstractFileDriver
{
    /**
     * {@inheritdoc}
     */
    protected function loadMetadataFromFile(\ReflectionClass $class, $file)
    {
        $className = $class->name;
        $properties = [];

        $xml = $this->parseFile($file);

        /** @var SimpleXMLElement $property */
        foreach ($xml as $property) {
            $name = (string) $property['id'];

            $properties[$name] = new PropertyMetadata(
                $className,
                $name,
                (string) $property['name'],
                (string) $property['type'],
                isset($property->option) ? $property->getArgumentsAsPhp('option') : []
            );
        }

        return new FileTrackingClassMetadata($className, $properties, [], null, [$class->getFileName(), $file]);
    }

    /**
     * Parses a XML file.
     *
     * @param string $file Path to a file
     *
     * @throws InvalidArgumentException When loading of XML file returns error
     *
     * @return SimpleXMLElement
     */
    private function parseFile($file)
    {
        static $mappingSchema;

        if (!$mappingSchema) {
            $mappingSchema = realpath(__DIR__.'/schema/dic/metadata/metadata-1.0.xsd');
        }

        try {
            $dom = XmlUtil::loadFile($file, $mappingSchema);
        } catch (\InvalidArgumentException $e) {
            throw new InvalidArgumentException(sprintf('Unable to parse file "%s".', $file), $e->getCode(), $e);
        }

        return simplexml_import_dom($dom, 'Rollerworks\\Component\\Search\\Metadata\\SimpleXMLElement');
    }
}

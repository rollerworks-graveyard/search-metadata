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

use Rollerworks\Component\Metadata\MetadataFactory;
use Rollerworks\Component\Metadata\NullClassMetadata;

/**
 * MetadataReader.
 *
 * @author Sebastiaan Stok <s.stok@rollerscapes.net>
 */
class MetadataReader implements MetadataReaderInterface
{
    /**
     * @var MetadataFactory
     */
    private $metadataFactory;

    /**
     * Constructor.
     *
     * @param MetadataFactory $metadataFactory
     */
    public function __construct(MetadataFactory $metadataFactory)
    {
        $this->metadataFactory = $metadataFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function getSearchFields($class)
    {
        $fields = [];
        $metadata = $this->metadataFactory->getMergedClassMetadata(
            $class,
            MetadataFactory::INCLUDE_INTERFACES & MetadataFactory::INCLUDE_TRAITS
        );

        if ($metadata instanceof NullClassMetadata) {
            return $fields;
        }

        /** @var PropertyMetadata $property */
        foreach ($metadata->getProperties() as $property) {
            $fields[$property->getFieldName()] = new SearchField(
                $property->getFieldName(),
                $property->getClassName(),
                $property->getName(),
                false,
                $property->getFieldType(),
                $property->getOptions()
            );
        }

        return $fields;
    }

    /**
     * {@inheritdoc}
     */
    public function getSearchField($class, $field)
    {
        $fieldsMetadata = $this->getSearchFields($class);

        return isset($fieldsMetadata[$field]) ? $fieldsMetadata[$field] : null;
    }
}

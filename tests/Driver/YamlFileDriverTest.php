<?php

/*
 * This file is part of the RollerworksSearch package.
 *
 * (c) Sebastiaan Stok <s.stok@rollerscapes.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Rollerworks\Component\Search\Tests\Metadata\Driver;

use Rollerworks\Component\Search\Metadata\Driver\YamlFileDriver;

final class YamlFileDriverTest extends MetadataFileDriverTestCase
{
    protected function getDriver()
    {
        return new YamlFileDriver($this->getFileLocator('.yml'));
    }

    protected function getFailureException()
    {
        return [
            'Rollerworks\Component\Search\Exception\InvalidArgumentException',
            '#Expected property metadata of class ".+Client" property "name", loaded '.
            'from file ".+/Config[^\.]Entity\.Client\.yml" to contain the following keys: name, type. '.
            'But the following keys are missing: type.#i',
        ];
    }
}

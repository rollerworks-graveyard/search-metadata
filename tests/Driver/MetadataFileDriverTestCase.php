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

use Rollerworks\Component\Metadata\Driver\PathByPrefixFileLocator;
use Rollerworks\Component\Search\Tests\Metadata\MetadataDriverTestCase;

abstract class MetadataFileDriverTestCase extends MetadataDriverTestCase
{
    protected function getFileLocator($fileExtension)
    {
        return new PathByPrefixFileLocator(
            ['Rollerworks\\Component\\Search\\Tests\\Metadata\\Fixtures' => dirname(__DIR__).'/Fixtures/Config'],
            $fileExtension
        );
    }
}

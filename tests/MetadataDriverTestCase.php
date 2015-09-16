<?php

/*
 * This file is part of the RollerworksSearch package.
 *
 * (c) Sebastiaan Stok <s.stok@rollerscapes.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Rollerworks\Component\Search\Tests\Metadata;

use Rollerworks\Component\Metadata\Cache\ArrayCache;
use Rollerworks\Component\Metadata\Cache\Validator\AlwaysFreshValidator;
use Rollerworks\Component\Metadata\CacheableMetadataFactory;
use Rollerworks\Component\Metadata\Driver\MappingDriver;
use Rollerworks\Component\Search\Metadata\MetadataReader;
use Rollerworks\Component\Search\Metadata\SearchField;
use Rollerworks\Component\Search\Searches;

abstract class MetadataDriverTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @var MetadataReader
     */
    private $reader;

    protected function setUp()
    {
        $metadataFactory = new CacheableMetadataFactory(
            $this->getDriver(),
            new ArrayCache(),
            new AlwaysFreshValidator(),
            ['Rollerworks\Component\Search\Metadata\MetadataReader', 'createClassMetadata']
        );

        $this->reader = new MetadataReader($metadataFactory);

        // Ensure the loader is compatible
        Searches::createSearchFactoryBuilder()
            ->setMetaReader($this->reader)
            ->getSearchFactory()
        ;
    }

    /**
     * @return MappingDriver
     */
    abstract protected function getDriver();

    /**
     * @return array array with [FQC exception class, message regexp]
     */
    abstract protected function getFailureException();

    /**
     * @test
     */
    public function it_returns_a_registered_field()
    {
        $userClass = 'Rollerworks\Component\Search\Tests\Metadata\Fixtures\Entity\User';
        $groupClass = 'Rollerworks\Component\Search\Tests\Metadata\Fixtures\Entity\Group';

        $this->assertEquals(
            new SearchField('uid', $userClass, 'id', true, 'integer', []),
            $this->reader->getSearchField($userClass, 'uid')
        );

        $this->assertEquals(
            new SearchField('username', $userClass, 'name', false, 'text', [
                'name' => 'doctor',
                'last' => ['who', 'zeus'],
            ]),
            $this->reader->getSearchField($userClass, 'username')
        );

        // Group
        $this->assertNull($this->reader->getSearchField($groupClass, 'id'));
        $this->assertNull($this->reader->getSearchField($groupClass, 'name'));
    }

    /**
     * @test
     */
    public function it_gets_all_classes()
    {
        $driver = $this->getDriver();

        $classes = $driver->getAllClassNames();
        sort($classes);

        $this->assertEquals(
            [
                'Rollerworks\Component\Search\Tests\Metadata\Fixtures\Entity\Client',
                'Rollerworks\Component\Search\Tests\Metadata\Fixtures\Entity\User',
            ],
            $classes
        );
    }

    /**
     * @test
     */
    public function it_errors_when_data_is_invalid()
    {
        $exceptionInfo = $this->getFailureException();

        $this->setExpectedExceptionRegExp($exceptionInfo[0], $exceptionInfo[1]);
        $this->reader->getSearchFields('Rollerworks\Component\Search\Tests\Metadata\Fixtures\Entity\Client');
    }
}

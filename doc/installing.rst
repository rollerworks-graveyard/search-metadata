Installing the Library
======================

Installing this extension package is very simple. Assuming you have already
installed `Composer`_ and set-up your dependencies. Installing this extension
with Composer is as easy as:

.. code-block:: bash

    $ php composer.phar require rollerworks/search-metadata

From the directory where your ``composer.json`` file is located.

Now, Composer will automatically download all required files, and install them
for you. After this you can enable the Metadata loader for
RollerworksSearch in your application.

.. note::

    For best performance it's advices to enable caching of the metadata,
    the examples in this document use the ``rollerworks/metadata-doctrine-cache``
    which needs to be installed by running the following command:

    .. code-block:: bash

        $ php composer.phar require rollerworks/metadata-doctrine-cache

    If you don't want to use the Doctrine Cache you may use any Caching library,
    see the documentation of the `Rollerworks Metadata Component`_ for all details.

Enabling the Metadata Reader
----------------------------

Don't worry, most of the work is already done for you.
You only need to glue to these pieces together.

The examples are heavily commented to help you with understanding
whats going on.

First initialize the :class:`Rollerworks\\Component\\Search\\Metadata\\MetadataReader`:

.. code-block:: php

    use Doctrine\Common\Cache\FilesystemCache;
    use Rollerworks\Component\Metadata\Cache\ArrayCache;
    use Rollerworks\Component\Metadata\Cache\ChainCache;
    use Rollerworks\Component\Metadata\Cache\DoctrineCache;
    use Rollerworks\Component\Metadata\Cache\Validator\FileTrackingValidator;
    use Rollerworks\Component\Metadata\CacheableMetadataFactory;
    use Rollerworks\Component\Metadata\Driver\ChainDriver;
    use Rollerworks\Component\Metadata\Driver\PathByPrefixFileLocator;
    use Rollerworks\Component\Search\Metadata\Driver as MappingDriver;
    use Rollerworks\Component\Search\Metadata\MetadataReader

    // As not all your models/entities always are in the same location.
    // You need to tell the metadata driver where your class metadata (or mapping data)
    // is located.
    //
    // The PathByPrefixFileLocator maps a namespace prefix to a directory path.
    // The ChainDriver allows to use multiple mapping drivers.
    //
    // The `Acme\Model` namespace prefix is mapped to `{current-directory}/Acme/Resources/Search`.
    // Note that namespace separators are replaced with a single dot.
    // So a namespaces like `Acme\Model\Advanced` is mapped as `{current-directory}/Acme/Resources/Search/Advanced.{ClassName}.{ext}`
    // Eg: `Acme\Model\Advanced\MyModel` is mapped to `{current-directory}/Acme/Resources/Search/Advanced.MyModel.yml`

    // Caution: Always use a full location to the mapping directory.
    $paths = [
        'Acme\\Model\\' => __DIR__.'/Acme/Resources/Search',
        'MyCorp\\Model\\' => __DIR__.'/MyCorp/Resources/Search',
    ];

    // The AnnotationReader requires some additional configuring and thus is not enabled in this example.
    // See for complete details:
    // http://docs.doctrine-project.org/projects/doctrine-common/en/latest/reference/annotations.html#setup-and-configuration
    // http://docs.doctrine-project.org/projects/doctrine-common/en/latest/reference/annotations.html#registering-annotations

    // $annotationReader = new AnnotationReader();

    $driver = new ChainDriver([
        // Add one or more drivers.
        new MappingDriver\YamlFileDriver(new PathByPrefixFileLocator($paths), '.yml');
        new MappingDriver\XmlFileDriver(new PathByPrefixFileLocator($paths), '.xml');

        // The AnnotationDriver only uses the $paths for the getAllClassNames() method.
        // new MappingDriver\AnnotationDriver($annotationReader, $paths)
    ]);

    // Cache provider for keeping loaded metadata.
    // The ChainCache will try all catches, and populate all the previous cache layers (that are assumed to be faster).
    $metadataCache = new ChainCache([
        new ArrayCache(),
        new DoctrineCache(), // populates the ArrayCache() (which is faster but not persistent)
    ]);

    // The FileTrackingValidator ensures the cached data is still fresh.
    // In production you would properly want to use the `Rollerworks\Component\Metadata\Cache\Validator\AlwaysFreshValidator`
    // which doesn't actually validate anything but is much faster!
    $cacheFreshnessValidator = new FileTrackingValidator();

    $metadataFactory = new CacheableMetadataFactory(
        $driver,
        $metadataCache,
        $cacheFreshnessValidator,
        // ClassMetadata Builder callback (should not be changed)
        ['Rollerworks\Component\Search\Metadata\MetadataReader', 'createClassMetadata']
    );

    $metadataReader = new MetadataReader($metadataFactory);

.. tip::

    If you only want to use single driver you can do just that.
    The ``ChainDriver`` is only used for convenience.

After this create a ``SearchFactoryBuilder`` and set the
``MetadataReader`` as metadata reader on the ``SearchFactoryBuilder``.

.. code-block:: php

    use Rollerworks\Component\Search\Searches;

    $searchFactory = Searches::createSearchFactoryBuilder()
        // ...
        ->setMetaReader($metadataReader)
        ->getSearchFactory()
    ;

That's it, the metadata reader is now ready for usage.

Now :doc:`configure the metadata <mapping>` of your classes, and your done.
Good luck!

.. _`Composer`: http://getcomposer.org/
.. _`downloading Composer`: http://getcomposer.org/download/
.. _`Rollerworks Metadata Component`: https://github.com/rollerworks/rollerworks-metadata

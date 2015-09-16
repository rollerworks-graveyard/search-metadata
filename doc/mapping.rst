Configuring Class metadata Mapping
==================================

Now that the Metadata Reader is installed and configured it's time to
configure the metadata of your classes, or else this extension is pretty
useless.

.. note::

    It's possible to overwrite the metadata of parent class.
    Simple (re)define the metadata in a child class.

As not everyone has the same preference when it comes a configuration
format you can choose from three different formats.

The information can be stored directly with the class using `PHP Annotations`_,
or as a separate file using either YAML or XML.

For the mapping files it's assumed to have a mapping of
``Acme\Store\Model`` to ``Acme/Resources/Search``.

.. configuration-block::

    .. code-block:: php-annotations

        // src/Acme/Store/Model/Product.php

        namespace Acme\Store\Model;

        use Rollerworks\Component\Search\Metadata\Field as SearchField;

        class Product
        {
            /**
             * @SearchField("product_id", type="number")
             */
            protected $id;

            /**
             * @SearchField("product_name", type="text")
             */
            protected $name;

            /**
             * @SearchField("product_price", type="decimal", options={min=0.01})
             */
            protected $price;

            // ...
        }

    .. code-block:: yaml

        # Acme/Resources/Search/Product.yml
        id:
            # Name is the search-field name
            name: product_id
            type: number
            accept-ranges: true
            accept-compares: true

        name:
            name: product_name
            type: text

        price:
            name: product_price
            accept-ranges: true
            accept-compares: true
            type:
                name: decimal
                params:
                    min: 0.01

    .. code-block:: xml

        <!-- Acme/Resources/Search/Product.xml -->

        <?xml version="1.0" encoding="UTF-8"?>
        <properties>
            <property id="id" name="product_id">
                <type name="number" />
            </property>
            <property id="name" name="product_name">
                <type name="text" />
            </property>
            <property id="name" name="product_name">
                <type name="text" />
            </property>
            <property id="price" name="product_price" accept-ranges="true" accept-compares="true">
                <type name="text">
                    <param key="min" type="float">0.01</param>
                    <!-- An array-value is build as follow. Key and type are optional, type is required for collections -->
                    <!--
                    <option key="key" type="collection">
                        <option type="string">value</option>
                        <option type="collection">
                            <value key="foo">value</option>
                        </option>
                    </option>
                    -->
                </type>
            </property>
        </properties>

.. caution::

    A class can accept only one metadata definition format.

    For example, it is not possible to mix YAML metadata definitions with
    annotated PHP class definitions.

.. tip::

    The metadata is not limited to only classes, you can also define metadata
    for traits and interfaces! With no difference in defining.

.. _`PHP Annotations`: http://docs.doctrine-project.org/projects/doctrine-common/en/latest/reference/annotations.html

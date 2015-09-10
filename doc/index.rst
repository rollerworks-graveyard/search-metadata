RollerworksSearch Metadata extension
====================================

Before you get started with this extension make sure you have a good
understanding on how to use `RollerworksSearch`_ and know how to integrate
it in your application.

What is this extension about?
-----------------------------

The ``FieldSetBuilder`` allows to import the field configuration from
class metadata, and that's were this extension comes in.

This extension uses the `Rollerworks Metadata Component`_ for handling
the actual metadata.

In short, the ``FieldSetBuilder`` uses the ``MetadataReader`` for loading
metadata of a class, the actual loading is done by mapping-drivers and a
MetadataFactory for delegating all the work (and ensuring metadata is properly
merged).

After :doc:`installing </installing>` this extension
you need to :doc:`configure the metadata <mapping>` of your classes.
And you are ready to use this extension.

.. toctree::
    :maxdepth: 2

    installing
    mapping

.. _`RollerworksSearch`: https://github.com/rollerworks/RollerworksSearch
.. _`Rollerworks Metadata Component`: https://github.com/rollerworks/rollerworks-metadata

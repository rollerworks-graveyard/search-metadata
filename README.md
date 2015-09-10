RollerworksSearch Metadata extension
====================================

This package provides a Metadata reader for [RollerworksSearch][1].

The Metadata reader uses the [Rollerworks Metadata component][2] for loading Metadata
from e.g. an XML document, a YAML file or PHP Annotations.

RollerworksSearch is a powerful open-source Search system, if you are new to
RollerworksSearch, please read the documentation in the [main repository][1].

Installation
------------

To install this extension, add the `rollerworks/search-metadata` package
to your composer.json

```bash
$ php composer.phar require rollerworks/search-metadata
```

Documentation
-------------

[Read the Documentation for master][4]

The documentation for this package is written in [reStructuredText][3] and can be built
into standard HTML using [Sphinx][4].

To build the documentation do the following:

1. Install [Spinx][4]
2. Change to the `doc` directory on the command line
3. Run `make html`

This will build the documentation into the `doc/_build/html` directory.

Further information can be found in The Symfony [documentation format][5] article.

> The Sphinx extensions and theme are installed sing Git submodules
> and don't need to be downloaded separately.

Contributing
------------

If you'd like to contribute to this project, please see the [Rollerworks contributing guide lines][3]
for more information.

License
-------

The source of this package is subject to the MIT license that is bundled
with this source code in the file [LICENSE](LICENSE).

[1]: https://github.com/rollerworks/RollerworksSearch
[2]: https://github.com/rollerworks/rollerworks-metadata
[3]: https://github.com/rollerworks/RollerworksSearch#contributing
[4]: http://rollerworks-search-metadata.readthedocs.org/en/latest/

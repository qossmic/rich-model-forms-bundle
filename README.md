Rich Model Forms Bundle
=======================

The Rich Model Forms Bundle enhances the [Symfony Form component](https://symfony.com/doc/current/forms.html) with
useful options that ease the work with rich domain models.

Installation
------------

Use Composer to install the bundle:

```bash
$ composer require sensiolabs-de/rich-model-forms-bundle
```

When using Symfony Flex, the bundle will be enabled automatically. Otherwise, you need to make sure that the bundle is
registered in your application kernel.

Usage
-----

The bundle currently supports the following use cases:

* [Differing Property Paths For Reading And Writing](docs/mapping.md)

* [Support for constructors with arguments and for value objects](docs/factory_value_object.md)

* [Enhanced exception handling](docs/exception_handling.md)

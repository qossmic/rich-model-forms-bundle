CHANGELOG
=========

0.5.0
-----

* Fixed compatibility with `symfony/translation` 5.0.
* Dropped support for Symfony components < 4.4.

0.4.0
-----

* Allow to use the bundle with Symfony 5 components.

0.3.0
-----

* Added support for mapping several form fields to a single method of the underlying model.
* Exceptions thrown during instantiating value objects are now caught and mapped back to the form as transformation
  failures.
* [BC BREAK] The `ValueObjectTransformer` requires an `ExceptionHandlerRegistry` instance.
* [BC BREAK] The first argument's type of the `ExceptionHandlerInterface::getError()` method has been changed from
  `FormInterface` to `FormConfigInterface`.
* [BC BREAK] The return type of `ExceptionHandlerInterface::getError()` has been changed to `SensioLabs\RichModelForms\ExceptionHandling\Error`.
* [BC BREAK] A `TranslatorInterface` and the translation domain to be used must now be passed to the `FormExceptionHandler`
  instead of passing to the individual `ExceptionHandlerInterface` implementations.

0.2.1
-----

* Fix to actually use a configured factory when submitting non-compound forms.
* Fixed passing submitted data to the configured factory for compound forms.
* Abstain from trying to map buttons to the data when creating value objects.
* Raise an error when the `data_class` option is used while the `immutable` option is enabled.

0.2.0
-----

* [BC BREAK] renamed `SensioLabs\RichModelForms\ExceptionHandling\ExceptionHandler` to
  `SensioLabs\RichModelForms\ExceptionHandling\ExceptionHandlerInterface`
* Added a `PropertyMapperInterface` whose implementations can be passed to forms using the new
  `property_mapper` option to map data to forms and vice versa programmatically.
* The `expected_exception` option is deprecated and will be removed in 0.3. Use `handle_exception` instead.

0.1.0
-----

Initial release of the bundle.

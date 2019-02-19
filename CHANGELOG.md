CHANGELOG
=========

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

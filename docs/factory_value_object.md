Initializing Objects
====================

When a form is not initialized with existing data (for example, because the object is to be created based on the user
submitted data), the Form component, by default, will create a new empty instance by calling the constructor of the
configured [data class](https://symfony.com/doc/current/reference/forms/types/form.html#data-class).

This approach does not work for models that require some initial data to be passed to the constructor (for example, to
ensure that the internal state of the object is always valid).

The `factory` option can be used to tell the Form component to create the initial data object in a more sophisticated
manner. The value passed here can be any of the following:

* When given a string, it must refer to an existing class whose constructor will be called (and thus must be `public`).

* When given an array, the value must be a valid [callable](http://www.php.net/manual/en/function.is-callable.php).

* Finally, the value can be a closure. In this case, the form data is passed as arguments to this anonymous function
  allowing for full flexibility on how to create the initial object.

Mapping Value Objects
=====================

When working with value objects, you have to pass all data the value object consists of as a whole to be sure that it
is always in a valid state and cannot be changed. This however means that every time a submitted form is mapped to your
model, a new value object must be created instead of manipulating the existing data.

Therefore, besides configuring how to create new instances (see the `factory` option above) you also need to tell the
form that the underlying model is immutable using the option with the same name:

```php
// ...

class PriceType extends AbstractType
{
    // ...

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('factory', Price::class);
        $resolver->setDefault('immutable', true);
    }
}
```

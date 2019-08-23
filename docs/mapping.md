Differing Property Paths for Reading and Writing
================================================

When your model provides methods for reading and writing an attribute whose names can not be handled by the built-in
`property_path` option, you can use `read_property_path` and `write_property_path` to separate read and write access.

Given a model like this:

```php
class Category
{
    private $name;

    // ...

    public function getName(): string
    {
        return $this->name;
    }

    public function rename(string $name): void
    {
        $this->validateName($name);

        $this->name = $name;
    }
```

The corresponding form type can be configured this way:

```php
// ...

class CategoryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'read_property_path' => 'getName',
                'write_property_path' => 'rename',
            ])
        ;
}
```

Mapping Several Form Fields to a Single Method
----------------------------------------------

When a method that changes the state of your model requires more than one argument you need to pass the method name as
is to the `write_property_path` option of all the form fields that should act as an argument:

```php
class Order
{
    private $shippingAddress;
    private $trackingNumber;

    public function ship(Address $address, string $trackingNumber): void
    {
        $this->shippingAddress = $address;
        $this->trackingNumber = $trackingNumber;
    }

    // ...
}
```

In this example, the `ship()` method requires two arguments, the address to ship to and the tracking number of the
parcel service.

The corresponding form type can now look like this:

```php
// ...

class ShipOrderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('address', AddressType::class, [
                'read_property_path' => 'shippingAddress',
                'write_property_path' => 'ship',
            ])
            ->add('trackingNumber', TextType::class, [
                'read_property_path' => 'trackingNumber',
                'write_property_path' => 'ship',
            ])
        ;
    }

    // ...
}
```

Reading Data Based on the Model's State
---------------------------------------

If the method used to expose data from the model depends on its state, `read_property_path` can be a closure that will
be executed when the model is mapped to the form:

```php
// ...

class CategoryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('parent', ChoiceType::class, [
                'choices' => $options['categories'],
                'read_property_path' => function (Category $category): ?Category {
                    if ($category->hasParent()) {
                        return $category->getParent();
                    }

                    return null;
                },
            ])
        ;
}
```

In the example above, this proves to be useful when `getParent()` throws an exception in case the category is a root
category (i.e. it has no parent category).

Mapping to the Model Depending on the Submitted Form Data
---------------------------------------------------------

When your model expects different method calls depending on the state change induced by the submitted form data, the
`write_property_path` option can be a closure. It will receive the underlying model as well as the data submitted by the
user:

```php
// ...

public function buildForm(FormBuilderInterface $builder, array $options): void
{
    $builder
        ->add('state', ChoiceType::class, [
            'choices' => [
                'active' => true,
                'paused' => false,
            ],
            'read_property_path' => 'isSuspended',
            'write_property_path' => function (Subscription $subscription, $submittedData): void {
                if (true === $submittedData) {
                    $subscription->reactivate();
                } elseif (false === $submittedData) {
                    $subscription->suspend();
                }
            },
        ])
    ;
}
```

Custom Property Mappers
-----------------------

Alternatively, you can also implement the `PropertyMapperInterface` and fully customize the mapping to your needs:

```php
// ...
use SensioLabs\RichModelForms\DataMapper\PropertyMapperInterface;

public function buildForm(FormBuilderInterface $builder, array $options): void
{
    $builder
        ->add('state', ChoiceType::class, [
            'choices' => [
                'active' => true,
                'paused' => false,
            ],
            'property_mapper' => new class() implements PropertyMapperInterface {
                public function readPropertyValue($data)
                {
                    return $data->isSuspended();
                }

                public function writePropertyValue($data, $value): void
                {
                    if (true === $value) {
                        $subscription->reactivate();
                    } elseif (false === $value) {
                        $subscription->suspend();
                    }
                }
            },
        ])
    ;
}
```

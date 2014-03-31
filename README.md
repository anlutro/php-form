# PHP Form Builder [![Build Status](https://travis-ci.org/anlutro/php-form.png?branch=master)](https://travis-ci.org/anlutro/php-form) [![Latest Version](http://img.shields.io/github/tag/anlutro/php-form.svg)](https://github.com/anlutro/php-form/releases)

Sick of the bugs and quirks present in the default Laravel 4 form builder, I wrote my own and made it framework-agnostic. Features include:

- Each form is its own object with (optionally) its own behaviours
- Set a model for the form object to pre-fill the form inputs with
- Get old input from session
- Validate input

WARNING: Backwards compatibility is not guaranteed during version 0.x.

## Installation

`composer require anlutro/php-form`

Pick the latest stable version from packagist or the GitHub tag list.

### Laravel 4

Add 'anlutro\Form\ServiceProvider' to the list of providers in app/config/app.php.

### Other frameworks/raw PHP

You will need to set up a shared instance of `anlutro\Form\Builder`, and this should be injected into all Form instances.

In order to get input from a Form instance, you should `setRequest` on the Builder instance. The request should be an instance of `Symfony\Component\HttpFoundation\Request`.

For old input from session, CSRF tokens and validation to work, you need to construct and set a session and/or validation service on the Builder instance. The interfaces are located in the `Adapters` namespace, and once you have an object that implements these you can set them via `setSessionAdapter` and `setValidationAdapter`.

If you have written an adapter for popular libraries, please consider a pull request so it can be added to the package!

## Usage

For simple forms, we'll use the class `anlutro\Form\DefaultForm`. Inject this into your controller...

```php
use anlutro\Form\DefaultForm;
public function __construct(DefaultForm $form)
{
	$this->form = $form;
}
```

Or just construct it, if you have an instance of the `Form\Builder` available.

```php
$this->form = new DefaultForm($formBuilder);
```

In your controller action you can define the behaviour of the form. All of the following are optional.

```php
// The model is where the form gets its data. It can be an existing active-
// record model, entity or an array of dummy data.
$this->form->setModel($myModel);
$this->form->setAction('http://mysite.com/my-route');
$this->form->setMethod('PUT');
```

Pass the form to your view in the controller action that shows the form. In your view - using whatever templating engine you have available:

```php
<?= $form->open(['class' => 'form-horizontal']) ?>
<div class="form-group">
  <?= $form->text('my_field', ['class' => 'form-control']) ?>
</div>
<?= $form->close() ?>
```

In the controller action that handles the POST request of the form:

```php
$input = $this->form->getInput();
$this->service->doStuff($input);
```

If your form has custom behaviour - getters, setters etc., you can extend the `AbstractForm` class and inject your custom class instead of the `DefaultForm`.

```php
use anlutro\Form\AbstractForm;
class MyForm extends AbstractForm {}
```

Check the examples directory for more information.

## Contact

Open an issue on GitHub if you have any problems or suggestions.

## License

The contents of this repository is released under the [MIT license](http://opensource.org/licenses/MIT).
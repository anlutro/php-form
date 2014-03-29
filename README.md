# PHP Form Builder [![Build Status](https://travis-ci.org/anlutro/php-form.png?branch=master)](https://travis-ci.org/anlutro/php-form) [![Latest Version](http://img.shields.io/github/tag/anlutro/php-form.svg)](https://github.com/anlutro/php-form/releases)
Sick of the default Laravel 4 form builder, I wrote my own and made it framework-agnostic. Improvements include:

- Each form is its own instance - no singletons, no shared states
- Form model binding is enforced, you can no longer pass the value of the input in the view
- Form model binding works properly with mixes of objects and arrays as well as relationships (checkboxes and relationships work out of the box)
- Write a class for your more advanced forms, define input types, input transformer and field presenter methods
- If not checked, checkbox values are false instead of not present in the input array
- Arrays of checkbox values come back as an empty array if none are checked
- Various bugfixes and simplification of interface

Intentionally missing features from the Laravel form builder:

- You may no longer pass an 'url' or 'route' attribute to `open()` as there is no URL generation included. The 'action' attribute must be a full URL.
- The 'size' attribute for `textarea()` no longer works. Use 'rows' and 'cols' as you would in normal HTML.

Please open an issue if you find more.

## Installation
`composer require anlutro/php-form`

Pick the latest stable version from packagist or the GitHub tag list.

Add 'anlutro\Form\ServiceProvider' to the list of providers in app/config/app.php.

WARNING: Backwards compatibility is not guaranteed during version 0.x.

## Usage
Note that usage of this library will add extra lines of code to your project and may not be the right tool for every job. The library was designed for two reasons - fix quirks in the laravel form builder, add flexibility and create a class representation of each form that handles transformation of input. If you do not need any of these, you can easily just keep using `Input::all()` and be fine.

Start by creating a class representation of your form.

```php
use anlutro\Form\AbstractForm;
class MyForm extends AbstractForm {}
```

Inject the form object into your controller.

```php
public function __construct(MyForm $form)
{
	$this->form = $form;
}
```

Pass the form to your view in the controller action that shows the form:

```php
$this->form->setModel(MyModel::find($id)); // optional
$this->form->setAction(URL::route('my.route', $id)); // optional
return View::make(['form' => $this->form]);
```

In the controller action that handles the POST request of the form:

```php
// instead of Input::all()...
$input = $this->form->getInput();
$model->fill($input)->save();
```

Your views will largely look the same as before, except your use `$form->` instead of `Form::`, and the `$value` parameter has been removed from most methods.

```php
Before: {{ Form::text('my_field', null, ['class' => 'form-control']) }}
After: {{ $form->text('my_field', ['class' => 'form-control']) }}
```

Check the examples directory for more information.

## Contact
Open an issue on GitHub if you have any problems or suggestions.

## License
The contents of this repository is released under the [MIT license](http://opensource.org/licenses/MIT).
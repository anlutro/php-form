This example shows how to use the SimpleForm class we created in a PHP template.

In your controller, you should construct the Form object like this:

	$form = Form::make('SimpleForm', $model);
	return View::make('form', compact('form'));

Most of the default Laravel FormBuilder methods are available.

<?= $form->open() ?>

<div class="form-group">
	<?= $form->label('date', 'Date', ['class' => 'control-label col-sm-2']) ?>
	<div class="col-sm-10 col-md-8 col-lg-6">
		<span>Here is where the magic comes in. Instead of doing Form::text() or
		whatever, we just call input($field, $attributes). The Form class handles
		the type of the input and the values, you define any HTML attributes.</span>
		<?= $form->input('date', ['class' => 'form-control']) ?>
	</div>
</div>

<?= $form->close() ?>
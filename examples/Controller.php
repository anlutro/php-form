<?php
/**
 * A simple example of a controller that gets a Form and Repository class
 * injected and utilizes them to create a new thing via a form.
 */
class MyController extends BaseController
{
	protected $form;
	protected $repo;

	public function __construct(SimpleForm $form, ThingRepository $repo)
	{
		$this->form = $form;
		$this->repo = $repo;
	}

	public function create()
	{
		// get a new model instance from the repository and give it to the form
		$this->form->setModel($this->repo->getNew());

		return View::make('thing.form', ['form' => $this->form]);
	}

	public function store()
	{
		if (!$this->form->isValid()) {
			$errors = $this->form->getErrors();
			return Redirect::action('MyController@create')
				->withInput()->withErrors($errors);
		}

		$input = $this->form->getInput();

		if ($thing = $this->repo->create($input)) {
			return Redirect::action('MyController@edit', $thing->id)
				->withMessage('Thing successfully created!');
		} else {
			return Redirect::action('MyController@create')
				->withInput()->withErrors($this->repo->getErrors());
		}
	}
}

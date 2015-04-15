<?php
class Toto extends BaseController
{

	public function indexAction()
	{
		$this->title = "TotoController";

		$this->loadLayout();

		$this->render('index');
	}
}
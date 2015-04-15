<?php
class Index extends BaseController
{
	public function indexAction()
	{
		$this->title = 'IndexController::index';

		$this->loadLayout();

		$this->render('index');
	}

	public function testparamsAction($one, $two, $three = null)
	{
		echo 'Parameter "one": '; var_dump($one); echo '<br>';
		echo 'Parameter "two": '; var_dump($two); echo '<br>';
		echo 'Parameter "three": '; var_dump($three); echo '<br>';
		$this->title = 'IndexController::testparams';
		$this->loadLayout();
		$this->render('index');
	}
}
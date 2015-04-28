<?php
class AnalyseController extends BaseController
{
	/**
	 * Show the full XML
	 */
	public function updateAction()
	{
		if ($this->getRequestMethod() != 'POST') {
			$this->exitError(405, 'Only post methods are accepted.');
			return;
		}

		$post = [
			'idAnalyse' => $_POST['idAnalyse'],
			'analyse'   => $_POST['analyse'],
			'type'      => $_POST['type'],
		];

		$analyseModel = new Analyse();
		$analyseModel->updateAnalyse($post);

		$this->sendStatus(204);
	}

	public function deleteAction()
	{
		if (!$this->getRequestMethod() == 'POST') {
			$this->exitError(405);
		}

		if (!isset($_POST['idAnalyse']))
		{
			$this->exitError(400, 'idAnalyse is a required field.');
		}

		$analyseModel = new Analyse();
		$analyseModel->deleteAnalyse($_POST['idAnalyse']);

		$this->sendStatus(204);
	}
}
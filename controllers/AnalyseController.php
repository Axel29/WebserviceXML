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

	/**
	 * Delete an analyse by it's ID
	 *
	 * @param $id int Analyse's ID to delete
	 */
	public function deleteAction($id)
	{
		if (!$this->getRequestMethod() == 'POST') {
			$this->exitError(405);
		}

		$analyseModel = new Analyse();
		$analyseModel->deleteAnalyse((int)$id);

		$this->sendStatus(204);
	}
}
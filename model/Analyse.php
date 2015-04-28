<?php
class Analyse extends BaseModel
{
	/**
	 * Retrieve every available analyses or analyses by test ID
	 *
	 * @param $testId int Test's ID attached to the analyse
	 * @return $analyses array
	 */
	public function getAnalyses($testId = null)
	{
		$this->table = 'analyse a';
		
		$where = [];
		if ($testId) {
			$where = [
				'a.test_idTest' => $testId,
			];
		}

		$analyses = $this->select(['*'], $where);

		return $analyses;
	}

	/**
	 * Delete an analyse by it's ID
	 *
	 * @param $id int Analyse's ID
	 */
	public function deleteAnalyse($id)
	{
		$this->table = 'analyse';
		$this->delete(['idAnalyse' => (int)$id]);
	}


	/**
	 * Update analyse
	 *
	 * @param $gameId int Game ID to retrieve
	 */
	public function updateAnalyse($post)
	{
		if (isset($post['idAnalyse']) && isset($post['analyse']) && isset($post['type'])) {
			$this->table = 'analyse';

			$fields = [
				'analyse' => $post['analyse'],
				'type'    => $post['type'],
			];

			$where = ['idAnalyse' => $post['idAnalyse']];

			$this->update($fields, $where);
		} else {
			return false;
		}
	}
}
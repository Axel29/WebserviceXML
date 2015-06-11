<?php
abstract class CRUD extends BaseController
{
	/**
	 * @var $id int Editor's ID
	 */
	protected $id = null;

	/**
	 * @var $page int Editor's page
	 */
	protected $page = null;

	abstract public function show();
	abstract public function add();
	abstract public function update();
	abstract public function delete();
	abstract public function generateXml($node = []);

	/**
	 * Redirect the request to the matching method regarding the request method
	 * Route: /{controller}/index/id/{id}
	 *
	 * @param $id int ID of the entity. Used for POST, PUT and DELETE methods
	 */
	public function indexAction($id = null, $page = null)
	{
		if ($id) {
			$this->setId($id);
		}

		if ($page) {
			$this->setPage($page);
		}

		switch ($this->getRequestMethod()) {
			case 'GET':
				$this->show();
				break;
			case 'POST':
				$this->add();
				break;
			case 'PUT':
				$this->update();
				break;
			case 'DELETE':
				$this->delete();
				break;
			
			default:
				$this->show();
				break;
		}
	}

	/**
	 * Get prev / next pages urls
	 *
	 * @param string $modelName Model's name associated to the current controller
	 * @return array $pages Pages URLs
	 */
	public function getNextPrevPages($modelName)
	{
		$routing    = new Routing();
		$route      = $routing->parseUri();
		$currentUrl = $route['realUrl'];
		$page       = (isset($route['args']['page']) && $route['args']['page']) ? $route['args']['page'] : 1;

		// Consider that the page is the first one if the parameter is not defined
		if (strpos($currentUrl, 'page') === false) {
			$currentUrl .= '/page/1';
		}

		$prevPage       = $page - 1;
		$nextPage       = $page + 1;

		$model          = new $modelName();
		$prevPageExists = false;
		$nextPageExists = $model->pageExists($nextPage, strtolower($modelName));

		if ($page > 1) {
			$prevPageExists = $model->pageExists($prevPage, strtolower($modelName));
			$urlPrev        = preg_replace('/(page\/\d+)/', 'page/' . $prevPage, $currentUrl);
		} else {
			$urlPrev = '';
		}

		if ($nextPageExists) {
			$urlNext = preg_replace('/(page\/\d+)/', 'page/' . $nextPage, $currentUrl);
		} else {
			$urlNext = '';
		}

		$pages = [
			'prev' => $urlPrev,
			'next' => $urlNext,
		];
		return $pages;
	}

	/**
	 * Validate XML from XSD
	 *
	 * @param SimpleXMLElement $xml XML to validate
	 * @param string $xsd Path to the XSD file
	 * @return $result string Errors to display or empty string
	 */
	public function validateXML($xml, $xsd)
	{
		// Enable user error handling
		libxml_use_internal_errors(true);

		$domDocument = new DOMDocument();
		$domDocument->loadXML($xml);

		$result = '';

		if (!$domDocument->schemaValidate($xsd)) {
			$errors = libxml_get_errors();

			foreach ($errors as $error) {
				$result = "<br>\n";
				switch ($error->level) {
					case LIBXML_ERR_WARNING:
						$result .= "<strong>Warning $error->code</strong>: ";
					break;
					case LIBXML_ERR_ERROR:
						$result .= "<strong>Error $error->code</strong>: ";
					break;
					case LIBXML_ERR_FATAL:
						$result .= "<strong>Fatal Error $error->code</strong>: ";
					break;
				}

				$result .= trim($error->message);

				if ($error->file) {
					$result .= " in <strong>$error->file</strong>";
				}
				$result .= " on line <strong>$error->line</strong>\n";
			}
			libxml_clear_errors();
		}

		return $result;
	}

	/**
	 * Get the ID
	 *
	 * @return int
	 */
	protected function getId()
	{
		return $this->id;
	}

	/**
	 * Set the ID
	 *
	 * @param $id int
	 */
	protected function setId($id)
	{
		if ($this->isInt($id)) {
			$this->id = $id;
		} else {
			$this->exitError(400, sprintf('The ID must be an integer. %s given', gettype($id)));
		}
	}

	/**
	 * Get the current page
	 *
	 * @return int
	 */
	protected function getPage()
	{
		return $this->page;
	}

	/**
	 * Set the current page
	 *
	 * @param int $page Current page
	 */
	protected function setPage($page)
	{
		if ($this->isInt($page)) {
			$this->page = $page;
		} else {
			$this->exitError(400, sprintf('The page must be an integer. %s given', gettype($page)));
		}
	}
}
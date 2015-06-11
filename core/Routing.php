<?php
class Routing
{
	private $defaultController = 'Game';
	private $defaultAction     = 'index';

	/**
	 * Parse URI and return an array 
	 *
	 * @return array $parsedUri Array containing controller action and args
	 */
	public function parseUri()
	{
		$params     = explode('/', $_GET['p']);
		$controller = !empty($params[0]) && $params[0] != 1 	? $params[0] : $this->defaultController;
		$action     = (isset($params[1]) && !empty($params[1])) ? $params[1] : $this->defaultAction;

		// Remove controller and action from parameters to get only params
		unset($params[0]);
		unset($params[1]);

		// Reset array keys in order to associate key / values
		$params = array_values($params);

		// Associate key - values for args
		$realParams = [];
		$args       = [];
		$i          = 0;
		foreach ($params as $param) {
			// Real params
			if (isset($params[$i+1]) && $params[$i+1]) {
				$realParams[] = $param . '/' . $params[$i+1];
			}

			if ($i%2 == 0 && isset($params[$i+1])) {
				$args[$param] = $params[$i+1];
			} else {
				$i++;
				continue;
			}
			$i++;
		}

		$parsedUri = [
			'realUrl'	 => rtrim(BASE_URL . '/' . $controller . '/' . $action . '/' . implode('/', $realParams), '/'),
			'controller' => $controller,
			'action'     => $action,
			'args'       => $args,
		];


		return $parsedUri;
	}

	/**
	 * Call a user function using named instead of positional parameters.
	 * If some of the named parameters are not present in the original function, they
	 * will be silently discarded.
	 * Does no special processing for call-by-ref functions...
	 *
	 * @param string $controller Name of the controller to use
	 * @param string $action Name of method to be called
	 * @param array $params Array containing parameters to be passed to the function using their name (ie array key)
	 */
	function call_user_func_named($controller, $action, $params)
	{
	    // Make sure we do not throw exception if function not found: raise error instead...
	    if (!method_exists($controller, $action)) {
	        echo '404 Page not found';
	        trigger_error('call to unexisting function ' . $action, E_USER_ERROR);
	        return null;
	    }

		$reflect    = new ReflectionMethod($controller, $action);
		$realParams = [];
	    foreach ($reflect->getParameters() as $param)
	    {
	        $pname = $param->getName();
	        if ($param->isPassedByReference()) {
	            // @todo Raise a warning
	        }
	        if (array_key_exists($pname, $params)) {
	            $realParams[] = $params[$pname];
	        } else if ($param->isDefaultValueAvailable()) {
	            $realParams[] = $param->getDefaultValue();
	        } else {
	            // Missing required parameter: mark an error and exit
	            echo '404 Page not found';
	            trigger_error(sprintf('call to %s missing parameter "%s"', $action, $pname), E_USER_ERROR);
	            return NULL;
	        }
	    }
	    return call_user_func_array(array($controller, $action), $realParams);
	}
}

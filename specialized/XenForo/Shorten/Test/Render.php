<?php

class Shorten_Test_Render extends XenForo_ControllerAdmin_Abstract
{
	public function actionIndex()
	{
		XenForo_Application::autoload('Shorten_URL');
		$methods = Shorten_URL::availableMethods();
		$methodDetails = array();
		$url = $this->_input->filterSingle('url', XenForo_Input::STRING);
		if(empty($url)) $url = "http://google.com/";
		foreach($methods as $method)
		{
			$class = 'Shorten_URL_' . $method;
			XenForo_Application::autoload($class);
			$methodDetails[$method] = call_user_func(array($class, 'about'));
		}
		return $this->responseView('XenForo_ViewAdmin_Sutest_Method_List', 'sutest_method_list', array('methods' => $methodDetails,'url' => $url));
	}

	public function actionTest()
	{
		$method = $this->_input->filterSingle('method', XenForo_Input::STRING);
		$url = $this->_input->filterSingle('url', XenForo_Input::STRING);
		if(empty($url) || empty($method)) return $this->responseError('Both Method and URL needs to be supplied!');

			XenForo_Application::autoload('Shorten_URL');
			$Details = array();
			$Details['name'] = $method;
			$Details['url'] = Shorten_URL::shorten($url,$method);
			if(!$Details['url']) $Details['url'] = 'ERROR';
		return $this->responseView('XenForo_ViewAdmin_Sutest_Method_Test', 'sutest_method_test', array('details' => $Details));
	}
}

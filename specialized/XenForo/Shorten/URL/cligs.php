<?php

class Shorten_URL_cligs
{
	public static function shorten($url, $timeout = 10)
	{
			$fullurl = "http://cli.gs/api/v1/cligs/create?url=".$url;
			if(XenForo_Application::get('options')->suMethod_cligs_API != '') $fullurl .= '&key='.XenForo_Application::get('options')->suMethod_cligs_API;
			try
			{
				$client = new Zend_Http_Client($fullurl, array(
					'timeout' => $timeout
				));

				$request = $client->request();

				if ($request->isSuccessful())
				{
					return $request->getBody();
				}
			}
			catch (Zend_Http_Client_Exception $e)
			{
				return false;
			}
		return false;
	}
	
	public static function options()
	{
		$options = array(
			'API' => array(
				'type' => 'textbox', 
				'default' => '', //optional
				'parameters' => '', //optional
				'label' => 'cli.gs API Key (Optional)',
				'description' => 'Enter your cli.gs API key here' //optional
			),
		);
		$phases = array();
		$templates = array();
		return array(
			'options' => $options, 
			'phases' => $phases, 
			'templates', $templates
		);
	}

	public static function about()
	{
		$details = array();
		$details['version'] = '1.0.0';
		$details['versionDecimal'] = '1';
		$details['shortName'] = 'cligs';
		$details['longName'] = 'cli.gs Shortener';
		$details['decription'] = 'Uses cli.gs\'s API to shorten URL\'s more info at http://cli.gs/, cli.gs API key and username optional!';
		$details['available'] = true;
		$details['author'] = 'Cezz';
		$details['installed'] = self::checkInstalled();
		$details['advanced'] = true;
		
		return $details;
	}

	public static function checkInstalled()
	{
		if(isset(XenForo_Application::get('options')->suMethod_cligs_API))
		{
			return true;
		}
		else
		{
			return false;
		}
	}	
}

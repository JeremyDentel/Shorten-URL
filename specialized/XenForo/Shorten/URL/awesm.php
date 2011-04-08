<?php

class Shorten_URL_awesm
{
	public static function shorten($url, $timeout = 10)
	{
		if(Xenforo_Application::get('options')->suMethod_awesm_API != '')
		{
			$post_vars = array(
							"version" => "1",
							"target" => $url,
							"api_key" => Xenforo_Application::get('options')->suMethod_awesm_API
						);
			$fullurl = "http://create.awe.sm/url.json";
				try
				{
					$client = new Zend_Http_Client($fullurl, array(
						'timeout' => $timeout,
						'useragent' => 'XenForo_ShortURL_Awesm_Mod'
					));
					
					$client->setParameterPost($post_vars);
					$client->setMethod(Zend_Http_Client::POST);
					$request = $client->request();
					
					if ($request->isSuccessful())
					{
						$body = json_decode($request->getBody(),true);
						return $body['url']['awesm_url'];
					}
				}
				catch (Zend_Http_Client_Exception $e)
				{
					return false;
				}
		}
		echo 'boo';
		return array('error' => 1, 'text' => 'API Key Or Username are missing');
	}
	
	public static function options()
	{
		$options = array(
			'API' => array(
				'type' => 'textbox', 
				'label' => 'Awe.sm API Key (Required)',
				'description' => 'Enter your Awe.sm API key here'
			)
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
		$details['shortName'] = 'awesm';
		$details['longName'] = 'awe.sm Shortener';
		$details['decription'] = 'Uses awe.sm\'s API to shorten URL\'s more info at http://awe.sm/, this requires a awe.sm API key to work!';
		$details['available'] = self::checkSettings();
		$details['author'] = 'Cezz';
		$details['installed'] = self::checkInstalled();
		$details['advanced'] = true;
		
		return $details;
	}

	public static function checkSettings()
	{
		return (XenForo_Application::get('options')->suMethod_awesm_API != '') ? true : false;
	}

	public static function checkInstalled()
	{
		if(isset(XenForo_Application::get('options')->suMethod_awesm_API))
		{
			return true;
		}
		else
		{
			return false;
		}
	}
}

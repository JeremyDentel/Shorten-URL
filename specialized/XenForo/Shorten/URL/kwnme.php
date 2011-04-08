<?php

class Shorten_URL_kwnme
{
	public static function shorten($url,$timeout = 10)
	{
		$domainOption = XenForo_Application::get('options')->suMethod_kwnme_domain;
		$domain = $domainOption == "" ? "kwn.me" : $domainOption ;
		$fullurl = "http://".$domain."/t.php?process=1&url=".$url;
			try
			{
				$client = new Zend_Http_Client($fullurl, array(
					'timeout' => $timeout,
					'useragent' => 'XenForo_shortURL_kwnme'
				));

				$request = $client->request();

				if ($request->isSuccessful())
				{
					return str_replace('kwn.me', $domain, $request->getBody());
				}
			}
			catch (Zend_Http_Client_Exception $e)
			{
				return FALSE;
			}
	}

	public static function options()
	{
		$options = array(
			'domain' => array(
				'type' => 'textbox', 
				'default' => 'kwn.me', //optional
				'parameters' => '', //optional
				'label' => 'kwn.me Domain.',
				'description' => 'Custom domain for kwn.me engine (optional)' //optional
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
		$details['version'] = '1.0.1';
		$details['versionDecimal'] = '2';
		$details['shortName'] = 'kwnme';
		$details['longName'] = 'kwn.me';
		$details['decription'] = 'Shortens the URL via kwn.me\'s API.';
		$details['available'] = true;
		$details['author'] = 'Cezz';
		$details['installed'] = self::checkInstalled();
		$details['advanced'] = true;
		
		return $details;
	}

	/* checkInstalled() is only required on advanced shorteners
		and verifies the settings are in the options. Should
		be called by about().
	*/	
	public static function checkInstalled()
	{
		if(isset(XenForo_Application::get('options')->suMethod_kwnme_domain))
		{
			return true;
		}
		
		return false;
	}	
}

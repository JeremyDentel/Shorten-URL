<?php

class Shorten_URL_simple
{
	public static function shorten($url, $timeout = 10)
	{
		$bURL = XenForo_Application::get('options')->boardUrl;
		if(!preg_match("#^".$bURL."#",$url)) return $url;
		$newShorterURL = str_replace($bURL.'/', '', $url); 
		$parts = explode('/', $newShorterURL);

		$paramParts = explode(XenForo_Application::URL_ID_DELIMITER, $parts[1]);
		$paramId = end($paramParts);

		$shortURL = $bURL . '/' . $parts[0] . '/' . $paramId . '/';
		if(isset($parts[2])) $shortURL .= $parts[2];

		return $shortURL;
	}
	
	public static function options()
	{
		$options = array();
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
		$details['shortName'] = 'simple';
		$details['longName'] = 'Simple URL';
		$details['decription'] = 'Removes the title from URLs and leaves nothing but an ID. Example: /threads/my-thread.1/ -> /threads/1/';
		$details['available'] = true;
		$details['author'] = 'King Kovifor';
		$details['installed'] = true;
		$details['advanced'] = false;
		
		return $details;
	}
}

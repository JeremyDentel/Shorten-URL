<?php

class Shorten_URL
{		
	/* Usage : install($class); should only really be
	   called by check() to save trying to install non
	   exsistant methods.
	*/
	public static function install($class,$method)
	{		
		$options = call_user_func(array($class, 'options'));
		$optionModel = XenForo_Model::create('XenForo_Model_Option');
		$optionlist = $optionModel->prepareOptions($optionModel->getOptionsInGroup('suOptions'));
		$lastoption = end($optionlist);
		$displayOrder = $lastoption['display_order']+1;
		
		foreach($options['options'] as $option => $param)
		{
			$source = new Xenforo_Input($param);
			$title = $source->filterSingle('label',XenForo_Input::STRING);
			$description = $source->filterSingle('description',XenForo_Input::STRING);
			
			$dwInput = array(
					'option_id' => 'suMethod_'.$method.'_'.$option,
					'edit_format' => $param['type'],
					'data_type' => 'string',
					'can_backup' => (int)1,
					'addon_id' => 'suOptions'
				);
			if(isset($param['default']))
			{
				$dwInput['default_value'] = $param['default'];
			}
			if(isset($param['parameter']))
			{
				$dwInput['edit_format_params'] = $param['parameter'];
			}
			
			$filterdw = new Xenforo_Input($dwInput);
			$dwInput = $filterdw->filter(array(
					'option_id' => XenForo_Input::STRING,
					'default_value' => XenForo_Input::STRING,
					'edit_format' => XenForo_Input::STRING,
					'edit_format_params' => XenForo_Input::STRING,
					'data_type' => XenForo_Input::STRING,
					'sub_options' => XenForo_Input::STRING,
					'can_backup' => XenForo_Input::UINT,
					'validation_class' => XenForo_Input::STRING,
					'validation_method' => XenForo_Input::STRING,
					'addon_id' => XenForo_Input::STRING
				));
			
			$dw = XenForo_DataWriter::create('XenForo_DataWriter_Option');
			$dw->bulkSet($dwInput);
			$dw->setExtraData(XenForo_DataWriter_Option::DATA_TITLE, $title);
			$dw->setExtraData(XenForo_DataWriter_Option::DATA_EXPLAIN, $description);

			$dw->setRelations(array('suOptions' => $displayOrder));

			$dw->save();
		}

		return true;
	}
	
	/* Usage: uninstall($method); removes options for specific method function
		Checks to see if the method file exists.
		Fetches options, and removes them.
	*/
	public static function uninstall($method)
	{
		if(self::check($method) && self::isInstalled($method))
		{
			$class = 'Shorten_URL_' . $method;
			$options = call_user_func(array($class, 'options'));
			foreach($options['options'] AS $option => $params)
			{
				$dw = XenForo_DataWriter::create('XenForo_DataWriter_Option');
				$dw->setExistingData('suMethod_'.$method.'_'.$option);
				$dw->delete();
			}
			return true;
		}
		
		if(!self::isInstalled($method))
		{
			return true;
		}
		
		return false;	
	}

	/* Usage : check($method); checks to see if a corresponding
	   method file exsists and if it does then it trys install
	   incase options haven't yet been added.
	*/
	public static function check($method)
	{
		return file_exists('./library/Shorten/URL/' . $method . '.php');
	}
	
	/* Basic Usage: shorten($url), shortens the url using the default
	   URL shortener as defined in the Shorten_URL settings block.

	   Alternative Usage: shorten($url,$method,[$timeout]), allows you to define a
	   custom method, for example bitly this will then call the shorten
	   class from that methods file eg URL/bitly.php
	*/
	public static function shorten($url,$method = 'default',$timeout = 10)
	{
		if(!XenForo_Application::get('options')->suEnabled)
		{
			return false; //Don't do anything if Disabled.
		}

		$default = (XenForo_Application::get('options')->suDefaultShortener != '' && self::check(XenForo_Application::get('options')->suDefaultShortener)) ? 'Shorten_URL_'.XenForo_Application::get('options')->suDefaultShortener : 'Shorten_URL_none';
		$class = ($method != 'default' && self::check($method)) ? 'Shorten_URL_' . $method : $default;
		
		XenForo_Application::autoload($class);
		$shortURL = call_user_func_array(array($class, 'shorten'), array($url,$timeout));

		if($shortURL == false || is_array($shortURL))
		{
			return false;
		}
		
		return $shortURL;
	}

	/* Usage : installAll(); cycles through all methods
		within the URL/ directory, checks to see if they're
		installed, and runs in the install if not.
	*/
	public static function installAll()
	{
		$methods = self::getMethodList();
		foreach($methods AS $method)
		{
			if(self::isInstalled($method) == false)
			{
				$class = 'Shorten_URL_' . $method;
				XenForo_Application::autoload($class);
				self::install($class, $method);	
			}
		}	
	}
	
	/* Usage: getMethodList(); will return an array
		of all methods found inside of URL/
	*/
	public static function getMethodList() 
	{
		global $configDir;
		
		$results = array();
		$handler = opendir($configDir.'library/Shorten/URL/');
		while ($file = readdir($handler))
		{
			if ($file != "." && $file != "..")
			{
				$results[] = str_replace('.php','',$file);
			}
		}
		closedir($handler);
		return $results;
	}
	
	/* Usage: availableMethods(); will return an array
		of all methods that have the available tag
		set to true.
	*/
	public static function availableMethods()
	{
		$methods = self::getMethodList();
		$available = array();
		foreach($methods AS $method)
		{
			$class = 'Shorten_URL_' . $method;
			XenForo_Application::autoload($class);
			$about = call_user_func(array($class, 'about'));
			if($about['available'])
			{
				$available[] = $method;
			}
		}
		return $available;
	}

	/* Usage: isInstalled(); will return true
		if the method requires install and 
	*/	
	public static function isInstalled($method)
	{
		if(self::check($method))
		{
			$class = 'Shorten_URL_' . $method;
			XenForo_Application::autoload($class);
			$about = call_user_func(array($class, 'about'));
			return $about['installed'];			
		}

		return false;
	}

	public static function getOptions($method)
	{
		$optionModel = XenForo_Model::create('XenForo_Model_Option');
		$optionlist = $optionModel->prepareOptions($optionModel->getOptionsInGroup('suOptions'));
		$methodOptions = array();
			foreach($optionlist as $option) {
				if(preg_match('#^suMethod_'.$method.'#',$option['option_id']))
				{
					$methodOptions[] = $option;
				}
			}
		return $methodOptions;
	}
}

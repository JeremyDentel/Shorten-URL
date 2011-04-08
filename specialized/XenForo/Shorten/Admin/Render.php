<?php

class Shorten_Admin_Render extends XenForo_ControllerAdmin_Abstract
{
	public function actionIndex()
	{
		XenForo_Application::autoload('Shorten_URL');
		$methods = Shorten_URL::getMethodList();
		$methodDetails = array();
		$default = XenForo_Application::get('options')->suDefaultShortener;
		foreach($methods as $method)
		{
			$class = 'Shorten_URL_' . $method;
			XenForo_Application::autoload($class);
			$methodDetails[$method] = call_user_func(array($class, 'about'));
		}
		return $this->responseView('XenForo_ViewAdmin_Su_Method_List', 'su_method_list', array('methods' => $methodDetails, 'default' => $default, 'suEnabled' => XenForo_Application::get('options')->suEnabled));
	}

	public function actionDefault()
	{
		$method = $this->_input->filterSingle('shortName', XenForo_Input::STRING);
		if ($this->isConfirmedPost())
		{
			$dw = XenForo_DataWriter::create('XenForo_DataWriter_Option');
			$dw->setExistingData('suDefaultShortener');
			$dw->set('option_value',$method);
			$dw->save();
			return $this->responseRedirect(XenForo_ControllerResponse_Redirect::SUCCESS, XenForo_Link::buildAdminLink('shortURL/'));
		}
		else // show confirmation dialog
		{
			$viewParams = array(
				'method' => $method
			);
			return $this->responseView('XenForo_ViewAdmin_Su_Method_Default', 'su_method_defalt', $viewParams);		$method = $this->_input->filterSingle('shortName', XenForo_Input::STRING);
		}
	}

	public function actionInstall()
	{
		$method = $this->_input->filterSingle('shortName', XenForo_Input::STRING);
		if ($this->isConfirmedPost())
		{
			$class = 'Shorten_URL_'.$method;
			XenForo_Application::autoload('Shorten_URL');
			if($method == 'all')
			{
				$install = Shorten_URL::installAll();
			}
			else
			{
				$install = Shorten_URL::install($class,$method);
			}
			return $this->responseRedirect(XenForo_ControllerResponse_Redirect::SUCCESS, XenForo_Link::buildAdminLink('shortURL/'));		
		}
		else // show confirmation dialog
		{
			$viewParams = array(
				'method' => $method
			);
			return $this->responseView('XenForo_ViewAdmin_Su_Method_Install', 'su_method_install', $viewParams);
		}
	}

	public function actionUninstall()
	{
		$method = $this->_input->filterSingle('shortName', XenForo_Input::STRING); 
		if ($this->isConfirmedPost())
		{
			if ($method == XenForo_Application::get('options')->suDefaultShortener)
			{
				$viewParams = array();
				return $this->responseView('XenForo_ViewAdmin_Su_Method_Uninstall_Default', 'su_method_uninstall_default', $viewParams);
			}

			XenForo_Application::autoload('Shorten_URL');
			$uninstall = Shorten_URL::uninstall($method);
			if($uninstall != false)
			{
				return $this->responseRedirect(XenForo_ControllerResponse_Redirect::SUCCESS, XenForo_Link::buildAdminLink('shortURL/'));
			}
			//! Todo:: add fail redirect / error.
		}
		else // show confirmation dialog
		{
			$viewParams = array(
					'method' => $method
				);	
			return $this->responseView('XenForo_ViewAdmin_Su_Method_Uninstall', 'su_method_uninstall', $viewParams);
		}
	}

	public function actionDetails()
	{
		$method = $this->_input->filterSingle('shortName', XenForo_Input::STRING);
			$class = 'Shorten_URL_' . $method;
			XenForo_Application::autoload($class);
			$methodDetails = call_user_func(array($class, 'about'));
			return $this->responseView('XenForo_ViewAdmin_Su_Method_Details', 'su_method_details', array('method' => $method, 'page_title' => 'Details about '.$method, 'methods' => $methodDetails));
	}

	public function actionInitinstall()
	{
		XenForo_Application::autoload('Shorten_URL');
		$initinstall = Shorten_URL::installAll();
		return $this->responseRedirect(XenForo_ControllerResponse_Redirect::SUCCESS, XenForo_Link::buildAdminLink('shortURL/'));
	}

	public function actionOptions()
	{
		$method = $this->_input->filterSingle('shortName', XenForo_Input::STRING);
		$options = Shorten_URL::getOptions($method);
		if($options)
		{
			$optionModel = XenForo_Model::create('XenForo_Model_Option');
			$viewParams = array(
				'method' => $method,
				'groups' => $optionModel->prepareOptionGroups($optionModel->getOptionGroupList()),
				'preparedOptions' => $optionModel->prepareOptions($options),
				'canEditGroup' => false,
				'canEditOptionDefinition' => false
			);

			return $this->responseView('XenForo_ViewAdmin_Option_ListOptions', 'su_method_options', $viewParams);
		}
		
		return $this->responseError('This method has no options or is not installed!');
	}
	
	public function actionToggle()
	{
		$enabledDisable = XenForo_Application::get('options')->suEnabled ? 0 : 1;
		$dw = XenForo_DataWriter::create('XenForo_DataWriter_Option');
		$dw->setExistingData('suEnabled');
		$dw->set('option_value', $enabledDisable);
		$dw->save();

		return $this->responseRedirect(XenForo_ControllerResponse_Redirect::SUCCESS, XenForo_Link::buildAdminLink('shortURL/'));
	}
}

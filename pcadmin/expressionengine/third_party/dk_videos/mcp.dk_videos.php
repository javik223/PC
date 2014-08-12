<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * DK Videos
 *
 * @package		DK Videos
 * @version		Version 1.0b1
 * @author		Benjamin David
 * @copyright	Copyright (c) 2012 - DUKT
 * @link		http://dukt.net/dk-videos/
 *
 */

require_once PATH_THIRD.'dk_videos/config.php';

class Dk_videos_mcp {

	/* constructor*/

	function __construct()
	{
		// Make a local reference to the ExpressionEngine super object

		$this->EE =& get_instance();
		
		
		$this->site_id = $this->EE->config->item('site_id');
		
		
		// load dk videos
		
		require_once(DK_VIDEOS_UNIVERSAL_PATH.'app/libraries/dukt_lib.php');
		require_once(DK_VIDEOS_UNIVERSAL_PATH.'cms/expressionengine/libraries/dukt_videos.php');
		
		$this->dukt_lib = new Dukt_lib(array('basepath' => DK_VIDEOS_UNIVERSAL_PATH));
		$this->dukt_lib->load_helper('url');
		
		$this->dukt_lib->lang_load('dk_videos');
		
		$this->dukt_videos = new Dukt_videos;
		
		$api_mode = true;
		
		$this->services = $this->dukt_videos->get_services($api_mode);
	}

	// --------------------------------------------------------------------

	/**
	 * List accounts
	 *
	 * @access	public
	 * @return	string
	 */
	function index()
	{
		// page settings
		
		$this->EE->lang->loadfile('dk_videos');
		
		$this->dukt_videos->insert_css_file('common/css/mcp.css');

		$this->EE->cp->set_variable('cp_page_title', DK_VIDEOS_NAME);
	
	
		// build links
		
		$links = array();
		
		foreach($this->services as $service)
		{
			$links[$service->service_key]['enable'] = $this->dukt_videos->cp_link('method=enable'.AMP.'service='.$service->service_key);
			$links[$service->service_key]['disable'] = $this->dukt_videos->cp_link('method=disable'.AMP.'service='.$service->service_key);
			$links[$service->service_key]['configure'] = $this->dukt_videos->cp_link('method=configure'.AMP.'service='.$service->service_key);
		}
		
		
		// assign variables for the view
		
		$vars['services'] = $this->services;
		$vars['links'] = $links;
		
		return $this->dukt_lib->load_view('account/index', $vars, true);
	}
	
	
	// --------------------------------------------------------------------
	
	public function configure()
	{
		$service_key = $this->dukt_lib->input_get('service');
		
		$service = $this->services[$service_key];
		
		
		// form open
		$form_open = form_open('C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=dk_videos'.AMP.'method=configure_save'.AMP.'service='.$service->service_key);
		$form_close = form_close();
		
		
		// build links
		
		$links = array();
		$links['back'] = $this->dukt_videos->cp_link();
		$links['reset'] = $this->dukt_videos->cp_link('method=reset'.AMP.'service='.$service->service_key);
		
		
		// assign variables for the view
		
		$vars['links'] = $links;
		$vars['services'] = $this->services;
		$vars['service'] = $service;
		$vars['dukt_videos'] = $this->dukt_videos;
		$vars['form_open'] = $form_open;
		$vars['form_close'] = $form_close;
		
		
		// load view

		return $this->dukt_lib->load_view('account/configure', $vars, true);
	}
	
	// --------------------------------------------------------------------
	
	public function configure_save()
	{
		$services = $this->services;
		
		$service_key = $this->dukt_lib->input_get('service');
		
		$service = $this->services[$service_key];
		
		foreach($service->api_options as $k => $v)
		{
			if($this->dukt_lib->input_post($k) !== false)
			{
				
				$this->dukt_videos->set_option($service_key, $k, $this->dukt_lib->input_post($k));
			}
		}
		
		$connect_url = $this->dukt_videos->cp_link('method=connect'.AMP.'service='.$service->service_key);

		$this->EE->functions->redirect($connect_url);
	}
	
	// --------------------------------------------------------------------
	
	public function connect()
	{
		$service_key = $this->dukt_lib->input_get('service');
		
		$service = $this->services[$service_key];
		
		$service->connect($this->dukt_lib, $this->dukt_videos);

	}
	
	// --------------------------------------------------------------------
	
	function reset()
	{
		$services = $this->services;
		
		$service_key = $this->dukt_lib->input_get('service');
		
		$service = $this->services[$service_key];
		
		foreach($service->token_options as $k => $v)
		{		
			$this->dukt_videos->set_option($service_key, $k, '');
		}
		
	    $redirect = $this->dukt_videos->cp_link('method=configure'.AMP.'service='.$service->service_key);
		
		$this->EE->functions->redirect($redirect);
	}
	
	// --------------------------------------------------------------------
	
	function enable()
	{
		$service_key = $this->dukt_lib->input_get('service');
		
		$this->dukt_videos->set_option($service_key, 'enabled', 1);
		
		$redirect = $this->dukt_videos->cp_link();
		
		$this->EE->functions->redirect($redirect);
	}
	
	// --------------------------------------------------------------------
	
	function disable()
	{
		$service_key = $this->dukt_lib->input_get('service');
		
		$this->dukt_videos->set_option($service_key, 'enabled', 0);
		
		$redirect = $this->dukt_videos->cp_link();
		
		$this->EE->functions->redirect($redirect);
	}
	
}

/* END Class */

/* End of file mcp.videoplayer.php */
/* Location: ./system/expressionengine/third_party/videoplayer/mcp.videoplayer.php */
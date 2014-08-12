<?php

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

class Dukt_videos {

	var $version = "1.0";

	// --------------------------------------------------------------------
	
	function __construct()
	{
	
		$this->EE =& get_instance();
		
		require_once(DK_VIDEOS_UNIVERSAL_PATH.'app/libraries/dukt_lib.php');
		
		$this->dukt_lib = new Dukt_lib(array('basepath' => DK_VIDEOS_UNIVERSAL_PATH));
	}
	
	// --------------------------------------------------------------------
	
	public function get_services($api_mode = false)
	{		
		$services = array();
		
		$this->dukt_lib->load_helper('directory');
		$this->dukt_lib->load_library('dukt_video_service');

		$map = directory_map(DK_VIDEOS_UNIVERSAL_PATH.'app/libraries/services/', 1);

		foreach($map as $service_key)
		{
			$service_key = substr($service_key, 0, -4);
			
			$service_class_file = DK_VIDEOS_UNIVERSAL_PATH.'app/libraries/services/'.$service_key.'.php';

			if(file_exists($service_class_file))
			{			
				include_once($service_class_file);
				
				$service_class = "Dukt_video_".$service_key;

				$options = array(
					'client_id' 			=> $this->get_option($service_key, 'client_id'),
					'client_secret' 		=> $this->get_option($service_key, 'client_secret'),
					'api_key' 				=> $this->get_option($service_key, 'api_key'),
					'api_secret' 			=> $this->get_option($service_key, 'api_secret'),
					'developer_key' 		=> $this->get_option($service_key, 'developer_key'),
					
					'token' 				=> $this->get_option($service_key, 'token'),
					'oauth_request_token' 			=> $this->get_option($service_key, 'oauth_request_token'),
					'oauth_request_token_secret' 	=> $this->get_option($service_key, 'oauth_request_token_secret'),
					'access_token' 			=> $this->get_option($service_key, 'access_token'),
					'access_token_secret' 	=> $this->get_option($service_key, 'access_token_secret')
				);

				$service_obj = new $service_class($options);
				
				
				if($api_mode)
				{
					$this->EE->db->where('class', 'Dk_videos');
					$this->EE->db->where('method', 'callback');
					
					$query = $this->EE->db->get('actions');
			
					$act_id = "";
			
					if ($query->num_rows() > 0)
					{
					   $row = $query->row(); 
					
					   $act_id = $row->action_id;
					}
					
					switch($service_key)
					{
						case "youtube":
						
							$admin_redirect = $this->EE->functions->fetch_site_index(0, 0).'?'.'ACT='.$act_id.'&'.'youtube';
							
							$cp_url = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
							
							
							$connect_url = parse_url($cp_url);
							parse_str($connect_url['query'], $query);
							$query['method'] = 'connect';
							$query = http_build_query($query);
							$connect_url = $connect_url['scheme'].'://'.$connect_url['host'].$connect_url['path'].'?'.$query;
							
							$oauth_success_url = parse_url($cp_url);
							parse_str($oauth_success_url['query'], $query);
							$query['method'] = 'configure';
							$query = http_build_query($query);
							$oauth_success_url = $oauth_success_url['scheme'].'://'.$oauth_success_url['host'].$oauth_success_url['path'].'?'.$query;
		
							$service_obj->oauth_redirect_uri =  $admin_redirect;
							$service_obj->connect_url = $connect_url;
							$service_obj->oauth_success_url = $oauth_success_url;
							
							break;
						
						case "vimeo":
	
							
							// admin redirect
							
							$admin_redirect ='http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
							$service_obj->admin_redirect = $admin_redirect;
													
							// referer
							
							$referer = parse_url($admin_redirect);
							
							parse_str($referer['query'], $query);
							
							$query['method'] = 'configure';
							$query = http_build_query($query);
							$referer = $referer['scheme'].'://'.$referer['host'].$referer['path'].'?'.$query;
							
							$service_obj->oauth_success_url = $referer;
	
							
							// redirect
							
							$redirect = $this->EE->functions->fetch_site_index(0, 0).'?'.'ACT='.$act_id.'&'.'vimeo';
							
							$service_obj->oauth_redirect_uri = $redirect;
	
							
							break;
					}
				}

				
				// is enabled ?
				
				$enabled = $this->get_option($service_key, 'enabled');
				
				if($enabled === false)
				{
					$enabled = true;
				}
				else
				{
					if($enabled == "1")
					{
						$enabled = true;
					}
					else
					{
						$enabled = false;
					}
				}
				
				
				$service_obj->enabled = $enabled;
				
				
				// embed options
				
				foreach($service_obj->embed_options as $k => $v)
				{
					$service_obj->embed_options[$k] = $this->get_option($service_key, 'player_'.$k, $v);
				}
				
				
				// add service to services array
				
				$services[$service_key] = $service_obj;	
			}
		}
		

		
		return $services;
	}
	
	// --------------------------------------------------------------------
	
	public function get_video($video_opts, $embed_opts)
	{
		$services = $this->get_services();
		
		foreach($services as $service)
		{
			$video = $service->get_video($video_opts, $embed_opts);
			
			if(isset($video['video_found']))
			{
				return $video;
			}
		}
		
		return false;
	}
	
	// --------------------------------------------------------------------
	
	public function get_option($service, $k, $default=false)
	{
		$this->EE->load->add_package_path(DK_VIDEOS_UNIVERSAL_PATH.'cms/expressionengine');
		
		$this->EE->load->model('dk_videos_model');
		
		$v = $this->EE->dk_videos_model->get_option($service, $k);
		
		$this->EE->load->remove_package_path(DK_VIDEOS_UNIVERSAL_PATH.'cms/expressionengine');
		
		return $v;
	}
	
	// --------------------------------------------------------------------
	
	public function set_option($service, $k, $v)
	{
		$this->EE->load->add_package_path(DK_VIDEOS_UNIVERSAL_PATH.'cms/expressionengine');
		
		$this->EE->load->model('dk_videos_model');
		
		$this->EE->dk_videos_model->set_option($service, $k, $v);
		
		$this->EE->load->remove_package_path(DK_VIDEOS_UNIVERSAL_PATH.'cms/expressionengine');
	}
	
	// --------------------------------------------------------------------

	public function cp_link($more = false)
	{
		$url = BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=dk_videos';
		
		if($more)
		{
			$url .= AMP.$more;
		}
		
		return $url;
	}

	// --------------------------------------------------------------------
	
	public function redirect($url)
	{
    	$this->EE->functions->redirect($url);
	}
	
	// --------------------------------------------------------------------

	/**
	 * Insert JS code
	 *
	 * @access	public
	 * @param	string
	 * @return	void
	 */
	public function insert_js($str)
	{
		$this->EE->cp->add_to_head('<script type="text/javascript">' . $str . '</script>');
	}

	// --------------------------------------------------------------------

	/**
	 * Insert JS file
	 *
	 * @access	public
	 * @param	string
	 * @return	void
	 */
	public function insert_js_file($file)
	{
		$this->EE->cp->add_to_head('<script charset="utf-8" type="text/javascript" src="'.$this->_theme_url().$file.'?'.$this->version.'"></script>');
	}

	// --------------------------------------------------------------------

	/**
	 * Insert CSS file
	 *
	 * @access	public
	 * @param	string
	 * @return	void
	 */
	public function insert_css_file($file)
	{
		$this->EE->cp->add_to_head('<link rel="stylesheet" type="text/css" href="'.$this->_theme_url().$file.'?'.$this->version.'" />');
	}

	// --------------------------------------------------------------------

	/**
	 * Load heading files once (load_head_files)
	 *
	 * @access	private
	 * @return	void
	 */
	public function include_resources()
	{
		$js = "	var Dk_videos = Dk_videos ? Dk_videos : new Object();
				Dk_videos.ajax_endpoint = '".$this->endpoint_url()."';
				Dk_videos.site_id = '".$this->EE->config->item('site_id')."';
			";

		$this->insert_js($js);

		$this->insert_css_file('common/css/box.css');
		$this->insert_css_file('expressionengine/css/box.css');
		$this->insert_css_file('expressionengine/css/field.css');

		$this->insert_js_file('common/js/jquery.easing.1.3.js');
		$this->insert_js_file('common/js/spin.min.js');
		$this->insert_js_file('common/js/box.js');
		
		$this->insert_js_file('expressionengine/js/field.js');
	}

	// --------------------------------------------------------------------
	
	/**
	 * Theme URL
	 *
	 * @access	private
	 * @return	string
	 */
	public function _theme_url()
	{
		$url = $this->EE->config->item('theme_folder_url')."third_party/dk_videos/";
		return $url;
	}
	
	// --------------------------------------------------------------------

	/**
	 * Endpoint base URL for frontend & cp
	 *
	 * @access	public
	 * @return	void
	 */
	function endpoint_url()
	{
		$site_url = $this->EE->functions->fetch_site_index(0, 0);

		if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on')
		{
			$site_url = str_replace('http://', 'https://', $site_url);
		}

		$action_id = $this->fetch_action_id('Dk_videos', 'ajax');

		$url = $site_url.QUERY_MARKER.'ACT='.$action_id;

		return $url;
	}
	
	// --------------------------------------------------------------------

	/**
	 * A copy of the standard fetch_action_id method that was unavailable from here
	 *
	 * @access	private
	 * @return	void
	 */
	private function fetch_action_id($class, $method)
	{
		$this->EE->db->select('action_id');
		$this->EE->db->where('class', $class);
		$this->EE->db->where('method', $method);
		$query = $this->EE->db->get('actions');

		if ($query->num_rows() == 0)
		{
			return FALSE;
		}

		return $query->row('action_id');
	}
}

/* End of file Someclass.php */
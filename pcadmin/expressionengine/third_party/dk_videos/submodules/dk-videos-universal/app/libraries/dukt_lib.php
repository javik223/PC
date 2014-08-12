<?

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

class Dukt_lib {

	var $language = array();
	var $load_view_current_vars = false;

	function __construct($options)
	{
		$this->base_path = $options['basepath'];
		
		$this->lang_load('dukt_video');
	}

	function load_view($view, $vars=false, $return = false, $cms = false)
	{
/*
		if($this->load_view_current_vars)
		{
			if($vars)
			{
				$vars = array_merge($vars, $this->load_view_current_vars);			
			}
			else
			{
				$vars = $this->load_view_current_vars;
			}
		}
*/

		if($vars)
		{			
/* 			$this->load_view_current_vars = $vars; */
			
			foreach($vars as $k => $v)
			{
				${$k} = $v;
			}
		}
		

		
		ob_start();
		
		if(!$cms)
		{
			include($this->base_path.'app/views/'.$view.'.php');
		}
		else
		{
			include($this->base_path.'cms/'.$cms.'/views/'.$view.'.php');
		}
		
		$buffer = ob_get_contents();
		
		@ob_end_clean();

		if($return)
		{
			return $buffer;	
		}
		else
		{
			echo $buffer;
		}
	}
	
	// ------------------------------------------------------------------------------
	
	function load_library($library)
	{	
		include_once($this->base_path.'app/libraries/'.$library.'.php');
		
		$library_object = new $library;
		
		return $library_object;
	}
	
	function load_helper($helper)
	{
		include_once($this->base_path.'app/helpers/'.$helper.'_helper.php');
	}
	
	function lang_load($lang_file)
	{
		$file = $this->base_path.'app/language/english/'.$lang_file.'_lang.php';

		if(file_exists($file))
		{
			include($this->base_path.'app/language/english/'.$lang_file.'_lang.php');
			
			$merged_array = array_merge($this->language, $lang);
			
			$this->language = $merged_array;
		}
	}
	
	function config_load($config)
	{
		include($this->base_path.'config/'.$config.'.php');
	}
	
	public function config_item($item)
	{
		include($this->base_path.'config/dukt_video.php');
		return $config[$item];
	}
	
	// ------------------------------------------------------------------------------
	
	function input_get($key)
	{
		if(isset($_GET[$key]))
		{
			return $_GET[$key];
		}
		
		return false;
	}
	
	// ------------------------------------------------------------------------------
	
	function input_post($key)
	{
		if(isset($_POST[$key]))
		{
			return $_POST[$key];
		}
		
		return false;
	}
	
	// ------------------------------------------------------------------------------
	
	function session_set_userdata($k, $v)
	{
		if(!isset($_SESSION))
		{
			session_start();
		}
		
		$_SESSION[$k] = $v;
		
		return $v;
	}
	
	// ------------------------------------------------------------------------------
	
	function session_userdata($k)
	{
		if(!isset($_SESSION))
		{
			session_start();
		}
		
		if(isset($_SESSION[$k]))
		{
			return $_SESSION[$k];
		}
		
		return NULL;
	}

	// ------------------------------------------------------------------------------

	function session_unset_userdata($k)
	{
		if(!isset($_SESSION))
		{
			session_start();
		}
		
		if(isset($_SESSION[$k]))
		{
			unset($_SESSION[$k]);
		}
	}
	
	// ------------------------------------------------------------------------------	
	
	function lang_line($k)
	{
		if(isset($this->language[$k]))
		{
			return $this->language[$k];
		}
		
		return $k;
	}
}
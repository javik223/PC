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

class Dk_videos_ft extends EE_Fieldtype {

	var $info = array(
		'name'		=> DK_VIDEOS_NAME,
		'version'	=> DK_VIDEOS_VERSION
	);

	var $has_array_data = TRUE;

	/**
	 * Constructor
	 *
	 */
	function __construct()
	{

		parent::EE_Fieldtype();


		// load video player package

		$this->EE->load->add_package_path(PATH_THIRD . 'dk_videos/');


		// load language file

		$this->EE->lang->loadfile('dk_videos');


		// prepare cache for head files

		if (! isset($this->EE->session->cache['dk_videos']['head_files']))
		{
			$this->EE->session->cache['dk_videos']['head_files'] = false;
		}
	}

	// --------------------------------------------------------------------
	
	function load_dk_videos()
	{
		// load dk videos
		
		require_once(DK_VIDEOS_UNIVERSAL_PATH.'app/libraries/dukt_lib.php');
		require_once(DK_VIDEOS_UNIVERSAL_PATH.'cms/expressionengine/libraries/dukt_videos.php');
		
		$this->dukt_lib = new Dukt_lib(array('basepath' => DK_VIDEOS_UNIVERSAL_PATH));
		$this->dukt_lib->load_helper('url');
		
		$this->dukt_videos = new Dukt_videos;
		
		$this->services = $this->dukt_videos->get_services();	
	}
	
	// --------------------------------------------------------------------

	/**
	 * Install Function
	 *
	 * @access	public
	 * @return	array
	 */
	function install()
	{
		return array(
			'video_url'	=> '',
		);
	}

	// --------------------------------------------------------------------

	/**
	 * Display Field
	 *
	 * @access	public
	 * @param	array
	 * @return	array
	 */
	function display_field($data)
	{
		return $this->_display_field($data);
	}

	// --------------------------------------------------------------------

	/**
	 * Matrix Compatibility
	 *
	 * @access	public
	 * @param	array
	 * @return	array
	 */
	function display_cell($data)
	{
		return $this->_display_field($data, 'matrix');
	}

	// --------------------------------------------------------------------

	/**
	 * Common function for native and Matrix display field
	 *
	 * @access	private
	 * @param	array
	 * @param	string
	 * @return	string
	 */
	function _display_field($data, $type=false)
	{
		$this->load_dk_videos();
		
		$vars['services'] = $this->services;
		$vars['manage_link'] = "#";
		
		$return = "";
		
		
		// include resources once
		
		if (!$this->EE->session->cache['dk_videos']['head_files'])
		{
			$this->dukt_videos->include_resources();

			$this->EE->session->cache['dk_videos']['head_files'] = true;
		}
		
		
		// hidden field
		
		if($type == "matrix")
		{
			$vars['hidden_input'] = form_hidden($this->cell_name, $data);
		}
		else
		{
			$vars['hidden_input'] = form_hidden($this->field_name, $data);
		}
		
		
		// account exists
		
		$vars['any_account_exists'] = false;
		
		foreach($vars['services'] as $service)
		{
			if($service->enabled)
			{
				$vars['any_account_exists'] = true;
			}
		}
		
		
		// field view
		
		$return .= $this->dukt_lib->load_view('field/field', $vars, true, 'expressionengine');
		

		return $return;
	}

	// --------------------------------------------------------------------

	/**
	 * {video} - Rendering Tag & Tag Pair
	 *
	 * @access	public
	 * @return	string
	 */
	
	function replace_tag($data, $params = array(), $tagdata = FALSE)
	{
		if(empty($data))
		{
			return "";
		}
		
		$this->load_dk_videos();
		
		$video_opts = array('url' => $data);
		
		$embed_opts = $params;
		
		$video = $this->dukt_videos->get_video($video_opts, $embed_opts);
		
		if($tagdata)
		{
			// rendering {video}{/video} pair

			if($video['video_found'])
			{
				// date format
				
				if(isset($video['date']))
				{
					$local_date = $video['date'];
	
					if (preg_match_all("#".LD."date format=[\"|'](.+?)[\"|']".RD."#", $tagdata, $matches))
					{
						foreach ($matches['1'] as $match)
						{
							$tagdata = preg_replace("#".LD."date format=.+?".RD."#", $this->EE->localize->decode_date($match, $local_date), $tagdata, 1);
						}
					}
				}
			}

			$conditionals = $this->EE->functions->prep_conditionals($tagdata, $video);

			$out = $this->EE->functions->var_swap($conditionals, $video);
		}
		else
		{
			// rendering {video}

			if($video['video_found'])
			{
				$out = $video['embed'];
			}
			else
			{
				$out = $video['error'];
			}
		}

		return $out;
	}
	
	// --------------------------------------------------------------------

	/**
	 * {video:single} Alias for replace_tag()
	 *
	 * @access	public
	 * @return	bool
	 */
	function replace_single($data, $params = array(), $tagdata = FALSE)
	{
		return $this->replace_tag($data, $params, $tagdata);
	}

	// --------------------------------------------------------------------

	/**
	 * {video:details} Alias for replace_tag()
	 *
	 * @access	public
	 * @return	bool
	 */
	function replace_details($data, $params = array(), $tagdata = FALSE)
	{
		return $this->replace_tag($data, $params, $tagdata);
	}

	// --------------------------------------------------------------------

	/**
	 * {video:pair} Alias for replace_tag()
	 *
	 * @access	public
	 * @return	bool
	 */
	function replace_pair($data, $params = array(), $tagdata = FALSE)
	{
		return $this->replace_tag($data, $params, $tagdata);
	}

	// --------------------------------------------------------------------

	/**
	 * Check if video exists
	 *
	 * @access	public
	 * @return	bool
	 */

	function replace_video_exists($data, $params=array(), $tagdata=false)
	{
		// load videoplayer library

		$this->EE->load->library('videoplayer_lib');

		$url = $data;

		return $this->EE->videoplayer_lib->video_exists($url);
	}

	// --------------------------------------------------------------------

	/**
	 * No Video Tag (DEPRECATED)
	 *
	 * @access	public
	 * @return	bool
	 */
	function replace_no_video($data, $params = array(), $tagdata = FALSE)
	{
		return false;
	}

	// --------------------------------------------------------------------

}
// END Videoplayer_ft class

/* End of file ft.videoplayer.php */
/* Location: ./system/expressionengine/third_party/videoplayer/ft.videoplayer.php */
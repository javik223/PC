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

class Dk_videos {

	var $return_data = '';

	/**
	 * Constructor
	 *
	 */
	function Dk_videos()
	{

		$this->EE =& get_instance();
		
		
		// load dk videos
		
		require_once(DK_VIDEOS_UNIVERSAL_PATH.'app/libraries/dukt_lib.php');
		require_once(DK_VIDEOS_UNIVERSAL_PATH.'cms/expressionengine/libraries/dukt_videos.php');
		
		$this->dukt_lib = new Dukt_lib(array('basepath' => DK_VIDEOS_UNIVERSAL_PATH));
		$this->dukt_lib->load_helper('url');
		
		$this->dukt_videos = new Dukt_videos;
		
		$this->services = $this->dukt_videos->get_services();
		
		
		// default method
		
		$this->details();
	}
	
	// --------------------------------------------------------------------	
	
	function details()
	{
		$out = "";

		if(!isset($this->EE->TMPL->tagdata))
		{
			return $this->return_data = "Video error";
		}

		$tagdata = $this->EE->TMPL->tagdata;

		// parameters
		
		$video_opts = array(
			'url' => $this->EE->TMPL->fetch_param('url')
		);
		
		
		$embed_opts = array();
		

		// load embed options for each service
		
		foreach($this->services as $service)
		{
			foreach($service->embed_options as $k => $v)
			{
				switch($k)
				{
					case "width":
					case "height":
						if($this->EE->TMPL->fetch_param($k))
						{
							$embed_opts[$k] = $this->EE->TMPL->fetch_param($k);
						}
						else
						{
							$embed_opts[$k] = $v;
						}
						break;
					
					default:
						if($this->EE->TMPL->fetch_param($k))
						{
							$embed_opts[$service->service_key.'_'.$k] = $this->EE->TMPL->fetch_param($service->service_key.'_'.$k);
						}
						else
						{
							$embed_opts[$service->service_key.'_'.$k] = $v;	
						}
					
				}
			}
		}
		
		$video = $this->dukt_videos->get_video($video_opts, $embed_opts);


		// rendering pair

		if(!$video)
		{
			return $this->return_data = "Video error";
		}


		// no tagdata ? return the embed

		if(!$tagdata)
		{
			return $this->return_data = $video['embed'];
		}


		// parse tagdata

		$local_date = $video['date'];

		if (preg_match_all("#".LD."date format=[\"|'](.+?)[\"|']".RD."#", $tagdata, $matches))
		{
			foreach ($matches['1'] as $match)
			{
				$tagdata = preg_replace("#".LD."date format=.+?".RD."#", $this->EE->localize->decode_date($match, $local_date), $tagdata, 1);
			}
		}

		$conditionals = $this->EE->functions->prep_conditionals($tagdata, $video);

		$out = $this->EE->functions->var_swap($conditionals, $video);

		return $this->return_data = $out;
	}

	// --------------------------------------------------------------------

	/**
	 * Front Endpoint for Ajax Calls
	 *
	 * @access	public
	 * @return	tagdata
	 */
	function ajax()
	{
		require_once(DK_VIDEOS_UNIVERSAL_PATH.'app/libraries/dk_videos_ajax.php');
		require_once(DK_VIDEOS_UNIVERSAL_PATH.'cms/expressionengine/libraries/dk_videos_ajax_expressionengine.php');
		
		$ajax = new Dk_videos_ajax_expressionengine();
		
		$method = $this->EE->input->post('method');
		
		if($method)
		{
			$ajax->{$method}();
		}
	}
	
	// --------------------------------------------------------------------
	
	function callback()
	{
		$dukt_lib = $this->dukt_lib;
		$dukt_videos = $this->dukt_videos;
		
		$services = $dukt_videos->get_services();
		
		foreach($services as $service)
		{
			$service->connect_callback($dukt_lib, $dukt_videos);
		}
	}
	
	
}

/* END Class */

/* End of file mod.videoplayer.php */
/* Location: ./system/expressionengine/third_party/videoplayer/mod.videoplayer.php */
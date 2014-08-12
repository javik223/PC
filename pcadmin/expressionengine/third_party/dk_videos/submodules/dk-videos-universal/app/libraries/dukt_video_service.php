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

class Dukt_video_service {

	var $options = false;

	// --------------------------------------------------------------------

	function __construct($options = false)
	{		
		$this->options = $options;
	}
	
	// --------------------------------------------------------------------
	
	function get_video($video_opts, $embed_opts = array())
	{
		$video = array();


		if(method_exists($this, "get_video_id"))
		{

			$video_id = $this->get_video_id($video_opts['url']);

			$video['service_key'] 			= $this->service_key;
			$video['service_name'] 			= $this->service_name;
			
			if($video_id)
			{
				$video['id'] = $video_id;
				

				// load embeds

				$video = $this->load_embed($video, $video_opts, $embed_opts);


				// load metadata
				
				$video = $this->metadata($video);

				$video['video_found'] = true;
			}
		}

		return $video;
	}
	
	// --------------------------------------------------------------------
	
	function load_embed($video, $video_opts, $embed_opts)
	{
		// video id

		$id = $video['id'];
	

		// specific processing for width & height because they are not needed to build the http query
		
		$width = "";
		$height = "";
		
		if(isset($embed_opts['width']))
		{		
			$width = $embed_opts['width'];
			
			unset($embed_opts['width']);
		}
		
		if(isset($embed_opts['height']))
		{	
			$height = $embed_opts['height'];
			
			unset($embed_opts['height']);
		}
		
		// merge default embed_opts and custom embed_opts

		$embed_opts = array_merge($this->embed_options, $embed_opts);
		
				
		// build embed

		$opts_query = http_build_query($embed_opts);
		

		if(!preg_match('/\?/', $this->universal_url, $matches, PREG_OFFSET_CAPTURE))
		{
			$opts_query = '?'.$opts_query;
		}
		else
		{
			$opts_query = '&'.$opts_query;
		}

		$format = '<iframe src="'.$this->universal_url.$opts_query.'" width="%s" height="%s" frameborder="0" allowfullscreen="true" allowscriptaccess="true"></iframe>';
		
		$embed = sprintf($format, $id, $width, $height);
		
		$video['embed'] = $embed;

		return $video;
	}
	
	// --------------------------------------------------------------------
	
	function throw_exception($e, $class, $function)
	{
		$msg = '';
		
		$msg .= 'file : '.$e->getFile().'<br />';
		$msg .= 'line number : '.$e->getLine().'<br />';
		$msg .= 'class : '.$class.'<br />';
		$msg .= 'method : '.$function.'<br />';
		$msg .= 'error : ';
		$msg .= strip_tags($e->getMessage());
		
		throw new Exception($msg);	
	}
}
?>
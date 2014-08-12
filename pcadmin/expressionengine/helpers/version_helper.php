<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * ExpressionEngine - by EllisLab
 *
 * @package		ExpressionEngine
 * @author		EllisLab Dev Team
 * @copyright	Copyright (c) 2003 - 2013, EllisLab, Inc.
 * @license		http://ellislab.com/expressionengine/user-guide/license.html
 * @link		http://ellislab.com
 * @since		Version 2.0
 * @filesource
 */
 
// ------------------------------------------------------------------------

/**
 * ExpressionEngine Segment Helper
 *
 * @package		ExpressionEngine
 * @subpackage	Helpers
 * @category	Helpers
 * @author		EllisLab Dev Team
 * @link		http://ellislab.com
 */

// --------------------------------------------------------------------

	/**
	 * EE Version Check function
	 * 
	 * Requests a file from ExpressionEngine.com that informs us what the current available version
	 * of ExpressionEngine.
	 *
	 * @access	private
	 * @return	bool|string
	 */
	
	// --------------------------------------------------------------------

	/**
	 * Validate version file
	 * Prototype:
	 *  0 => 
	 *    array
	 *      0 => string '2.1.0' (length=5)
	 *      1 => string '20100805' (length=8)
	 *      2 => string 'normal' (length=6)
	 * 
	 * @access	private
	 * @return	bool
	 */
	function _is_valid_version_file($version_file)
	{
		if ( ! is_array($version_file))
		{
			return FALSE;
		}
		
		foreach ($version_file as $version)
		{
			if ( ! is_array($version) OR count($version) != 3)
			{
				return FALSE;
			}
			
			foreach ($version as $val)
			{
				if ( ! is_string($val))
				{
					return FALSE;
				}
			}
		}
		
		return TRUE;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Check EE Version Cache.
	 *
	 * @access	private
	 * @return	bool|string
	 */
	function _check_version_cache()
	{
		$EE =& get_instance();
		$EE->load->helper('file');
		
		// check cache first
		$cache_expire = 60 * 60 * 24;	// only do this once per day
		$contents = read_file(APPPATH.'cache/ee_version/current_version');

		if ($contents !== FALSE)
		{
			$details = @unserialize($contents);

			if (isset($details['timestamp']) && ($details['timestamp'] + $cache_expire) > $EE->localize->now)
			{
				return $details['data'];
			}
			else
			{
				return FALSE;
			}
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Write EE Version Cache
	 *
	 * @param array - details of version needed to be cached.
	 * @return void
	 */
	function _write_version_cache($details)
	{
		$EE =& get_instance();
		$EE->load->helper('file');
		
		$cache_path = $EE->config->item('cache_path');
		
		if (empty($cache_path))
		{
			$cache_path = APPPATH.'cache/';
		}
		
		$cache_path .= 'ee_version/';
		
		if ( ! is_dir($cache_path))
		{
			mkdir($cache_path, DIR_WRITE_MODE);
			@chmod($cache_path, DIR_WRITE_MODE);	
		}
		
		$data = array(
				'timestamp'	=> $EE->localize->now,
				'data' 		=> $details
			);

		if (write_file($cache_path.'current_version', serialize($data)))
		{
			@chmod($cache_path.'current_version', FILE_WRITE_MODE);			
		}		
	}


/* End of file version_helper.php */
/* Location: ./system/expressionengine/helpers/version_helper.php */

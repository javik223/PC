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

if (! defined('DK_VIDEOS_NAME'))
{
	define('DK_VIDEOS_NAME', 'DK Videos');
	define('DK_VIDEOS_VERSION',  '1.0');
	define('DK_VIDEOS_UNIVERSAL_PATH',  PATH_THIRD.'dk_videos/submodules/dk-videos-universal/');
}

//require_once PATH_THIRD.'videoplayer/config.php';

// NSM Addon Updater
$config['name'] = DK_VIDEOS_NAME;
$config['version'] = DK_VIDEOS_VERSION;

$config['nsm_addon_updater']['versions_xml'] = 'http://dukt.net/addons/dk-videos/release-notes.rss';

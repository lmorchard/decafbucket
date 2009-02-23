<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * @package  Core
 *
 * Sets the default route to "welcome"
 */
$config['_default']   = 'entries/index';
$config['index.rss']  = 'entries/index';
$config['index.atom'] = 'entries/index';

$config['entries/(\d{4})/(\d{2})/(\d{2})'] = 'entries/single/$1/$2/$3';

<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
	File berisi setting sensitif ada di config/sandi.php yang tidak disimpan di git
*/

$config['nothing'] = '';
$config['abaikan'] = '';
$config['dev_token'] = '';
$config['mapbox_token'] = '';
if (file_exists($file_path = FCPATH.'config/sandi.php'))
{
	include($file_path);
}

<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');


$route['depositos/admin/(:num)/(:any)']			= 'admin/load/$1/$2';

$route['depositos/admin/(:num)']			= 'admin/load/$1';
?>
<?php
/*
 * The entire MVC needs a database to work properly
 */
global $wpdb;
$prefix = $wpdb->prefix;
$this->config['database'] = array(
    'hostname' 	=> DB_HOST,
    'username' 	=> DB_USER,
    'password' 	=> DB_PASSWORD,
    'dbname' 	=> DB_NAME,
    'prefix' 	=> $prefix,
);
<?php

if( ! defined('WP_UNINSTALL_PLUGIN') ) exit;

require_once "includes/db.php";

$db = new \com\vital\subscription_bar\DB();
$db->drop_table();
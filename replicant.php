<?php

/**
 * Plugin Name:       Replicant
 * Plugin URI:        https://github.com/evokelektrique/replicant
 * Description:       This plugin replicates posts and content in your wordpress websites
 * Version:           0.2
 * Requires at least: 5.7.0
 * Author:            EVOKE
 * Author URI:        https://github.com/evokelektrique/
 * License:           GPL 3
 * License URI:       https://www.gnu.org/licenses/gpl-3.0.txt
 * Text Domain:       replicant
 * Domain Path:       /languages
 */

// Exit if accessed directly
if(!defined( 'ABSPATH' )) exit;

require_once __DIR__ . "/vendor/autoload.php";

$GLOBALS["replicant"] = \Replicant\Plugin::get_instance();

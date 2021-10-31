<?php

/**
 * Plugin Name:       Replicant
 * Plugin URI:        https://github.com/evokelektrique/replicant
 * Description:       Synchronize your WordPress websites with ease
 * Version:           6.2
 * Requires at least: 7.0
 * Author:            EVOKE
 * Author URI:        https://github.com/evokelektrique/
 * License:           AGPL v3
 * License URI:       https://www.gnu.org/licenses/agpl-3.0.txt
 * Text Domain:       replicant
 * Domain Path:       /languages
 */

// Exit if accessed directly
if(!defined( 'ABSPATH' )) exit;

require_once __DIR__ . "/vendor/autoload.php";

$GLOBALS["replicant"] = \Replicant\Plugin::get_instance();

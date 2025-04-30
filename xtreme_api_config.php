<?php

/**
 * Config.php
 *
 * This script contains the API key, base API URL, and individual
 * dealer code for the Xtreme Scheduler API
 * PHP Version 7.4+
 *
 * @category Xtreme_Scheduler_API
 * @package  Xtreme_Scheduler_API
 * @author   Brandon Baker <brandon@gmail.com>
 * @license  Creative Commons Attribution-NonCommercial 4.0 International
 *             (CC BY-NC 4.0)
 * @link     https://scheduler.xtremecrm.com/
  * Loads Xtreme Scheduler API credentials from .env
 */

require_once __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();        // won’t throw if .env missing

$config = [
    'apiKey'     => $_ENV['XTREME_API_KEY']     ?? '',
    'apiBaseUrl' => $_ENV['XTREME_API_BASE']    ?? '',
    'dealerCode' => $_ENV['XTREME_DEALER_CODE'] ?? '',
];
?>
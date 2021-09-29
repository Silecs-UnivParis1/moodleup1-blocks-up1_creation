<?php
/**
 * @package    block_up1_creation
 * @copyright  2021 Silecs & Université Paris1 Panthéon-Sorbonne
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$plugin->version = 2021061003;
$plugin->requires =  2020060900;
$plugin->component = 'block_up1_creation';

$plugin->dependencies = [
    'local_crswizard' => 2020103100,
];

<?php
/**
 * Plugin Name: LeadFlow Client Enquiry Manager
 * Description: Captures service enquiries and stores them as private WordPress records.
 * Version: 1.0.0
 * License: GPL-2.0-or-later
 * Text Domain: leadflow
 */

if (!defined('ABSPATH')) {
    exit;
}

const LEADFLOW_VERSION = '1.0.0';

require_once __DIR__ . '/includes/class-post-type.php';
require_once __DIR__ . '/includes/class-form.php';

$post_type = new LeadFlow\Post_Type();
$form      = new LeadFlow\Form();

$post_type->register_hooks();
$form->register_hooks();
<?php

/**
 * @package    mod_userlist_admin
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

require_once __DIR__ . '/helper.php';

$app    = Factory::getApplication();
$input  = $app->getInput();

// Module params (with defaults)
$showUsername = (bool) $params->get('show_username', 1);
$showEmail    = (bool) $params->get('show_email', 1);
$limit        = (int)  $params->get('limit', 20);
$orderBy      = $params->get('order_by', 'id');
$orderDir     = strtoupper($params->get('order_dir', 'ASC')) === 'DESC' ? 'DESC' : 'ASC';

// Permission check: only show list if user can manage com_users
$user = Factory::getUser();
$canView = $user->authorise('core.manage', 'com_users');

$errors = [];

if (!$canView) {
    // Not authorized to see the user list
    $errors[] = Text::_('MOD_USERLIST_ADMIN_ERR_NO_PERMISSION');
    $users = [];
} else {
    // Handle CSV download if requested
    if ($input->getBool('download_csv', false)) {
        $users = ModUserlistAdminHelper::getUsers($limit, $orderBy, $orderDir);
        ModUserlistAdminHelper::exportCsv($users, $showUsername, $showEmail);
        // exportCsv will exit after sending headers
    }

    $users = ModUserlistAdminHelper::getUsers($limit, $orderBy, $orderDir);
}

// Render layout
require JModuleHelper::getLayoutPath('mod_userlist_admin', $params->get('layout', 'default'));

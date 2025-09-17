<?php
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Language\Text;

require_once __DIR__ . '/helper.php';

$app    = Factory::getApplication();
$input  = $app->getInput();

// Handle export
if ($input->getMethod() === 'POST' && $input->getInt('mod_userlist_admin_export', 0) && Session::checkToken('post')) {
    $user = Factory::getUser();
    if (!$user->authorise('core.manage', 'com_users')) {
        header($_SERVER['SERVER_PROTOCOL'] . ' 403 Forbidden');
        echo Text::_('MOD_USERLIST_ADMIN_ERR_NO_PERMISSION');
        exit; // stop everything
    }

    require_once __DIR__ . '/helper.php';

    $showUsername = (int) $input->getInt('show_username', 1);
    $showEmail    = (int) $input->getInt('show_email', 1);
    $limit        = (int) $input->getInt('limit', 20);
    $orderBy      = $input->getCmd('order_by', 'id');
    $orderDir     = strtoupper($input->getCmd('order_dir', 'ASC')) === 'DESC' ? 'DESC' : 'ASC';

    $users = ModUserlistAdminHelper::getUsers($limit, $orderBy, $orderDir);

    // Clean buffer to avoid leftover HTML
    while (ob_get_level()) {
        ob_end_clean();
    }

    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="userlist-' . date('Ymd-His') . '.csv"');
    header('Cache-Control: no-store, no-cache, must-revalidate');
    header('Pragma: no-cache');

    $out = fopen('php://output', 'w');

    $hdr = ['ID', 'Name'];
    if ($showUsername) $hdr[] = 'Username';
    if ($showEmail)    $hdr[] = 'Email';
    $hdr[] = 'Registered Date';
    fputcsv($out, $hdr);

    foreach ($users as $u) {
        $row = [(int) $u->id, (string) $u->name];
        if ($showUsername) $row[] = (string) $u->username;
        if ($showEmail)    $row[] = (string) $u->email;
        $row[] = isset($u->registerDate) ? $u->registerDate : '';
        fputcsv($out, $row);
    }

    fclose($out);
    exit;
}

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
    $users = ModUserlistAdminHelper::getUsers($limit, $orderBy, $orderDir);
}

// Render layout
require JModuleHelper::getLayoutPath('mod_userlist_admin', $params->get('layout', 'default'));

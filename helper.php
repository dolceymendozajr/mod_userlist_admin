<?php

/**
 * @package    mod_userlist_admin
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Language\Text;

class ModUserlistAdminHelper
{
    /**
     * Get users
     *
     * @param int    $limit
     * @param string $orderBy
     * @param string $orderDir
     *
     * @return array
     */
    public static function getUsers($limit = 20, $orderBy = 'id', $orderDir = 'ASC')
    {
        $db = Factory::getDbo();
        $query = $db->getQuery(true);

        // Accept only certain order columns to avoid injection
        $allowed = [
            'id'           => 'u.id',
            'name'         => 'u.name',
            'username'     => 'u.username',
            'email'        => 'u.email',
            'registerDate' => 'u.registerDate'
        ];

        $orderBy = isset($allowed[$orderBy]) ? $allowed[$orderBy] : $allowed['id'];
        $orderDir = strtoupper($orderDir) === 'DESC' ? 'DESC' : 'ASC';

        $query->select($db->quoteName(['u.id', 'u.name', 'u.username', 'u.email', 'u.registerDate']))
            ->from($db->quoteName('#__users', 'u'))
            ->order($orderBy . ' ' . $orderDir);

        $db->setQuery($query, 0, (int) $limit);

        try {
            $results = $db->loadObjectList();
        } catch (RuntimeException $e) {
            // Return empty list on DB error
            $results = [];
        }

        return $results;
    }

    /**
     * Export CSV - outputs headers and exits.
     *
     * @param array $users
     * @param bool  $showUsername
     * @param bool  $showEmail
     *
     * @return void
     */
    public static function exportCsv($users, $showUsername = true, $showEmail = true)
    {
        $filename = 'userlist-' . date('Ymd-His') . '.csv';

        // CSV headers
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $out = fopen('php://output', 'w');

        $header = ['ID', 'Name'];
        if ($showUsername) $header[] = 'Username';
        if ($showEmail)    $header[] = 'Email';
        $header[] = 'Registered Date';

        fputcsv($out, $header);

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
}

<?php

/**
 * @package    mod_userlist_admin
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;

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
}

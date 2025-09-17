<?php

/**
 * @package    mod_userlist_admin
 */

defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;

$escape = function ($s) {
    return htmlspecialchars((string) $s, ENT_QUOTES, 'UTF-8');
};

// Module params are available via $params if loaded. If not, we can access via ModuleHelper in entry.
// But we will fetch local variables from the scope (the entry included this layout).
// Expected variables in scope: $users, $showUsername, $showEmail, $limit, $orderBy, $orderDir, $errors

?>
<div class="mod-userlist-admin">
    <?php if (!empty($errors)) : ?>
        <div class="alert alert-danger">
            <?php foreach ($errors as $err) : ?>
                <p><?php echo $escape($err); ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <?php if (empty($users)) : ?>
        <p><?php echo $escape(Text::_('MOD_USERLIST_ADMIN_NO_USERS')); ?></p>
    <?php else: ?>

        <?php if (!$showUsername && !$showEmail): ?>
            <div class="alert alert-warning">
                <?php echo $escape(Text::_('MOD_USERLIST_ADMIN_NO_COLUMNS')); ?>
            </div>
        <?php else: ?>

            <div style="margin-bottom: 0.5rem;">
                <form method="get" style="display:inline;">
                    <input type="hidden" name="download_csv" value="1" />
                    <button type="submit" class="btn btn-primary btn-sm">
                        <?php echo $escape(Text::_('MOD_USERLIST_ADMIN_DOWNLOAD_CSV')); ?>
                    </button>
                </form>
            </div>

            <table class="table table-striped table-condensed" style="width:100%; border-collapse:collapse;">
                <thead>
                    <tr>
                        <th><?php echo $escape(Text::_('MOD_USERLIST_ADMIN_COL_ID')); ?></th>
                        <th><?php echo $escape(Text::_('MOD_USERLIST_ADMIN_COL_NAME')); ?></th>
                        <?php if ($showUsername): ?>
                            <th><?php echo $escape(Text::_('MOD_USERLIST_ADMIN_COL_USERNAME')); ?></th>
                        <?php endif; ?>
                        <?php if ($showEmail): ?>
                            <th><?php echo $escape(Text::_('MOD_USERLIST_ADMIN_COL_EMAIL')); ?></th>
                        <?php endif; ?>
                        <th><?php echo $escape(Text::_('MOD_USERLIST_ADMIN_COL_REGISTERED')); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $u) : ?>
                        <tr>
                            <td><?php echo $escape($u->id); ?></td>
                            <td><?php echo $escape($u->name); ?></td>
                            <?php if ($showUsername): ?>
                                <td><?php echo $escape($u->username); ?></td>
                            <?php endif; ?>
                            <?php if ($showEmail): ?>
                                <td><?php echo $escape($u->email); ?></td>
                            <?php endif; ?>
                            <td><?php echo $escape(isset($u->registerDate) && $u->registerDate ? $u->registerDate : '-'); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

        <?php endif; ?>
    <?php endif; ?>
</div>
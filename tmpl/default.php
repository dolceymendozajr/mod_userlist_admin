<?php

/**
 * @package    mod_userlist_admin
 */

defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;

$escape = function ($s) {
    return htmlspecialchars((string) $s, ENT_QUOTES, 'UTF-8');
};

// capture current values so they can be posted back for export
$curShowUsername = (int) $showUsername;
$curShowEmail = (int) $showEmail;
$curLimit = (int) $limit;
$curOrderBy = isset($orderBy) ? $orderBy : 'id';
$curOrderDir = isset($orderDir) ? $orderDir : 'ASC';

// current full URL (will post to same page)
$action = Uri::getInstance()->toString();
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

            <!-- Export form (POSTs to the CURRENT page) -->
            <form method="post" action="<?php echo $escape($action); ?>" style="display:inline;">
                <?php echo \Joomla\CMS\HTML\Helpers\Form::token(); ?>
                <input type="hidden" name="mod_userlist_admin_export" value="1" />
                <input type="hidden" name="show_username" value="<?php echo $curShowUsername; ?>" />
                <input type="hidden" name="show_email" value="<?php echo $curShowEmail; ?>" />
                <input type="hidden" name="limit" value="<?php echo $curLimit; ?>" />
                <input type="hidden" name="order_by" value="<?php echo $escape($curOrderBy); ?>" />
                <input type="hidden" name="order_dir" value="<?php echo $escape($curOrderDir); ?>" />
                <button type="submit" class="btn btn-primary btn-sm">
                    <?php echo $escape(Text::_('MOD_USERLIST_ADMIN_DOWNLOAD_CSV')); ?>
                </button>
            </form>

            <table class="table table-striped table-condensed" style="width:100%; border-collapse:collapse; margin-top:.5rem;">
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
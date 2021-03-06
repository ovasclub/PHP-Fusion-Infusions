<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright (C) PHP-Fusion Inc
| https://www.php-fusion.co.uk/
+--------------------------------------------------------+
| Filename: team/infusion_db.php
| Author: RobiNN
+--------------------------------------------------------+
| This program is released as free software under the
| Affero GPL license. You can redistribute it and/or
| modify it under the terms of this license which you
| can read by viewing the included agpl.txt or online
| at www.gnu.org/licenses/agpl.html. Removal of this
| copyright header is strictly prohibited without
| written permission from the original author(s).
+--------------------------------------------------------*/
if (!defined('IN_FUSION')) {
    die('Access Denied');
}

if (!defined('TEAM_LOCALE')) {
    if (file_exists(INFUSIONS.'team/locale/'.LOCALESET.'team.php')) {
        define('TEAM_LOCALE', INFUSIONS.'team/locale/'.LOCALESET.'team.php');
    } else {
        define('TEAM_LOCALE', INFUSIONS.'team/locale/English/team.php');
    }
}

if (!defined('DB_TEAM')) {
    define('DB_TEAM', DB_PREFIX.'team');
}

\PHPFusion\Admins::getInstance()->setAdminPageIcons('TEAM', '<i class="admin-ico fa fa-fw fa-users"></i>');

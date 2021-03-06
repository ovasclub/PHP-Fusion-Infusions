<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright (C) PHP-Fusion Inc
| https://www.php-fusion.co.uk/
+--------------------------------------------------------+
| Filename: team/admin.php
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
require_once '../../maincore.php';
require_once THEMES.'templates/admin_header.php';

pageAccess('TEAM');

use \PHPFusion\BreadCrumbs;

$locale = fusion_get_locale('', TEAM_LOCALE);

add_to_title($locale['TEAM_title_admin']);

BreadCrumbs::getInstance()->addBreadCrumb(['link' => INFUSIONS.'team/admin.php'.fusion_get_aidlink(), 'title' => $locale['TEAM_title_admin']]);

opentable($locale['TEAM_title_admin']);

$data = [
    'team_id'    => 0,
    'userid'     => 0,
    'position'   => '',
    'profession' => ''
];

$edit = (isset($_GET['action']) && $_GET['action'] == 'edit') && isset($_GET['team_id']) ? TRUE : FALSE;
$allowed_section = ['list', 'form'];
$_GET['section'] = isset($_GET['section']) && in_array($_GET['section'], $allowed_section) ? $_GET['section'] : 'list';

if (isset($_GET['section']) && $_GET['section'] == 'form') {
    $tab['title'][] = $locale['back'];
    $tab['id'][]    = 'back';
    $tab['icon'][]  = 'fa fa-fw fa-arrow-left';
}

$tab['title'][] = $locale['TEAM_title'];
$tab['id'][]    = 'list';
$tab['icon'][]  = 'fa fa-fw fa-users';

$tab['title'][] = $edit ? $locale['edit'] : $locale['add'];
$tab['id'][]    = 'form';
$tab['icon'][]  = 'fa fa-'.($edit ? 'pencil' : 'plus');

$result = dbquery("SELECT * FROM ".DB_TEAM." WHERE team_id='".(isset($_GET['team_id']) ? $_GET['team_id'] : '')."'");

if (isset($_GET['section']) && $_GET['section'] == 'back') redirect(FUSION_SELF.fusion_get_aidlink());

if ((isset($_GET['action']) && $_GET['action'] == 'delete') && (isset($_GET['team_id']) && isnum($_GET['team_id']))) {
    if (dbrows($result)) dbquery("DELETE FROM ".DB_TEAM." WHERE team_id='".intval($_GET['team_id'])."'");
    addNotice('success', $locale['TEAM_011']);
    redirect(FUSION_SELF.fusion_get_aidlink());
}

echo opentab($tab, $_GET['section'], 'teamadmin', TRUE, 'nav-tabs m-b-20');
switch ($_GET['section']) {
    case 'form':
        if ($edit) {
            BreadCrumbs::getInstance()->addBreadCrumb(['link' => FUSION_REQUEST, 'title' => $locale['edit']]);
        } else {
            BreadCrumbs::getInstance()->addBreadCrumb(['link' => FUSION_REQUEST, 'title' => $locale['add']]);
        }

        if (isset($_POST['save'])) {
            $data = [
                'team_id'    => form_sanitizer($_POST['team_id'], 0, 'team_id'),
                'userid'     => form_sanitizer($_POST['userid'], '', 'userid'),
                'position'   => form_sanitizer($_POST['position'], '', 'position'),
                'profession' => form_sanitizer($_POST['profession'], '', 'profession')
            ];

            if (dbcount('(team_id)', DB_TEAM, "team_id='".$data['team_id']."'")) {
                dbquery_insert(DB_TEAM, $data, 'update');
                if (\defender::safe()) {
                    addNotice('success', $locale['TEAM_010']);
                    redirect(FUSION_SELF.fusion_get_aidlink());
                }
            } else {
                dbquery_insert(DB_TEAM, $data, 'save');
                if (\defender::safe()) {
                    addNotice('success', $locale['TEAM_009']);
                    redirect(FUSION_SELF.fusion_get_aidlink());
                }
            }
        }

        if ((isset($_GET['action']) && $_GET['action'] == 'edit') && (isset($_GET['team_id']) && isnum($_GET['team_id']))) {
            if (dbrows($result)) {
                $data = dbarray($result);
            } else {
                redirect(FUSION_SELF.fusion_get_aidlink());
            }
        }

        echo openform('teamform', 'post', FUSION_REQUEST);
        echo form_hidden('team_id', '', $data['team_id']);
        echo form_user_select('userid', $locale['TEAM_008'], $data['userid'], ['inline' => TRUE, 'allow_self' => TRUE]);
        echo form_text('position', $locale['TEAM_002'], $data['position'], ['inline' => TRUE]);
        echo form_text('profession', $locale['TEAM_003'], $data['profession'], ['inline' => TRUE]);
        echo form_button('save', $locale['save'], 'save', ['class' => 'btn-success']);
        echo closeform();
        break;
    default:
        $result = dbquery("SELECT t.*, u.user_id, u.user_name, u.user_status, u.user_avatar, u.user_level, u.user_joined
            FROM ".DB_TEAM." t
            LEFT JOIN ".DB_USERS." u ON t.userid=u.user_id
        ");

        echo '<div class="table-responsive"><table class="table table-striped table-bordered">';
            echo '<thead><tr>';
                echo '<td>'.$locale['TEAM_001'].'</td>';
                echo '<td>'.$locale['TEAM_002'].'</td>';
                echo '<td>'.$locale['TEAM_003'].'</td>';
                echo '<td>'.$locale['TEAM_004'].'</td>';
                echo '<td>'.$locale['TEAM_006'].'</td>';
            echo '</tr></thead>';

            if (dbrows($result)) {
                while ($data = dbarray($result)) {
                    echo '<tr>';
                        echo '<td>';
                            echo display_avatar($data, '20px', '', false, 'img-circle m-r-5');
                            echo profile_link($data['user_id'], $data['user_name'], $data['user_status']);
                        echo '</td>';
                        echo '<td>'.$data['position'].'</td>';
                        echo '<td>'.$data['profession'].'</td>';
                        echo '<td>'.showdate('shortdate', $data['user_joined']).'</td>';
                        echo '<td>';
                            echo '<a href="'.FUSION_SELF.fusion_get_aidlink().'&amp;section=form&amp;action=edit&amp;team_id='.$data['team_id'].'">'.$locale['edit'].'</a> | ';
                            echo '<a href="'.FUSION_SELF.fusion_get_aidlink().'&amp;action=delete&amp;team_id='.$data['team_id'].'">'.$locale['delete'].'</a>';
                        echo '</td>';
                    echo '</tr>';
                }
            } else {
                echo '<tr><td colspan="6" class="text-center">'.$locale['TEAM_007'].'</td></tr>';
            }
        echo '</table></div>';
        break;
}
echo closetab();

closetable();

require_once THEMES.'templates/footer.php';

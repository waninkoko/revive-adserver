<?php

/*
+---------------------------------------------------------------------------+
| Openads v${RELEASE_MAJOR_MINOR}                                           |
| ============                                                              |
|                                                                           |
| Copyright (c) 2003-2007 Openads Limited                                   |
| For contact details, see: http://www.openads.org/                         |
|                                                                           |
| This program is free software; you can redistribute it and/or modify      |
| it under the terms of the GNU General Public License as published by      |
| the Free Software Foundation; either version 2 of the License, or         |
| (at your option) any later version.                                       |
|                                                                           |
| This program is distributed in the hope that it will be useful,           |
| but WITHOUT ANY WARRANTY; without even the implied warranty of            |
| MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the             |
| GNU General Public License for more details.                              |
|                                                                           |
| You should have received a copy of the GNU General Public License         |
| along with this program; if not, write to the Free Software               |
| Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA |
+---------------------------------------------------------------------------+
$Id: settings-stats.php 12637 2007-11-20 19:02:36Z miguel.correa@openads.org $
*/

/**
 * @todo add warn_limit and warn_limit_days for agency and advertiser,
 *       now is only in the interface
 */

// Require the initialisation file
require_once '../../init.php';

// Required files
require_once MAX_PATH . '/lib/OA/Admin/Preferences.php';
require_once MAX_PATH . '/lib/max/Admin/Redirect.php';
require_once MAX_PATH . '/lib/OA/OperationInterval.php';
require_once MAX_PATH . '/lib/OA/Admin/Option.php';

$oOptions = new OA_Admin_Option('preferences');

// Security check
phpAds_checkAccess(phpAds_Admin);

$aErrormessage = array();
if (isset($_POST['submitok']) && $_POST['submitok'] == 'true') {

    // Register input variables
    phpAds_registerGlobal('warn_admin', 'warn_client', 'warn_agency', 'warn_limit',
                          'warn_limit_days');

    // Set up the preferences object
    $oPreferences = new OA_Admin_Preferences();
    $oPreferences->setPrefChange('warn_admin',  isset($warn_admin));
    $oPreferences->setPrefChange('warn_client', isset($warn_client));
    $oPreferences->setPrefChange('warn_agency', isset($warn_agency));
    if (isset($warn_limit)) {
        if ((!is_numeric($warn_limit)) || ($warn_limit <= 0)) {
            $aErrormessage[4][] = $strWarnLimitErr;
        } else {
            $oPreferences->setPrefChange('warn_limit', $warn_limit);
        }
    }
    if (isset($warn_limit_days)) {
        if ((!is_numeric($warn_limit_days)) || ($warn_limit_days <= 0)) {
            $aErrormessage[4][] = $strWarnLimitDaysErr;
        } else {
            $oPreferences->setPrefChange('warn_limit_days', $warn_limit_days);
        }
    }

    if (!count($aErrormessage)) {
        if (!$oPreferences->writePrefChange()) {
            // Unable to update the preferences
            $aErrormessage[0][] = $strUnableToWritePrefs;
        } else {
            MAX_Admin_Redirect::redirect('account-preferences-language-timezone.php');
        }
    }
}

phpAds_PageHeader("5.1");
phpAds_ShowSections(array("5.1", "5.2", "5.4", "5.5", "5.3", "5.6", "5.7"));

$oOptions->selection("campaing-email-reports");

// Change ignore_hosts into a string, so the function handles it good
$conf['ignoreHosts'] = join("\n", $conf['ignoreHosts']);

$aSettings = array (
    array (
        'text'  => $strAdminEmailWarnings,
        'items' => array (
            array (
                'type'    => 'checkbox',
                'name'    => 'warn_admin',
                'text'    => $strWarnAdmin
            ),
            array (
                'type'    => 'break'
            ),
            array (
                'type'    => 'text',
                'name'    => 'warn_limit',
                'text'    => $strWarnLimit,
                'size'    => 12,
                'depends' => 'warn_client==true || warn_admin==true || warn_agency==true',
                'req'     => true,
                'check'   => 'number+'
            ),
            array (
                'type'    => 'break'
            ),
            array (
                'type'    => 'text',
                'name'    => 'warn_limit_days',
                'text'    => $strWarnLimitDays,
                'size'    => 12,
                'depends' => 'warn_client==true || warn_admin==true || warn_agency==true',
                'req'     => true,
                'check'   => 'number+'
            ),
            array (
                'type'    => 'break'
            )
        )
     ),
     array (
        'text'  => $strAgencyEmailWarnings,
        'items' => array (
            array (
                'type'    => 'checkbox',
                'name'    => 'warn_agency',
                'text'    => $strWarnAgency
            ),
            array (
                'type'    => 'break'
            ),
            array (
                'type'    => 'text',
                'name'    => 'warn_limit',
                'text'    => $strWarnLimit,
                'size'    => 12,
                'depends' => 'warn_client==true || warn_admin==true || warn_agency==true',
                'req'     => true,
                'check'   => 'number+'
            ),
            array (
                'type'    => 'break'
            ),
            array (
                'type'    => 'text',
                'name'    => 'warn_limit_days',
                'text'    => $strWarnLimitDays,
                'size'    => 12,
                'depends' => 'warn_client==true || warn_admin==true || warn_agency==true',
                'req'     => true,
                'check'   => 'number+'
            ),
            array (
                'type'    => 'break'
            )
        )
     ),
     array (
        'text'  => $strAdveEmailWarnings,
        'items' => array (
            array (
                'type'    => 'checkbox',
                'name'    => 'warn_client',
                'text'    => $strWarnClient
            ),
            array (
                'type'    => 'break'
            ),
            array (
                'type'    => 'text',
                'name'    => 'warn_limit',
                'text'    => $strWarnLimit,
                'size'    => 12,
                'depends' => 'warn_client==true || warn_admin==true || warn_agency==true',
                'req'     => true,
                'check'   => 'number+'
            ),
            array (
                'type'    => 'break'
            ),
            array (
                'type'    => 'text',
                'name'    => 'warn_limit_days',
                'text'    => $strWarnLimitDays,
                'size'    => 12,
                'depends' => 'warn_client==true || warn_admin==true || warn_agency==true',
                'req'     => true,
                'check'   => 'number+'
            )
        )
    )
);

$oOptions->show($aSettings, $aErrormessage);
phpAds_PageFooter();

?>

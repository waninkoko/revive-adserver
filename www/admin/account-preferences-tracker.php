<?php

/*
+---------------------------------------------------------------------------+
| Openads v${RELEASE_MAJOR_MINOR}                                                              |
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
$Id: account-settings-interface.php 12825 2007-11-26 18:04:41Z miguel.correa@openads.org $
*/

// Require the initialisation file
require_once '../../init.php';

// Required files
require_once MAX_PATH . '/lib/OA/Admin/Preferences.php';
require_once MAX_PATH . '/lib/max/Admin/Redirect.php';
require_once MAX_PATH . '/lib/OA/Admin/Option.php';

$oOptions = new OA_Admin_Option('preferences');

// Security check
phpAds_checkAccess(phpAds_Admin + phpAds_Agency);

$aErrormessage = array();
if (isset($_POST['submitok']) && $_POST['submitok'] == 'true') {
    // Register input variables
    phpAds_registerGlobal('default_tracker_status', 'default_tracker_type',
                          'default_tracker_linkcampaigns');

    // Set up the preferences object
    $oPreferences = new OA_Admin_Preferences();
    if (isset($default_tracker_status)) {
        $oPreferences->setPrefChange('default_tracker_status', $default_tracker_status);
    }
    if (isset($default_tracker_type)) {
        $oPreferences->setPrefChange('default_tracker_type', $default_tracker_type);
    }

    $oPreferences->setPrefChange('default_tracker_linkcampaigns', isset($default_tracker_linkcampaigns));

    if (!count($aErrormessage)) {
        if (!$oPreferences->writePrefChange()) {
            // Unable to update the preferences
            $aErrormessage[0][] = $strUnableToWritePrefs;
        } else {
            MAX_Admin_Redirect::redirect('account-preferences-user-interface.php');
        }
    }

}

phpAds_PageHeader("5.1");
phpAds_ShowSections(array("5.1", "5.2", "5.4", "5.5", "5.3", "5.6", "5.7"));

$oOptions->selection("tracker");

$aStatuses = array();
foreach($GLOBALS['_MAX']['STATUSES'] as $statusId => $statusName) {
    $aStatuses[$statusId] = $GLOBALS[$statusName];
}

$aTrackerTypes = array();
foreach($GLOBALS['_MAX']['CONN_TYPES'] as $typeId => $typeName) {
    $aTrackerTypes[$typeId] = $GLOBALS[$typeName];
}

$aSettings = array (
    array (
        'text'  => $strTracker,
        'items' => array (
            array (
                'type'    => 'select',
                'name'    => 'default_tracker_status',
                'text'    => $strDefaultTrackerStatus,
                'items'   => $aStatuses
            ),
            array (
                'type'    => 'break'
            ),
            array (
                'type'    => 'select',
                'name'    => 'default_tracker_type',
                'text'    => $strDefaultTrackerType,
                'items'   => $aTrackerTypes
            ),
            array (
                'type'    => 'break'
            ),
            array (
                'type'    => 'checkbox',
                'name'    => 'default_tracker_linkcampaigns',
                'text'    => $strLinkCampaignsByDefault
            )
        )
    )
);

$oOptions->show($aSettings, $aErrormessage);
phpAds_PageFooter();

?>
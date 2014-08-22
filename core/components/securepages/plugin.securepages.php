<?php
/**
 *
 * SecurePages: Freeware
 *
 * A simple method of creating secure pages throughout the entire site, through system settings insired by PageLocker.
 * Licenses under GPLv2, modifiying this code must have the GPLv2 or later licenses attached.
 * All is welcomed to contribute to this plugin at http://github.com/brh55/securepages, please submit any issues or bugs on there.
 *
 *
 * @ PageLocker by Aaron Ladage (mods by Bob Ray)
 * @ SecurePages by Brandon Him inspired by PageLocker
 * @ version 0.1 - Currently Under Development - DEV 1
 * 
 * SecurePages is distributed in the hope that it will be useful, but WITHOUT ANY
 * WARRANTY.
 * For more information regarding the licenses attached, please check out: www.gnu.org/licenses/gpl-2.0.html
 * 
 * SYSTEM SETTINGS:
 * Secure Template ID - (Required) The ID of secured template (default set to 1, but needs to be modified accordingly to function)
 * Form Resource ID - (Required) The ID of the password form page (default set to 1, but needs to be modified accordingly to function)
 *
**/

/* @var $modx modX */
/* @var $scriptProperties array */
// System Path Creation
if (!function_exists("toForm")) {
    // To form function
    function toForm($resourceId) {
        global $modx;
        unset($_SESSION['password']);  // make sure password is not still set
        if ($modx->resource->get('id') != $resourceId) { // prevent infinite loop
            $modx->sendForward($resourceId);
        }
    }
}

//Logout Function - Not tested yet
if(!function_exist("exitSecure")) {
    function exitSecure($resourceId) {
        global $modx;
        $_SESSION['loggedin'] = 0;
        if ($modx->resource->get('id') != 1) {
            $msg=$modx->lexicon('log_out');
            $modx->event->_output = $msg;
            sleep(3);
            $modx->sendForward(1);
        }
    }
}

/* Set Plug-in Variables */
$secureTemplate = $modx->getOption('securepages.securetemplateid');
$currentTemplate = $modx->resource->get('template');
$formResourceID = $modx->getOption('securepages.formresourceid');

/* Determine currentID if the current template is using secureTemplate */
if ($currentTemplate == $secureTemplate) {
    $currentID = $modx->getOption('formResourceID', $scriptProperties);
} else {
    $currentID = $modx->resource->get('id');
} 

/* Get the password and password group values from the password and passwordGroup Chunk */
$systemPW = $modx->getOption('securepages.secure_password');

/* Do nothing if page is not password-protected, or the form page is not set in the properties */
if ((empty($systemPW)) || (empty($formResourceID))) { 
    return;
}

/* Get and sanitize the password submitted by the user (if any) */
$userPW = isset($_POST['password'])? filter_var($_POST['password'], FILTER_SANITIZE_STRING) : '';

/* Check CAPTCHA with set VERIWORD */
if ($_SESSION['veriword'] != $_POST['captcha_code']) {
/*$rt = "Debug: No Match: SESSION:".$_SESSION['veriword']." captcha_code:".$_POST['captcha_code']; */
    if ($modx->getOption('captcha.use_mathstring',null,true)) {
        $rt=$modx->lexicon('login_mathstring_error');
        toForm($currentID);
    } else {
        $rt=$modx->lexicon('login_captcha_error');
        toForm($currentID);
    }
}

/* Check if form was submitted */
if (!empty($userPW)) { 
    /* Check if pass matches system pass */
    if ($userPW == $systemPW) { 
        /* Set the logged in session */
        $_SESSION['loggedin'] = 1;
        return;
    } else { /* return to form if not logged in */
        toForm($currentID);
    }
} else { /* Form isn't submitted, check if user is already logged in */
    if ( ! isset($_SESSION['loggedin']) || (! $_SESSION['loggedin'] === 1)) {
        toForm($currentID);
    } 
}

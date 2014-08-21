<?php
/**
 *
 * PageLocker
 *
 * Simple front-end password protection for individual or groups of pages.
 *
 * @ author Aaron Ladage (mods by Bob Ray)
 * @ version 1.1.0-beta1 - June 21, 2012
 *
 * PLUGIN PROPERTIES
 * &tvPassword - (Required) The TV for the password (default: 'pagePassword')
 * &tvPasswordGroup - The TV for the password group (default: 'pagePasswordGroup'). Not required, but a good idea, unless you want all password-protected pages to be accessible with the same password.
 * &formResourceID - (Required) The ID of the password form page (no default set, but absolutely necessary -- the plugin will not work without it)
 *
**/

/* @var $modx modX */
/* @var $scriptProperties array */

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

// Set snippetspace Variables
$secureTemplate = $modx->getOption('secure_template');
$currentTemplate = $modx->resource->get('template');
$formResourceID = $modx->getOption('formResourceID', $scriptProperties);

//Determine currentID if the current template is using secureTemplate
if ($currentTemplate == $secureTemplate) {
    $currentID = $modx->getOption('formResourceID', $scriptProperties);
} else {
    $currentID = $modx->resource->get('id');
} 


// Get the password and password group values from the password and passwordGroup Chunk
$systemPW = $modx->getOption('password');

// Do nothing if page is not password-protected, or the form page is not set in the properties 
if ((empty($systemPW)) || (empty($formResourceID))) { 
    return;
}

/* Get and sanitize the password submitted by the user (if any) */
$userPW = isset($_POST['password'])? filter_var($_POST['password'], FILTER_SANITIZE_STRING) : '';

// Check CAPTCHA with set VERIWORD
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

// Check if form was submitted
if (!empty($userPW)) { 
    // Check if pass matches system pass
    if ($userPW == $systemPW) { 
        // Set the logged in session
        $_SESSION['loggedin'] = 1;
        return;
    } else { // return to form if not logged in
        toForm($currentID);
    }
} else { // Form isn't submitted, check if user is already logged in
    if ( ! isset($_SESSION['loggedin']) || (! $_SESSION['loggedin'] === 1)) {
        toForm($currentID);
    } 
}

<?php
/** System settings for SecurePages Plugin GPLv2
* @package securepages
* @subpackage build
*/
/* @var $modx modX */
$settings = array();
$settings['securepages.securetemplateid']= $modx->newObject('modSystemSetting');
$settings['securepages.securetemplateid']->fromArray(array (
'key' => 'securepages.securetemplateid',
'value' => '1',
'xtype' => 'textfield',
'namespace' => 'securepages',
'area' => 'setup',
), '', true, true);

$settings['securepages.formresourceid']= $modx->newObject('modSystemSetting');
$settings['securepages.formresourceid']->fromArray(array (
'key' => 'securepages.formresourceid',
'value' => '1',
'xtype' => 'textfield',
'namespace' => 'securepages',
'area' => 'setup',
), '', true, true);

$settings['securepages.secure_password']= $modx->newObject('modSystemSetting');
$settings['securepages.secure_password']->fromArray(array (
'key' => 'securepages.secure_password',
'value' => 'password',
'xtype' => 'textfield',
'namespace' => 'securepages',
'area' => 'setup',
), '', true, true);

return $settings;
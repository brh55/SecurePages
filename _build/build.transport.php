<?php
/* define package names */
$mtime = microtime();
$mtime = explode(" ", $mtime);
$mtime = $mtime[1] + $mtime[0];
$tstart = $mtime;
set_time_limit(0); /* makes sure our script doesnt timeout */

define('PKG_NAME','SecurePages');
define('PKG_NAME_LOWER','securepages');
define('PKG_VERSION','1.0.0');
define('PKG_RELEASE','beta1');
 
/* define build paths */
$root = dirname(dirname(__FILE__)).'/';
$sources = array(
    'root' => $root,
    'build' => $root . '_build/',
    'data' => $root . '_build/data/',
    'resolvers' => $root . '_build/resolvers/',
    'templates' => $root.'core/components/'.PKG_NAME_LOWER.'/templates/',
    'lexicon' => $root . 'core/components/'.PKG_NAME_LOWER.'/lexicon/',
    'docs' => $root.'core/components/'.PKG_NAME_LOWER.'/docs/',
    'source_assets' => $root.'assets/components/'.PKG_NAME_LOWER,
    'source_core' => $root.'core/components/'.PKG_NAME_LOWER,
);
unset($root);
 
/* override with your own defines here (see build.config.sample.php) */
require_once $sources['build'] . 'build.config.php';
require_once MODX_CORE_PATH . 'model/modx/modx.class.php';
 
$modx= new modX();
$modx->initialize('mgr');
echo '<pre>'; /* used for nice formatting of log messages */
$modx->setLogLevel(modX::LOG_LEVEL_INFO);
$modx->setLogTarget(XPDO_CLI_MODE ? 'ECHO' : 'HTML');

$modx->loadClass('transport.modPackageBuilder','',false, true);
$builder = new modPackageBuilder($modx);
$builder->createPackage(PKG_NAME_LOWER,PKG_VERSION,PKG_RELEASE);
$builder->registerNamespace(PKG_NAME_LOWER,false,true,'{core_path}components/'.PKG_NAME_LOWER.'/');

/* @var $ctgy modPlugin */
$ctgy = $modx->newObject('modPlugin');
$ctgy->set('id',1);
$ctgy->set('category',PKG_NAME);
$ctgy->set('description', '<b>1.0.0-beta1</b> SecurePages Plugin: Securing your pages through a secure template');
$ctgy->set('plugincode', file_get_contents($sources['source_core'] . '/plugin.securepages.php')); 

/* add snippets */
//$modx->log(modX::LOG_LEVEL_INFO,'Packaging in snippets...');
//$snippets = include $sources['data'].'transport.snippets.php';
//if (empty($snippets)) $modx->log(modX::LOG_LEVEL_ERROR,'Could not package in snippets.');
//$category->addMany($snippets);
 
/* create category vehicle */
$modx->log(xPDO::LOG_LEVEL_INFO,'Writing Up Package Attributes...'); flush();
$attr = array(
    xPDOTransport::UNIQUE_KEY => 'category',
    xPDOTransport::PRESERVE_KEYS => false,
    xPDOTransport::UPDATE_OBJECT => false, /* if user already set, keep it */
    xPDOTransport::RELATED_OBJECTS => true,
    xPDOTransport::RELATED_OBJECT_ATTRIBUTES => array (
        'PluginEvents' => array(
            xPDOTransport::PRESERVE_KEYS => false,
            xPDOTransport::UPDATE_OBJECT => true,
            xPDOTransport::UNIQUE_KEY => array('pluginid','event'),
        ),
    ),
);
$builder->putVehicle($vehicle);
unset($vehicle,$action); /* to keep memory low */
$modx->log(xPDO::LOG_LEVEL_INFO,'Finished Setting Package Attributes...'); flush();

$modx->log(xPDO::LOG_LEVEL_INFO,'Creating the Transport Vehicle...'); flush();

/* load system settings */
$settings = include $sources['data'].'transport.settings.php'; 
$attributes= array(
    xPDOTransport::UNIQUE_KEY => 'key',
    xPDOTransport::PRESERVE_KEYS => true,
    xPDOTransport::UPDATE_OBJECT => false,
);
foreach ($settings as $setting) {
    $vehicle = $builder->createVehicle($setting,$attributes);
    $builder->putVehicle($vehicle);
}
unset($settings,$setting,$attributes);

/* create category */
$category= $modx->newObject('modCategory');
$category->set('id',1);
$category->set('category','SecurePages');

$vehicle = $builder->createVehicle($category,$attr);
$builder->putVehicle($vehicle);

/* prompt user with accepting readme and license file*/
$builder->setPackageAttributes(array(
    'license' => file_get_contents($sources['docs'] . 'license.txt'),
    'readme' => file_get_contents($sources['docs'] . 'readme.txt'),
    'changelog' => file_get_contents($sources['docs'] . 'changelog.txt'),
    'setup-options' => '',
));

/* zip up package */
$builder->pack();

$mtime= microtime();
$mtime= explode(" ", $mtime);
$mtime= $mtime[1] + $mtime[0];
$tend= $mtime;
$totalTime= ($tend - $tstart);
$totalTime= sprintf("%2.4f s", $totalTime);

$modx->log(modX::LOG_LEVEL_INFO,"Package Built.\nExecution time: {$totalTime}");

session_write_close();
exit ();
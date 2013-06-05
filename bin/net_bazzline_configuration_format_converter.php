#!/usr/bin/php
<?php
/**
 * @author stev leibelt <artodeto@arcor.de>
 * @since 2013-06-06
 */

chdir(realpath(getcwd()));

require 'vendor/autoload.php';

//autloader for development
if (file_exists('source/Net/Bazzline/ConfigurationFormatConverter/developmentAutoloader.php')) {
    echo 'Development mode.' . PHP_EOL;
    echo 'Loading autoloaders' . PHP_EOL;

    require 'source/Net/Bazzline/ConfigurationFormatConverter/developmentAutoloader.php';
}

$application = new \Net\Bazzline\ConfigurationFormatConverter\Application\Application();
$application->run();
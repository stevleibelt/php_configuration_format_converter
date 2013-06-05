<?php
/**
 * @author stev leibelt <artodeto@arcor.de>
 * @since 2013-06-06
 */

function netBazzlineConfigurationFormatConverterDevelopmentAutoloader($className)
{
    $namespace = 'Net\\Bazzline\\ConfigurationFormatConverter\\';
    //$lengthOfNamespace = strlen($namespace);
    //$lengthOfNamespace = 42;
    //$expectedNamespace = substr($classname, 0, $lengthOfNamespace);
    $expectedNamespace = substr($className, 0, 42);

    $isSupportedClassNameByNamespace = ($namespace == $expectedNamespace);

    if ($isSupportedClassNameByNamespace) {
        $classNameWithRemovedNamespace = str_replace($namespace, '', $className);
        $fileName = str_replace('\\', DIRECTORY_SEPARATOR, $classNameWithRemovedNamespace) . '.php';
        $includePaths = array(
            realpath(__DIR__ . DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR
        );

        foreach ($includePaths as $includePath) {
            $filePath = realpath($includePath . DIRECTORY_SEPARATOR . $fileName);

            if (file_exists($filePath)) {
                require_once $filePath;

                break;
            } else {
                echo var_export(
                        array(
                            'classname' => $className,
                            'filename' => $fileName,
                            'filepath' => $filePath,
                            'includedPath' => $includePath
                        ),
                        true
                    ) . PHP_EOL;
            }
        }
    } else {
        return false;
    }
}

spl_autoload_register('netBazzlineConfigurationFormatConverterDevelopmentAutoloader');

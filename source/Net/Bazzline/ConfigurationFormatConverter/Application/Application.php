<?php
/**
 * @author stev leibelt <artodeto@arcor.de>
 * @since 2013-06-06 
 */

namespace Net\Bazzline\ConfigurationFormatConverter\Application;

use Net\Bazzline\Symfony\Console\Application\Application as BazzlineApplication;
use Net\Bazzline\ConfigurationFormatConverter\Command\ConvertCommand;

/**
 * Class Application
 *
 * @package Net\Bazzline\ConfigurationFormatConverter\Application
 * @author stev leibelt <artodeto@arcor.de>
 * @since 2013-06-06
 */
class Application extends BazzlineApplication
{
    /**
     * @{inheritDoc}
     */
    public function __construct()
    {
        parent::__construct('Configuration Format Converter', '0.0.1');

        $this->add(new ConvertCommand());
    }
}
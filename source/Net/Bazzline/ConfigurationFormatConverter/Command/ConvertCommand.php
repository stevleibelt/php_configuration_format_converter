<?php
/**
 * @author stev leibelt <artodeto@arcor.de>
 * @since 2013-06-06 
 */

namespace Net\Bazzline\ConfigurationFormatConverter\Command;

use Net\Bazzline\Component\Converter\InvalidArgumentException;
use Net\Bazzline\Symfony\Console\Command\Command;
use Net\Bazzline\Component\Converter\ConverterFactory;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use RuntimeException;

/**
 * Class ConvertCommand
 *
 * @package Net\Bazzline\ConfigurationFormatConverter
 * @author stev leibelt <artodeto@arcor.de>
 * @since 2013-06-06
 * @todo replace file related stuff with finished https://github.com/stevleibelt/php_component_filesystem
 */
class ConvertCommand extends Command
{
    /**
     * @var string
     * @author stev leibelt <artodeto@arcor.de>
     * @since 2013-06-06
     */
    const ARGUMENT_DESTINATION = 'destination';

    /**
     * @var string
     * @author stev leibelt <artodeto@arcor.de>
     * @since 2013-06-06
     */
    const ARGUMENT_SOURCE = 'source';

    /**
     * @var string
     * @author stev leibelt <artodeto@arcor.de>
     * @since 2013-06-06
     */
    const OPTION_FORCE = 'force';

    /**
     * @var string
     * @author stev leibelt <artodeto@arcor.de>
     * @since 2013-06-06
     */
    private $destination;

    /**
     * @var \Net\Bazzline\Component\Converter\ConverterInterface
     * @author stev leibelt <artodeto@arcor.de>
     * @since 2013-06-06
     */
    private $destinationConverter;

    /**
     * @var string
     * @author stev leibelt <artodeto@arcor.de>
     * @since 2013-06-06
     */
    private $source;

    /**
     * @var \Net\Bazzline\Component\Converter\ConverterInterface
     * @author stev leibelt <artodeto@arcor.de>
     * @since
     */
    private $sourceConverter;

    /**
     * @author stev leibelt <artodeto@arcor.de>
     * @since 2013-06-06
     */
    protected function configure()
    {
        $this
            ->setName('converter')
            ->setDescription('converts files from one format to another')
            ->setDefinition(
                array(
                    new InputArgument(
                        self::ARGUMENT_SOURCE,
                        InputArgument::REQUIRED,
                        'The existing source file you want to convert.'
                    ),
                    new InputArgument(
                        self::ARGUMENT_DESTINATION,
                        InputArgument::REQUIRED,
                        'The destination file for your conversation.'
                    ),
                    new InputOption(
                        '--' . self::OPTION_FORCE,
                        '-f',
                        InputOption::VALUE_NONE,
                        'The destination is overwritten if exists.'
                    )
                )
            )
            ->setHelp(
                'The <info>%command.name%</info> command is converting the ' . PHP_EOL .
                'provided source file to the destination file.' . PHP_EOL .
                PHP_EOL .
                'Supported formats are ".yaml", ".json", ".php" and ".xml"' . PHP_EOL
            )
        ;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     * @throws InvalidArgumentException
     * @author stev leibelt <artodeto@arcor.de>
     * @since 2013-06-06
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->setSourceAndDestination();
        $this->setConverters();
        $this->writeDestinationContent();
    }

    /**
     * Validates input arguments and set private properties.
     *
     * @throws \Net\Bazzline\Component\Converter\InvalidArgumentException
     * @author stev leibelt <artodeto@arcor.de>
     * @since 2013-06-06
     */
    private function setSourceAndDestination()
    {
        $source = $this->io->getArgument(self::ARGUMENT_SOURCE);
        $destination = $this->io->getArgument(self::ARGUMENT_DESTINATION);
        $isForced = $this->io->getOption(self::OPTION_FORCE);

        if (file_exists($destination)
            && !$isForced) {
            throw new RuntimeException(
                'Destination "' . $this->destination . '" already exists.' . PHP_EOL .
                'Use --' . self::OPTION_FORCE . ' to overwrite.'
            );
        }

        if (!file_exists($source)) {
            throw new InvalidArgumentException(
                'Provided source file does not exists! ' . PHP_EOL .
                'source: "' . $source . '"'
            );
        }

        $destinationExtension = $this->getFileExtension($destination);
        $sourceExtension = $this->getFileExtension($source);

        if ($destinationExtension == $destination
            || !$this->isFileExtensionSupported($destinationExtension)) {
            throw new InvalidArgumentException(
                'Destination has to be a supported format.' . PHP_EOL .
                'Use --help to list supported formats.'
            );
        }

        if ($sourceExtension == $source
            || !$this->isFileExtensionSupported($sourceExtension)) {
            throw new InvalidArgumentException(
                'Source has to be a supported format.' . PHP_EOL .
                'Use --help to list supported formats.'
            );
        }

        if (!is_writable(dirname($destination))) {
            throw new RuntimeException(
                'Destination is not writable.'
            );
        }

        $this->destination = $destination;
        $this->source = $source;
    }

    /**
     * Returns the assumed file extension. Assumed that the extension is the
     *  last string after the last available dot (".")
     *
     * @param string $fileName
     * @return string
     * @author stev leibelt <artodeto@arcor.de>
     * @since 2013-06-06
     */
    private function getFileExtension($fileName)
    {
        $fileNameAsArray = explode('.', $fileName);

        return (string) end($fileNameAsArray);
    }

    /**
     * Validates if given file extension is supported
     *
     * @param string $extension
     * @return bool
     * @author stev leibelt <artodeto@arcor.de>
     * @since 2013-06-06
     */
    private function isFileExtensionSupported($extension)
    {
        $supportedExtension = array('xml' => true, 'yaml' => true, 'php' => true, 'json' => true);

        return isset($supportedExtension[strtolower($extension)]);
    }

    /**
     * Sets converters by content of source and destination
     *
     * @author stev leibelt <artodeto@arcor.de>
     * @since 2013-06-06
     */
    private function setConverters()
    {
        $converterFactory = ConverterFactory::buildDefault();
        $extensionToConverter = array(
            'php' => 'Net\Bazzline\Component\Converter\PhpArrayConverter',
            'json' => 'Net\Bazzline\Component\Converter\JSONConverter',
            'yaml' => 'Net\Bazzline\Component\Converter\YAMLConverter',
            'xml' => 'Net\Bazzline\Component\Converter\XMLConverter'
        );

        $destinationExtension = strtolower($this->getFileExtension($this->destination));
        $sourceExtension = strtolower($this->getFileExtension($this->source));

        $destinationConverterName = $extensionToConverter[$destinationExtension];
        $sourceConverterName = $extensionToConverter[$sourceExtension];

        $this->destinationConverter = $converterFactory->get($destinationConverterName);
        $this->sourceConverter = $converterFactory->get($sourceConverterName);

        $this->sourceConverter->fromSource(file_get_contents($this->source));
    }

    /**
     * Uses destination and source converter to write destination content
     *
     * @author stev leibelt <artodeto@arcor.de>
     * @since 2013-06-06
     */
    private function writeDestinationContent()
    {
        $this->destinationConverter->fromPhpArray($this->sourceConverter->toPhpArray());

        $content = $this->destinationConverter->toSource();
        if (is_array($content)) {
            $destinationContent = '<?php
/**
 * Converted configuration from source "' . $this->source . '"
 *
 * @author net_bazzline/php_component_converter
 * @since ' . date('Y-m-d') . '
 */

return ' . var_export($content, true) . '
';
        } else {
            $destinationContent = $content;
        }

        if (file_put_contents($this->destination, $destinationContent) === false) {
            throw new RuntimeException(
                'Writing of destination file content was not sucessfull.'
            );
        }
    }
}
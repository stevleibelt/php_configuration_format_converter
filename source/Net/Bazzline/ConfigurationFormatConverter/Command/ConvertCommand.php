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
use Symfony\Component\Console\Output\OutputInterface;
use RuntimeException;

/**
 * Class ConvertCommand
 *
 * @package Net\Bazzline\ConfigurationFormatConverter
 * @author stev leibelt <artodeto@arcor.de>
 * @since 2013-06-06
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
    private $destination;

    /**
     * @var \Net\Bazzline\Component\Converter\ConverterInterface
     * @author stev leibelt <artodeto@arcor.de>
     * @since
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
        //@todo convert source to php array and php array to destination
        //@todo write destination
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
        $source = $this->io->getArgument('source');
        $destination = $this->io->getArgument('destination');

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
    }
}
<?php
/**
 * @author stev leibelt <artodeto@arcor.de>
 * @since 2013-06-06 
 */

namespace Net\Bazzline\ConfigurationFormatConverter\Command;

use Net\Bazzline\Component\Converter\InvalidArgumentException;
use Net\Bazzline\Symfony\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

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
                    new InputArgument('source', InputArgument::REQUIRED, 'The existing source file you want to convert.'),
                    new InputArgument('destination', InputArgument::REQUIRED, 'The destination file for your conversation.')
                )
            )
            ->setHelp(
                'The <info>%command.name%</info> command is converting the ' . PHP_EOL .
                'provided source file to the destination file.' . PHP_EOL
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
        $source = $input->getArgument('source');
        $destination = $input->getArgument('destination');

        if (!file_exists($source)) {
            throw new InvalidArgumentException(
                'Provided source file does not exists! ' . PHP_EOL .
                'source: "' . $source . '"'
            );
        }
    }
}
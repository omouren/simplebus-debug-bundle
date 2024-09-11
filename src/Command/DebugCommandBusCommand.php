<?php

namespace Omouren\SimpleBusDebugBundle\Command;

use Omouren\SimpleBusDebugBundle\Descriptor\DescriptorHelper;
use SimpleBus\Message\CallableResolver\CallableMap;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * @author Olivier Mouren <mouren.olivier@gmail.com>
 */
class DebugCommandBusCommand extends Command
{
    private $commandBusSubscribersCollection;

    public function __construct(CallableMap $commandBusSubscribersCollection)
    {
        parent::__construct();
        $this->commandBusSubscribersCollection = $commandBusSubscribersCollection;
    }

    /**
     * @see Command
     */
    protected function configure()
    {
        $this
            ->setName('omouren:debug:simple-bus:command-bus')
            ->setDescription('List commandBus commands with handlers')
            ->setDefinition(array(
                new InputArgument('commandName', InputArgument::OPTIONAL, 'A command name'),
                new InputOption('format', null, InputOption::VALUE_REQUIRED, 'The output format  (txt, xml, json, or md)', 'txt'),
                new InputOption('raw', null, InputOption::VALUE_NONE, 'To output raw description'),
            ))
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $options = array();
        if ($command = $input->getArgument('commandName')) {
            $options = array('command' => $command);
        }

        $helper = new DescriptorHelper();
        $options['format'] = $input->getOption('format');
        $options['raw_text'] = $input->getOption('raw');
        $options['output'] = $io;
        $helper->describe($io, $this->commandBusSubscribersCollection, $options);

        return 0;
    }
}

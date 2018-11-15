<?php

namespace Omouren\SimpleBusDebugBundle\Command;

use Omouren\SimpleBusDebugBundle\Descriptor\DescriptorHelper;
use SimpleBus\Message\CallableResolver\CallableCollection;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * @author Olivier Mouren <mouren.olivier@gmail.com>
 */
class DebugEventBusCommand extends Command
{
    private $eventBusSubscribersCollection;

    public function __construct(CallableCollection $eventBusSubscribersCollection)
    {
        parent::__construct();
        $this->eventBusSubscribersCollection = $eventBusSubscribersCollection;
    }

    /**
     * @see Command
     */
    protected function configure()
    {
        $this
            ->setName('omouren:debug:simple-bus:event-bus')
            ->setDescription('List eventBus events with handlers')
            ->setDefinition(array(
                new InputArgument('eventName', InputArgument::OPTIONAL, 'An event name'),
                new InputOption('format', null, InputOption::VALUE_REQUIRED, 'The output format  (txt, xml, json, or md)', 'txt'),
                new InputOption('raw', null, InputOption::VALUE_NONE, 'To output raw description'),
            ))
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $options = array();
        if ($event = $input->getArgument('eventName')) {
            $options = array('event' => $event);
        }

        $helper = new DescriptorHelper();
        $options['format'] = $input->getOption('format');
        $options['raw_text'] = $input->getOption('raw');
        $options['output'] = $io;
        $helper->describe($io, $this->eventBusSubscribersCollection, $options);
    }
}
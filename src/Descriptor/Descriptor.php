<?php

namespace Omouren\SimpleBusDebugBundle\Descriptor;

use SimpleBus\Message\CallableResolver\CallableCollection;
use SimpleBus\Message\CallableResolver\CallableMap;
use Symfony\Component\Console\Descriptor\DescriptorInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Olivier Mouren <mouren.olivier@gmail.com>
 */
abstract class Descriptor implements DescriptorInterface
{
    /**
     * @var OutputInterface
     */
    protected $output;

    /**
     * {@inheritdoc}
     */
    public function describe(OutputInterface $output, $object, array $options = array())
    {
        $this->output = $output;

        switch (true) {
            case $object instanceof CallableCollection:
                $this->describeEventBus($object, $options);
                break;
            case $object instanceof CallableMap:
                $this->describeCommandBus($object, $options);
                break;
            default:
                throw new \InvalidArgumentException(sprintf('Object of type "%s" is not describable.', \get_class($object)));
        }
    }

    /**
     * Returns the output.
     *
     * @return OutputInterface The output
     */
    protected function getOutput()
    {
        return $this->output;
    }

    /**
     * Writes content to output.
     *
     * @param string $content
     * @param bool   $decorated
     */
    protected function write($content, $decorated = false)
    {
        $this->output->write($content, false, $decorated ? OutputInterface::OUTPUT_NORMAL : OutputInterface::OUTPUT_RAW);
    }

    abstract protected function describeEventBus(CallableCollection $callables, array $options = array());
    
    abstract protected function describeCommandBus(CallableMap $callables, array $options = array());
}

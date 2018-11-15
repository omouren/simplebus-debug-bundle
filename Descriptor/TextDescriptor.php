<?php

namespace Omouren\SimpleBusDebugBundle\Descriptor;

use SimpleBus\Message\CallableResolver\CallableCollection;
use SimpleBus\Message\CallableResolver\CallableMap;
use Symfony\Component\Console\Style\SymfonyStyle;

class TextDescriptor extends Descriptor
{
    protected function describeEventBus(CallableCollection $callables, array $options = array())
    {
        $event = array_key_exists('event', $options) ? $options['event'] : null;

        if (null !== $event) {
            $title = sprintf('Registered Handlers for "%s" Event', $event);
        } else {
            $title = 'Registered Handlers Grouped by Event';
        }

        $options['output']->title($title);
        if (null !== $event) {
            $handlers = $callables->filter($event);

            ksort($handlers);
            $this->renderEventHandlerTable($handlers, $options['output']);
        } else {
            $getCallables = function () { return $this->callablesByName; };
            $handlers = $getCallables->call($callables);

            ksort($handlers);

            foreach ($handlers as $eventListened => $eventListeners) {
                $options['output']->section(sprintf('"%s" event', $eventListened));
                $this->renderEventHandlerTable($eventListeners, $options['output']);
            }
        }
    }

    protected function describeCommandBus(CallableMap $callables, array $options = array())
    {
        $command = array_key_exists('command', $options) ? $options['command'] : null;

        if (null !== $command) {
            $title = sprintf('Registered Handlers for "%s" Command', $command);
        } else {
            $title = 'Registered Handlers Grouped by Command';
        }

        $options['output']->title($title);
        if (null !== $command) {
            $handlers = $callables->get($command);

            $this->renderCommandHandlerTable([$handlers], $options['output']);
        } else {
            $getCallables = function () { return $this->callablesByName; };
            $handlers = $getCallables->call($callables);

            ksort($handlers);

            foreach ($handlers as $commandListened => $commandListeners) {
                $options['output']->section(sprintf('"%s" command', $commandListened));
                $this->renderCommandHandlerTable($commandListeners, $options['output']);
            }
        }
    }

    private function renderEventHandlerTable(array $eventListeners, SymfonyStyle $io)
    {
        $tableHeaders = array('Order', 'Callable');
        $tableRows = array();

        foreach ($eventListeners as $order => $listener) {
            $tableRows[] = array(sprintf('#%d', $order + 1), $this->formatCallable($listener));
        }

        $io->table($tableHeaders, $tableRows);
    }

    private function renderCommandHandlerTable(array $commandListeners, SymfonyStyle $io)
    {
        $tableHeaders = array('Order', 'Callable');
        $tableRows = array();

        foreach ($commandListeners as $order => $listener) {
            $tableRows[] = array(sprintf('#%d', $order + 1), $this->formatCallable($listener));
        }

        $io->table($tableHeaders, $tableRows);
    }

    /**
     * @param callable $callable
     *
     * @return string
     */
    private function formatCallable($callable)
    {
        if (\is_array($callable) && isset($callable[0]) && \is_object($callable[0])) {
            return sprintf('%s::%s()', \get_class($callable[0]), $callable[1]);
        }

        if (\is_array($callable) && isset($callable['serviceId']) && $callable['method']) {
            return sprintf('%s::%s()', $callable['serviceId'], $callable['method']);
        }

        if (\is_string($callable)) {
            return sprintf('%s()', $callable);
        }

        if (method_exists($callable, '__invoke')) {
            return sprintf('%s::__invoke()', \get_class($callable));
        }

        throw new \InvalidArgumentException('Callable is not describable.');
    }
}

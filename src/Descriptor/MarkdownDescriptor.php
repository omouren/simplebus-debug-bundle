<?php

namespace Omouren\SimpleBusDebugBundle\Descriptor;

use SimpleBus\Message\CallableResolver\CallableCollection;
use SimpleBus\Message\CallableResolver\CallableMap;

/**
 * @author Olivier Mouren <mouren.olivier@gmail.com>
 */
class MarkdownDescriptor extends Descriptor
{
    protected function describeEventBus(CallableCollection $callables, array $options = array())
    {
        $event = array_key_exists('event', $options) ? $options['event'] : null;

        $title = 'Registered handlers';
        if (null !== $event) {
            $title .= sprintf(' for event `%s`', $event);
        }

        $this->write(sprintf('# %s', $title)."\n");

        if (null !== $event) {
            $handlers = $callables->filter($event);

            ksort($handlers);

            foreach ($handlers as $order => $listener) {
                $this->write("\n".sprintf('## Handler %d', $order + 1)."\n");
                $this->describeCallable($listener);
            }
        } else {
            $getCallables = function () { return $this->callablesByName; };
            $handlers = $getCallables->call($callables);

            ksort($handlers);

            foreach ($handlers as $eventListened => $eventListeners) {
                $this->write("\n".sprintf('## %s', $eventListened)."\n");

                foreach ($eventListeners as $order => $eventListener) {
                    $this->write("\n".sprintf('### Handler %d', $order + 1)."\n");
                    $this->describeCallable($eventListener);
                }
            }
        }
    }

    protected function describeCommandBus(CallableMap $callables, array $options = array())
    {
        $command = array_key_exists('command', $options) ? $options['command'] : null;

        $title = 'Registered handlers';
        if (null !== $command) {
            $title .= sprintf(' for command `%s`', $command);
        }

        $this->write(sprintf('# %s', $title)."\n");

        if (null !== $command) {
            $handler = $callables->get($command);

            $this->write("\n".sprintf('## Handler %d', $order + 1)."\n");
            $this->describeCallable($handler);
        } else {
            $getCallables = function () { return $this->callablesByName; };
            $handlers = $getCallables->call($callables);

            ksort($handlers);

            foreach ($handlers as $commandListened => $commandListeners) {
                $this->write("\n".sprintf('## %s', $commandListened)."\n");

                foreach ($commandListeners as $order => $commandListener) {
                    $this->write("\n".sprintf('### Handler %d', $order + 1)."\n");
                    $this->describeCallable($commandListener);
                }
            }
        }
    }

    protected function describeCallable($callable, array $options = array())
    {
        $string = '';

        if (\is_array($callable) && isset($callable[0]) && \is_object($callable[0])) {
            $string .= "\n- Type: `function`";

            $string .= "\n".sprintf('- Name: `%s`', $callable[1]);
            $string .= "\n".sprintf('- Class: `%s`', \get_class($callable[0]));

            return $this->write($string."\n");
        }
        
        if (\is_array($callable) && isset($callable['serviceId']) && isset($callable['method'])) {
            $string .= "\n- Type: `function`";

            $string .= "\n".sprintf('- Name: `%s`', $callable['method']);
            $string .= "\n".sprintf('- Class: `%s`', $callable['serviceId']);

            return $this->write($string."\n");
        }

        if (\is_string($callable)) {
            $string .= "\n- Type: `function`";

            if (false === strpos($callable, '::')) {
                $string .= "\n".sprintf('- Name: `%s`', $callable);
            } else {
                $callableParts = explode('::', $callable);

                $string .= "\n".sprintf('- Name: `%s`', $callableParts[1]);
                $string .= "\n".sprintf('- Class: `%s`', $callableParts[0]);
            }

            return $this->write($string."\n");
        }

        throw new \InvalidArgumentException('Callable is not describable.');
    }
}

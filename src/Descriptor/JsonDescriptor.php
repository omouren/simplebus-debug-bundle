<?php

namespace Omouren\SimpleBusDebugBundle\Descriptor;

use SimpleBus\Message\CallableResolver\CallableCollection;
use SimpleBus\Message\CallableResolver\CallableMap;

/**
 * @author Olivier Mouren <mouren.olivier@gmail.com>
 */
class JsonDescriptor extends Descriptor
{
    protected function describeEventBus(CallableCollection $callables, array $options = array())
    {
        $this->writeData($this->getEventBusData($callables, array_key_exists('event', $options) ? $options['event'] : null), $options);
    }

    protected function describeCommandBus(CallableMap $callables, array $options = array())
    {
        $this->writeData($this->getCommandBusData($callables, array_key_exists('event', $options) ? $options['event'] : null), $options);
    }

    private function getEventBusData(CallableCollection $callables, $event = null)
    {
        $data = array();

        if (null !== $event) {
            $handlers = $callables->filter($event);

            ksort($handlers);
            foreach ($handlers as $listener) {
                $l = $this->getCallableData($listener);
                $data[] = $l;
            }
        } else {
            $getCallables = function () { return $this->callablesByName; };
            $handlers = $getCallables->call($callables);

            ksort($handlers);

            foreach ($handlers as $eventListened => $eventListeners) {
                foreach ($eventListeners as $eventListener) {
                    $l = $this->getCallableData($eventListener);
                    $data[$eventListened][] = $l;
                }
            }
        }

        return $data;
    }

    private function getCommandBusData(CallableMap $callables, $command = null)
    {
        $data = array();

        if (null !== $command) {
            $handler = $callables->get($command);

            $l = $this->getCallableData($handler);
            $data[] = $l;
        } else {
            $getCallables = function () { return $this->callablesByName; };
            $handlers = $getCallables->call($callables);

            ksort($handlers);

            foreach ($handlers as $commandListened => $commandListeners) {
                foreach ($commandListeners as $commandListener) {
                    $l = $this->getCallableData($commandListener);
                    $data[$commandListened][] = $l;
                }
            }
        }

        return $data;
    }

    /**
     * @param callable $callable
     * @param array    $options
     *
     * @return array
     */
    private function getCallableData($callable, array $options = array())
    {
        $data = array();

        if (\is_array($callable) && isset($callable[0]) && \is_object($callable[0])) {
            $data['type'] = 'function';

            $data['name'] = $callable[1];
            $data['class'] =  \get_class($callable[0]);

            return $data;
        }

        if (\is_array($callable) && isset($callable['serviceId']) && isset($callable['method'])) {
            $data['type'] = 'function';

            $data['name'] = $callable['method'];
            $data['class'] = $callable['serviceId'];

            return $data;
        }

        if (\is_string($callable)) {
            $data['type'] = 'function';

            if (false === strpos($callable, '::')) {
                $data['name'] = $callable;
            } else {
                $callableParts = explode('::', $callable);

                $data['name'] = $callableParts[1];
                $data['class'] = $callableParts[0];
            }

            return $data;
        }

        throw new \InvalidArgumentException('Callable is not describable.');
    }

    /**
     * Writes data as json.
     *
     * @return array|string
     */
    private function writeData(array $data, array $options)
    {
        $flags = isset($options['json_encoding']) ? $options['json_encoding'] : 0;
        $this->write(json_encode($data, $flags | JSON_PRETTY_PRINT)."\n");
    }
}

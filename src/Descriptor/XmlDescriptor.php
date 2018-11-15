<?php

namespace Omouren\SimpleBusDebugBundle\Descriptor;

use SimpleBus\Message\CallableResolver\CallableCollection;
use SimpleBus\Message\CallableResolver\CallableMap;

/**
 * @author Olivier Mouren <mouren.olivier@gmail.com>
 */
class XmlDescriptor extends Descriptor
{
    protected function describeEventBus(CallableCollection $callables, array $options = array())
    {
        $this->writeDocument($this->getEventBusDocument($callables, array_key_exists('event', $options) ? $options['event'] : null));
    }

    protected function describeCommandBus(CallableMap $callables, array $options = array())
    {
        $this->writeDocument($this->getCommandBusDocument($callables, array_key_exists('command', $options) ? $options['command'] : null));
    }

    private function getEventBusDocument(CallableCollection $callables, $event = null)
    {
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $dom->appendChild($eventBusXML = $dom->createElement('event-bus'));

        if (null !== $event) {
            $handlers = $callables->filter($event);

            ksort($handlers);

            $this->appendEventBusDocument($eventBusXML, $handlers);
        } else {
            $getCallables = function () { return $this->callablesByName; };
            $handlers = $getCallables->call($callables);

            ksort($handlers);

            foreach ($handlers as $eventListened => $eventListeners) {
                $eventBusXML->appendChild($eventXML = $dom->createElement('event'));
                $eventXML->setAttribute('name', $eventListened);

                $this->appendEventBusDocument($eventXML, $eventListeners);
            }
        }

        return $dom;
    }

    private function getCommandBusDocument(CallableMap $callables, $event = null)
    {
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $dom->appendChild($eventBusXML = $dom->createElement('command-bus'));

        if (null !== $event) {
            $handler = $callables->get($event);

            $this->appendEventBusDocument($eventBusXML, [$handler]);
        } else {
            $getCallables = function () { return $this->callablesByName; };
            $handlers = $getCallables->call($callables);

            ksort($handlers);

            foreach ($handlers as $eventListened => $eventListeners) {
                $eventBusXML->appendChild($eventXML = $dom->createElement('event'));
                $eventXML->setAttribute('name', $eventListened);

                $this->appendEventBusDocument($eventXML, $eventListeners);
            }
        }

        return $dom;
    }

    private function appendEventBusDocument(\DOMElement $element, array $handlers)
    {
        foreach ($handlers as $listener) {
            $callableXML = $this->getCallableDocument($listener);

            $element->appendChild($element->ownerDocument->importNode($callableXML->childNodes->item(0), true));
        }
    }

    /**
     * @param callable $callable
     *
     * @return \DOMDocument
     */
    private function getCallableDocument($callable)
    {
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $dom->appendChild($callableXML = $dom->createElement('callable'));

        if (\is_array($callable) && isset($callable[0]) && \is_object($callable[0])) {
            $callableXML->setAttribute('type', 'function');

            $callableXML->setAttribute('name', $callable[1]);
            $callableXML->setAttribute('class', $callable[0]);

            return $dom;
        }

        if (\is_array($callable) && isset($callable['serviceId']) && isset($callable['method'])) {
            $callableXML->setAttribute('type', 'function');

            $callableXML->setAttribute('name', $callable['method']);
            $callableXML->setAttribute('class', $callable['serviceId']);

            return $dom;
        }

        if (\is_string($callable)) {
            $callableXML->setAttribute('type', 'function');

            if (false === strpos($callable, '::')) {
                $callableXML->setAttribute('name', $callable);
            } else {
                $callableParts = explode('::', $callable);

                $callableXML->setAttribute('name', $callableParts[1]);
                $callableXML->setAttribute('class', $callableParts[0]);
            }

            return $dom;
        }

        throw new \InvalidArgumentException('Callable is not describable.');
    }

    /**
     * Writes DOM document.
     *
     * @return \DOMDocument|string
     */
    private function writeDocument(\DOMDocument $dom)
    {
        $dom->formatOutput = true;
        $this->write($dom->saveXML());
    }
}

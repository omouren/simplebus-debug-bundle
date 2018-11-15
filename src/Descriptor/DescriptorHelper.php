<?php

namespace Omouren\SimpleBusDebugBundle\Descriptor;

use Symfony\Component\Console\Helper\DescriptorHelper as BaseDescriptorHelper;

/**
 * @author Olivier Mouren <mouren.olivier@gmail.com>
 */
class DescriptorHelper extends BaseDescriptorHelper
{
    public function __construct()
    {
        $this
            ->register('txt', new TextDescriptor())
            ->register('json', new JsonDescriptor())
            ->register('md', new MarkdownDescriptor())
            ->register('xml', new XmlDescriptor())
        ;
    }
}

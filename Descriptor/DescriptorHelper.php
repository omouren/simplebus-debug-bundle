<?php

namespace Omouren\SimpleBusDebugBundle\Descriptor;

use Symfony\Component\Console\Helper\DescriptorHelper as BaseDescriptorHelper;

class DescriptorHelper extends BaseDescriptorHelper
{
    public function __construct()
    {
        $this
            ->register('txt', new TextDescriptor())
        ;
    }
}

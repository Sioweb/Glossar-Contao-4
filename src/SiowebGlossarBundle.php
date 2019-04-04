<?php

namespace Sioweb\Glossar;

use Sioweb\Glossar\Extension\Extension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Configures the Contao Glossar bundle.
 *
 * @author Sascha Weidner <https://www.sioweb.de>
 */
class SiowebGlossarBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function getContainerExtension()
    {
        return new Extension();
    }
}

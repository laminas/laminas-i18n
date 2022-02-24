<?php

declare(strict_types=1);

namespace Laminas\I18n\View\Helper;

use Laminas\View\Helper\HelperInterface;
use Laminas\View\Renderer\RendererInterface;

/**
 * @deprecated since >= 2.15 If it is necessary to access the renderer from within the plugin, you should inject it
 *             as a constructor dependency
 * @internal
 * @psalm-internal \Laminas\I18n\View
 * @psalm-suppress DeprecatedProperty
 */
abstract class AbstractHelper implements HelperInterface
{
    /**
     * View object instance
     *
     * @deprecated since >= 2.15 If it is necessary to access the renderer from within the plugin, you should inject it
     *             as a constructor dependency
     *
     * @var RendererInterface|null
     */
    protected $view;

    /**
     * Set the View object
     *
     * @deprecated since >= 2.15 If it is necessary to access the renderer from within the plugin, you should inject it
     *             as a constructor dependency
     *
     * @return $this
     */
    public function setView(RendererInterface $view)
    {
        $this->view = $view;
        return $this;
    }

    /**
     * Get the view object
     *
     * @deprecated since >= 2.15 If it is necessary to access the renderer from within the plugin, you should inject it
     *             as a constructor dependency
     *
     * @psalm-suppress ImplementedReturnTypeMismatch
     * @return RendererInterface|null
     */
    public function getView()
    {
        return $this->view;
    }
}

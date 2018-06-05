<?php

namespace AppBundle\Traits;

use AppBundle\Service\ManagersCommandService;

/**
 * Implements a setter to inject the 'app.service.managers_command' service.
 */
trait ManagersCommandServiceTrait
{
    /**
     * @var ManagersCommandService $managersCommandService
     */
    private $managersCommandService;

    /**
     * Get $managersCommandService.
     *
     * @return ManagersCommandService
     */
    public function getManagersCommandService()
    {
        return $this->managersCommandService;
    }

    /**
     * Set $managersCommandService.
     *
     * @param ManagersCommandService $managersCommandService  $managersCommandService
     *
     * @return self
     */
    public function setManagersCommandService(ManagersCommandService $managersCommandService)
    {
        $this->managersCommandService = $managersCommandService;

        return $this;
    }
}

<?php

namespace Dekalee\Enom\Query;

use Dekalee\Enom\Facade\FacadeInterface;

/**
 * Interface QueryInterface
 */
interface QueryInterface
{
    /**
     * @param FacadeInterface $facade
     *
     * @return FacadeInterface
     */
    public function execute(FacadeInterface $facade);
}

<?php

declare(strict_types=1);

namespace PoPSchema\Posts\Conditional\Users\Hooks;

use PoP\Engine\Hooks\AbstractHookSet;
use PoPSchema\Users\Conditional\RESTAPI\RouteModuleProcessors\EntryRouteModuleProcessor;

class PostHooks extends AbstractHookSet
{
    const USER_RESTFIELDS = 'posts.id|title|date|url';

    protected function init()
    {
        $this->hooksAPI->addFilter(
            EntryRouteModuleProcessor::HOOK_REST_FIELDS,
            [$this, 'getRESTFields']
        );
    }

    public function getRESTFields($restFields): string
    {
        return $restFields . ',' . self::USER_RESTFIELDS;
    }
}

<?php

namespace App\Infrastructure\EventSubscriber;

use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

/**
 * @author Quentin Boitel <quentin.boitel@outlook.fr>
 */
class RequestSubscriber implements EventSubscriberInterface
{
    use TargetPathTrait;

    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();
        if (
            !$event->isMainRequest() ||
            $request->isXmlHttpRequest() ||
            "app_login" === $request->attributes->get("_route")
        ) {
            return;
        }
        $this->saveTargetPath(
            $request->getSession(),
            "main",
            $request->getUri()
        );
    }

    #[ArrayShape([KernelEvents::REQUEST => "string[]"])]
    public static function getSubscribedEvents(): array
    {
        return [KernelEvents::REQUEST => ["onKernelRequest"]];
    }
}

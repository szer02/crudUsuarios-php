<?php

declare(strict_types=1);

namespace App\Infrastructure\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final readonly class AuthSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private UrlGeneratorInterface $urlGenerator
    ) {}

    public static function getSubscribedEvents(): array
    {
        return [
            // Escuta o evento principal de requisição do Symfony
            KernelEvents::REQUEST => 'onKernelRequest',
        ];
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        // Ignora sub-requisições
        if (!$event->isMainRequest()) {
            return;
        }

        $request = $event->getRequest();
        $route = (string) $request->attributes->get('_route');

        // Rotas públicas que não exigem login
        $publicRoutes = ['app_login', 'app_register'];

        // Libera rotas públicas e rotas internas do Symfony (profiler, assets, etc.)
        if (in_array($route, $publicRoutes, true) || str_starts_with($route, '_')) {
            return;
        }

        // Se tentar acessar qualquer outra página e não tiver a sessão ativa
        if (!$request->getSession()->has('user_auth')) {
            $url = $this->urlGenerator->generate('app_login');
            $event->setResponse(new RedirectResponse($url));
        }
    }
}
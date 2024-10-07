<?php
namespace App\EventSubscriber;

use App\Repository\SectionRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpFoundation\Request;
use Twig\Environment;

class GlobalVariableSubscriber implements EventSubscriberInterface
{
    private $twig;
    private $sectionRepository;

    public function __construct(Environment $twig, SectionRepository $sectionRepository)
    {
        $this->twig = $twig;
        $this->sectionRepository = $sectionRepository;
    }

    private function homeController(array $controller, Request $request): bool{
        if(get_class($controller[0]) !== "App\\Controller\\homeController") return false;
        $sections = $this->sectionRepository->findAll();
        $this->twig->addGlobal('sections', $sections);
        return true;
    }

    private function adminSectionController(array $controller, Request $request): bool{
        if(get_class($controller[0]) !== "App\\Controller\\AdminSectionController") return false;
        $sections = $this->sectionRepository->findAll();
        $this->twig->addGlobal('sections', $sections);
        return true;
    }

    private function adminPostController(array $controller, Request $request): bool{
        if(get_class($controller[0]) !== "App\\Controller\\AdminPostController") return false;
        $sections = $this->sectionRepository->findAll();
        $this->twig->addGlobal('sections', $sections);
        return true;
    }

    private function adminTagController(array $controller, Request $request): bool{
        if(get_class($controller[0]) !== "App\\Controller\\AdminTagController") return false;
        $sections = $this->sectionRepository->findAll();
        $this->twig->addGlobal('sections', $sections);
        return true;
    }

    private function adminController(array $controller, Request $request): bool{
        if(get_class($controller[0]) !== "App\\Controller\\AdminController") return false;
        $sections = $this->sectionRepository->findAll();
        $this->twig->addGlobal('sections', $sections);
        return true;
    }

    private function securityController(array $controller, Request $request): bool{
        if(get_class($controller[0]) !== "App\\Controller\\SecurityController") return false;
        $sections = $this->sectionRepository->findAll();
        $this->twig->addGlobal('sections', $sections);
        return true;
    }

    public function onKernelController(ControllerEvent $event)
    {
        $controller = $event->getController();
        if(is_array($controller)){
            $request = $event->getRequest();
            if($this->homeController($controller, $request)) return;
            if($this->adminSectionController($controller, $request)) return;
            if($this->adminPostController($controller, $request)) return;
            if($this->adminTagController($controller, $request)) return;
            if($this->adminController($controller, $request)) return;
            if($this->securityController($controller, $request)) return;
        }
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::CONTROLLER => 'onKernelController',
        ];
    }
}
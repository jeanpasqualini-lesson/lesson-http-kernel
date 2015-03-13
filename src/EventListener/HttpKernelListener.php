<?php
/**
 * Created by PhpStorm.
 * User: darkilliant
 * Date: 3/13/15
 * Time: 7:18 AM
 */

namespace EventListener;


use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\Event\FinishRequestEvent;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Event\PostResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class HttpKernelListener implements EventSubscriberInterface {
    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * The array keys are event names and the value can be:
     *
     *  * The method name to call (priority defaults to 0)
     *  * An array composed of the method name to call and the priority
     *  * An array of arrays composed of the method names to call and respective
     *    priorities, or 0 if unset
     *
     * For instance:
     *
     *  * array('eventName' => 'methodName')
     *  * array('eventName' => array('methodName', $priority))
     *  * array('eventName' => array(array('methodName1', $priority), array('methodName2'))
     *
     * @return array The event names to listen to
     *
     * @api
     */
    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::REQUEST => array("onKernelRequest", 0),
            KernelEvents::FINISH_REQUEST => array("onKernelFinishRequest", 0),
            KernelEvents::EXCEPTION => array("onKernelException", 0),
            KernelEvents::RESPONSE => array("onKernelResponse", 0),
            KernelEvents::TERMINATE => array("onKernelTerminate", 0),
            KernelEvents::VIEW => array("onKernelView", 0)
        );

        // TODO: Implement getSubscribedEvents() method.
    }

    public function onKernelView(GetResponseForControllerResultEvent $e)
    {
        if(!$e->isMasterRequest()) return;

        $controllerResult = $e->getControllerResult();

        if(!$controllerResult instanceof Response)
        {
            if(is_array($controllerResult))
            {
                $e->setResponse(new JsonResponse($controllerResult));
            }
        }
    }

    public function onKernelRequest(GetResponseEvent $e)
    {
        if($e->isMasterRequest()) return;

        $e->setResponse(new Response("subRequest"));
    }

    public function onKernelFinishRequest(FinishRequestEvent $e)
    {

    }

    public function onKernelException(GetResponseForExceptionEvent $e)
    {
        if(!$e->isMasterRequest()) return;

        $e->setResponse(new Response("ex : ".$e->getException()->getMessage()));
    }

    public function onKernelResponse(FilterResponseEvent $e)
    {
        if(!$e->isMasterRequest()) return;

        $e->setResponse(new Response("<<<<".$e->getResponse()->getContent().">>>>"));
    }

    public function onKernelTerminate(PostResponseEvent $e, $name, EventDispatcherInterface $dispatcher)
    {
        echo "Kernel terminated.".PHP_EOL;
    }

    public function onKernelController(FilterControllerEvent $e)
    {
        if(!$e->isMasterRequest()) return;

        $e->setController(array('Controller\\TestController', 'exceptionAction'));
    }


}
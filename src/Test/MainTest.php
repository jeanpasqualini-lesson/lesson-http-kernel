<?php
/**
 * Created by PhpStorm.
 * User: darkilliant
 * Date: 3/13/15
 * Time: 6:59 AM
 */

namespace Test;


use EventListener\HttpKernelListener;
use Interfaces\TestInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ControllerResolver;
use Symfony\Component\HttpKernel\HttpKernel;
use Symfony\Component\HttpKernel\KernelEvents;

class MainTest implements TestInterface {

    /** @var Request */
    private $request;

    /** @var EventDispatcher */
    private $dispatcher;

    /** @var HttpKernel */
    private $kernel;

    /** @var HttpKernelListener */
    private $kernelListener;

    public function runTest()
    {
        $request = Request::createFromGlobals();

        $dispatcher = new EventDispatcher();

        $resolver = new ControllerResolver();

        $kernel = new HttpKernel($dispatcher, $resolver);

        $this->kernelListener = new HttpKernelListener();

        $this->request = $request;

        $this->dispatcher = $dispatcher;

        $this->kernel = $kernel;

        $this->dispatcher->addSubscriber($this->kernelListener);

        $this->printSeparator("basic response");

        $this->testBasicResponse();

        $this->printSeparator("exception response");

        $this->testExceptionResponse();

        $this->printSeparator("view listener");

        $this->testViewListener();

        $this->printSeparator("sub request");

        $this->testSubRequest();
    }

    private function printSeparator($title)
    {
        echo "=== ".$title." ===".PHP_EOL;
    }

    private function testBasicResponse()
    {
        $this->request->attributes->set("_controller", 'Controller\TestController::testAction');

        $response = $this->kernel->handle($this->request);

        echo $response.PHP_EOL;

        $this->kernel->terminate($this->request, $response);
    }

    private function testExceptionResponse()
    {
        $exceptionListenerHandle = array($this->kernelListener, "onKernelController");

        $this->dispatcher->addListener(KernelEvents::CONTROLLER, $exceptionListenerHandle);

        $response = $this->kernel->handle($this->request);

        $this->dispatcher->removeListener(KernelEvents::CONTROLLER, $exceptionListenerHandle);

        echo $response.PHP_EOL;

        $this->kernel->terminate($this->request, $response);
    }

    private function testViewListener()
    {
        $this->request->attributes->set("_controller", 'Controller\\TestController::nonResponseAction');

        $response = $this->kernel->handle($this->request);

        echo $response.PHP_EOL;
    }

    private function testSubRequest()
    {
        $response = $this->kernel->handle($this->request, HttpKernel::SUB_REQUEST);

        echo $response.PHP_EOL;
    }
}
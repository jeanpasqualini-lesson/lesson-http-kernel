<?php
/**
 * Created by PhpStorm.
 * User: darkilliant
 * Date: 3/13/15
 * Time: 7:14 AM
 */

namespace Controller;


use Symfony\Component\HttpFoundation\Response;

class TestController {

    public function testAction()
    {
        return new Response(__CLASS__." -> ".__METHOD__);
    }

    public function exceptionAction()
    {
        throw new \Exception("une exception");
    }

    public function nonResponseAction()
    {
        return array(
            "un" => 1,
            "deux" => 2,
            "trois" => 3
        );
    }
}
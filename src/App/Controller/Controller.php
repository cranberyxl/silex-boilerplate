<?php

namespace App\Controller;

use Silex\Application;
use Symfony\Component\HttpKernel\Exception\HttpException;

class Controller
{
    protected $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function render($name, array $context = array())
    {
        return $this->app['twig']->render($name, $context);
    }

    public function abort($statusCode, $message = '', array $headers = array())
    {
        $this->app->abort($statusCode, $message, $headers);
    }

    public function json($data = array(), $status = 200, array $headers = array())
    {
        return $this->app->json($data, $status, $headers);
    }

    public function redirect($url, $status = 302)
    {
        return $this->app->redirect($url, $status);
    }

    public function redirectRoute($route, $parameters = array(), $status = 302)
    {
        return $this->redirect($this->generateUrl($route, $parameters), $status);
    }

    public function generateUrl($route, $parameters = array())
    {
        return $this->app['url_generator']->generate($route, $parameters);
    }

    public function stream($callback = null, $status = 200, $headers = array())
    {
        return $this->app->stream($callback, $status, $headers);
    }

    public function createForm($type, $data = null, array $options = array())
    {
        return $this->app['form.factory']->create($type, $data, $options);
    }

    public function createFormBuilder($data = null, array $options = array())
    {
        return $this->app['form.factory']->createBuilder('form', $data, $options);
    }

    public function has($id)
    {
        return isset($this->app[$id]);
    }

    public function get($id)
    {
        return $this->app[$id];
    }

    public function getDocumentManager()
    {
        return $this->app['dm'];
    }

}

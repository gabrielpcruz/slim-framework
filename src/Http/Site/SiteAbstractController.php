<?php

namespace SlimFramework\Http\Site;

use SlimFramework\Http\AbstractController;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Views\Twig;
use SlimFramework\Slim;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

abstract class SiteAbstractController extends AbstractController
{
    /**
     * @param Response $response
     * @param string $template
     * @param array $args
     * @return Response
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function view(
        Response $response,
        string $template,
        array $args = []
    ): Response {
        return Slim::container()->get(Twig::class)->render($response, $template . ".twig", $args);
    }
}

<?php

declare(strict_types=1);

/*
 * This file is part of snow
 *
 * (c) Aurelien Morvan <morvan.aurelien@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AppBundle\Responders;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 * Class WebResponder
 */
class WebResponder
{
    /** @var Environment */
    protected $templating;

    /** @var UrlGeneratorInterface */
    protected $urlGenerator;

    /** @var RequestStack */
    protected $requestStack;

    /**
     * WebResponder constructor.
     *
     * @param Environment           $templating
     * @param UrlGeneratorInterface $urlGenerator
     * @param RequestStack          $requestStack
     */
    public function __construct(
        Environment $templating,
        UrlGeneratorInterface $urlGenerator,
        RequestStack $requestStack
    ) {
        $this->templating = $templating;
        $this->urlGenerator = $urlGenerator;
        $this->requestStack = $requestStack;
    }

    /**
     * @param int     $statusCode
     * @param bool    $isRedirect
     * @param array   $paramsTemplate
     * @param array   $paramRedirect
     *
     * @return RedirectResponse|Response
     *
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function __invoke(
        bool $isRedirect = false,
        array $paramsTemplate = [],
        array $paramRedirect = [],
        int $statusCode = Response::HTTP_OK
    ) {
        $response = $isRedirect ?
            new RedirectResponse(
                $this->urlGenerator->generate(
                    $paramRedirect['name'],
                    array_key_exists('params', $paramRedirect) ? $paramRedirect['params'] : []
                )
            ) :
            new Response(
                $this->templating->render(
                    $paramsTemplate['name'],
                    array_key_exists('params', $paramsTemplate) ? $paramsTemplate['params'] : []
                )
            );

        if (!\is_null($this->requestStack->getCurrentRequest()) &&
            $this->requestStack->getCurrentRequest()->isMethodCacheable()
        ) {
            $response->setMaxAge(3600)
                ->setSharedMaxAge(3600);
        }

        return $response;
    }
}

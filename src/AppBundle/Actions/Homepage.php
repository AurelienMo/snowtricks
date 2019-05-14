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

namespace AppBundle\Actions;

use AppBundle\Responders\WebResponder;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 * Class Homepage
 *
 * @Route("/", name="homepage")
 */
class Homepage
{
    /**
     * @param WebResponder $responder
     *
     * @return RedirectResponse|Response
     *
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function __invoke(WebResponder $responder)
    {
        //TODO Implement logic to get X first tricks
        return $responder(
            false,
            [
                'name' => 'core/home.html.twig',
            ]
        );
    }
}

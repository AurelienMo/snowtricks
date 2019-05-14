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

use Behat\Behat\Context\Context;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\ORM\Tools\ToolsException;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Class DoctrineContext
 */
class DoctrineContext implements Context
{
    /** @var SchemaTool */
    private $schemaTool;

    /** @var RegistryInterface */
    private $doctrine;

    /** @var KernelInterface */
    private $kernel;

    /** @var  */
    private $passwordEncoder;

    /**
     * DoctrineContext constructor.
     *
     * @param RegistryInterface            $doctrine
     * @param KernelInterface              $kernel
     * @param UserPasswordEncoderInterface $passwordEncoder
     */
    public function __construct(
        RegistryInterface $doctrine,
        KernelInterface $kernel,
        UserPasswordEncoderInterface $passwordEncoder
    ) {
        $this->doctrine = $doctrine;
        $this->schemaTool = new SchemaTool($this->doctrine->getManager());
        $this->kernel = $kernel;
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * @BeforeScenario
     *
     * @throws ToolsException
     */
    public function clearDatabase()
    {
        $this->schemaTool->dropSchema($this->doctrine->getManager()->getMetadataFactory()->getAllMetadata());
        $this->schemaTool->createSchema($this->doctrine->getManager()->getMetadataFactory()->getAllMetadata());
    }
    /**
     * @return ObjectManager
     */
    public function getManager()
    {
        return $this->doctrine->getManager();
    }
}

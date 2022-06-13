<?php

namespace Tests\FMT\DomainBundle;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class AbstractDbTest
 * @package Tests\FMT\DomainBundle
 */
abstract class AbstractDbTest extends KernelTestCase
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @return array
     */
    protected function getFixtures(): array
    {
        return [];
    }

    protected function prepareDatabaseSchema()
    {
        // Make sure we are in the test environment
        if ('test' !== self::$kernel->getEnvironment()) {
            throw new \LogicException('Unit tests must be executed on the test environment');
        }

        // Run the schema update tool using our entity metadata
        $metaData = $this->em->getMetadataFactory()->getAllMetadata();
        $schemaTool = new SchemaTool($this->em);
        $schemaTool->updateSchema($metaData);
    }

    protected function loadFixtures()
    {
        $application = new Application(self::$kernel);
        $application->setAutoExit(false);

        $input = new ArrayInput([
            'command' => 'doctrine:fixtures:load',
            '--append',
            '--fixtures' => array_map(
                function (string $fixture) {
                    $filePath = $this->getFixtureFilePath($fixture);
                    if (!file_exists($filePath)) {
                        throw new \InvalidArgumentException(sprintf('Fixture %s does not exist', $fixture));
                    }
                    return $filePath;
                },
                $this->getFixtures()
            ),
        ]);

        $output = new BufferedOutput();
        $input->setInteractive(false);
        $application->run($input, $output);
    }

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        parent::setUp();
        self::bootKernel();

        $this->container = self::$kernel->getContainer();
        $this->em = $this->container->get('doctrine.orm.entity_manager');

        $this->prepareDatabaseSchema();

        if ($this->getFixtures()) {
            $this->loadFixtures();
        }
    }

    /**
     * @param string $fixture
     * @return string|bool
     */
    private function getFixtureFilePath(string $fixture)
    {
        $path = preg_replace('/^Tests\\\/', 'tests\\', $fixture);
        $path = str_replace('\\', '/', $path);

        return realpath(self::$kernel->getRootDir() . '/../' . $path . '.php');
    }
}

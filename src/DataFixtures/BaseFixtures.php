<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

abstract class BaseFixtures extends Fixture
{
    /**
     * @var \Faker\Generator
     */
    protected $faker;

    /**
     * @var ObjectManager
     */
    protected $manager;

    public function load(ObjectManager $manager)
    {
        $this->faker = Factory::create();
        $this->manager = $manager;

        $this->loadData($manager);
    }

    abstract function loadData(ObjectManager $manager);

    protected function create(string $classname, callable $factory)
    {
        $entity = new $classname;
        $factory($entity);

        $this->manager->persist($entity);

        return $entity;
    }

    protected function createMany(string $classname, int $count, callable $factory)
    {
        for ($i = 0; $i < $count;  $i++)
        {
            $entity = $this->create($classname, $factory);

            $this->addReference("$classname|$i", $entity);

        }

        $this->manager->flush();
    }

    private $referencesIndex = [];

    protected function getRandomReference($className)
    {
        if (! isset($this->referencesIndex[$className])) {
            $this->referencesIndex[$className] = [];

            foreach ($this->referenceRepository->getReferences() as $key => $reference) {
                if (strpos($key, $className . '|') === 0) {
                    $this->referencesIndex[$className][] = $key;
                }
            }
        }

        if (empty($this->referencesIndex[$className])) {
            throw new \Exception('Не найдены ссылки на класс: ' . $className);
        }

        return $this->getReference($this->faker->randomElement($this->referencesIndex[$className]));
    }

}

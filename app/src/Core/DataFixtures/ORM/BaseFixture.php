<?php
namespace App\Core\DataFixtures\ORM;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;

/**
 * Базовый класс для Fixtures ORM
 *
 * @psalm-suppress MixedAssignment
 */
abstract class BaseFixture extends Fixture
{
    /**
     * @var Generator Faker
     */
    protected Generator $faker;

    /**
     * @var ObjectManager Object Manager;
     */
    protected ObjectManager $manager;

    /**
     * @inheritdoc
     */
    public function load(ObjectManager $manager): void
    {
        $this->manager = $manager;
        $this->faker = Factory::create('ru_RU');
    }
}

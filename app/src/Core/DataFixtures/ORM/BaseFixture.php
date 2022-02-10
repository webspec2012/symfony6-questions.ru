<?php
namespace App\Core\DataFixtures\ORM;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;

/**
 * Базовый класс для Fixtures ORM
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
    public function load(ObjectManager $manager)
    {
        $this->faker = Factory::create('ru_RU');
        $this->manager = $manager;
    }

    /**
     * Создать одну модуль сущности и сохранить в базе данных
     *
     * @param string $class Название класса сущности
     * @param \Closure $cb Функция для заполнение модели сущности
     * @return void
     */
    protected function createOne(string $class, \Closure  $cb): void
    {
        $this->manager->persist($cb(new $class()));
        $this->manager->flush();
    }

    /**
     * Создать указанное количество сущностей и сохранить в базе данных
     *
     * @param string $class Название класса сущности
     * @param int $count Количество создаваемых объектов
     * @param \Closure $cb Функция для заполнение модели сущности
     * @return void
     */
    protected function createMany(string $class, int $count, \Closure  $cb): void
    {
        for ($n = 0; $n < $count; $n++) {
            $this->manager->persist($cb(new $class(), $n));
            $this->manager->flush();
        }
    }
}

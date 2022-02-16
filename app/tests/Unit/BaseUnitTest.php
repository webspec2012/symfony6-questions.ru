<?php
namespace App\Tests\Unit;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Базовый Unit Test
 */
abstract class BaseUnitTest extends KernelTestCase
{
    /**
     * @return void Загрузка ядра приложения
     */
    protected static function loadKernel(): void
    {
        static::bootKernel();
    }

    /**
     * @return ContainerInterface Контейнер приложения
     */
    protected static function getAppContainer(): ContainerInterface
    {
        return static::getContainer();
    }
}

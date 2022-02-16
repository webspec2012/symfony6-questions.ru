<?php
namespace App\Users\Tests\Service\PasswordGenerate;

use App\Core\Exception\ServiceException;
use App\Tests\Unit\BaseUnitTest;
use App\Users\Service\PasswordGenerate\PasswordGenerateInterface;

/**
 * Password Generate Interface Test
 */
class PasswordGenerateInterfaceTest extends BaseUnitTest
{
    /**
     * @var PasswordGenerateInterface|null Password Generate
     */
    private static ?PasswordGenerateInterface $passwordGenerate;

    /**
     * @inheritdoc
     */
    public static function setUpBeforeClass(): void
    {
        static::$passwordGenerate = static::getAppContainer()->get(PasswordGenerateInterface::class);
    }

    /**
     * @return void Создание случайного пароля без указания длины
     * @throws ServiceException В случае ошибки
     */
    public function testGenerateEmptyLength(): void
    {
        $password = static::$passwordGenerate->generate();

        $this->assertIsString($password);
        $this->assertEquals(8, mb_strlen($password));
    }

    /**
     * @return void Создание случайного пароля заданной длины
     * @throws ServiceException В случае ошибки
     */
    public function testGenerateCustomLength(): void
    {
        $length = 36;
        $password = static::$passwordGenerate->generate($length);

        $this->assertIsString($password);
        $this->assertEquals($length, mb_strlen($password));
    }
}

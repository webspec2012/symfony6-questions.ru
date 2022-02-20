<?php
namespace App\Users\Tests\UseCase\User;

use App\Core\Exception\AppException;
use App\Core\Exception\ServiceException;
use App\Core\Service\Pagination\Paginator;
use App\Core\Service\Pagination\PaginatorInterface;
use App\Tests\Unit\BaseUnitTest;
use App\Users\Dto\User\UserCreateForm;
use App\Users\Dto\User\UserSearchForm;
use App\Users\Entity\UserInterface;
use App\Users\Tests\CreateUserTrait;
use App\Users\UseCase\User\UserCreateCase;
use App\Users\UseCase\User\UserListingCase;

/**
 * User Listing Case Test
 */
class UserListingCaseTest extends BaseUnitTest
{
    use CreateUserTrait;

    /**
     * @var UserListingCase|null $userListingCase User Listing Case
     */
    static private ?UserListingCase $userListingCase;

    /**
     * @inheritdoc
     * @throws ServiceException
     */
    public static function setUpBeforeClass(): void
    {
        static::$userListingCase = static::getAppContainer()->get(UserListingCase::class);

        /* @var UserCreateCase $userCreateCase */
        $userCreateCase = static::getAppContainer()->get(UserCreateCase::class);
        for ($i = 0; $i < 100; $i++) {
            $formData = new UserCreateForm();
            $formData->name = sprintf("Listing User %s", $i);
            $formData->email = sprintf("listing-user-%s@listing-example.com", $i);
            $formData->password = 'password_normal';
            $formData->roles = [
                UserInterface::ROLE_USER,
            ];

            $userCreateCase->create($formData);
        }
    }

    /**
     * @return void Поиск по E-mail c пагинацией
     * @throws AppException В случае ошибки
     */
    public function testSearchWithPaginateByEmail(): void
    {
        $formData = new UserSearchForm();
        $formData->email = 'listing-user-10@listing-example.com';

        $withPaginate = static::$userListingCase->listingWithPaginate($formData);
        $this->assertInstanceOf(PaginatorInterface::class, $withPaginate);

        $withPaginateResults = $this->iterableToArray($withPaginate->getResults());
        $this->assertCount(1, $withPaginateResults);
        $this->assertInstanceOf(UserInterface::class, $withPaginateResults[0] ?? null);
        $this->assertEquals($formData->email, $withPaginateResults[0]?->getEmail());
    }

    /**
     * @return void Поиск по E-mail без пагинации
     * @throws AppException В случае ошибки
     */
    public function testSearchWithoutPaginateByEmail(): void
    {
        $formData = new UserSearchForm();
        $formData->email = 'listing-user-20@listing-example.com';

        $withoutPaginate = static::$userListingCase->listingWithoutPaginate($formData);

        $withoutPaginateResults = $this->iterableToArray($withoutPaginate);
        $this->assertCount(1, $withoutPaginateResults);
        $this->assertInstanceOf(UserInterface::class, $withoutPaginateResults[0] ?? null);
        $this->assertEquals($formData->email, $withoutPaginateResults[0]?->getEmail());
    }

    /**
     * @return void Поиск с ошибкой валидации orderBy
     * @throws ServiceException В случае ошибки
     */
    public function testSearchWithFailedValidateOrderBy(): void
    {
        $formData = new UserSearchForm();
        $formData->orderBy = 'followers';

        $this->expectException(ServiceException::class);
        $this->expectExceptionMessage(sprintf("Направление сортировки '%s' не поддерживается", $formData->orderBy));

        static::$userListingCase->listingWithPaginate($formData);
    }

    /**
     * Iterable to Array
     *
     * @param iterable $iterable
     * @return object[]
     */
    private function iterableToArray(iterable $iterable): array
    {
        $items = [];
        foreach ($iterable as $item) {
            $items[] = $item;
        }

        return $items;
    }
}

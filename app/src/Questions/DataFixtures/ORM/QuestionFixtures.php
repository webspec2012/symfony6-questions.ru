<?php
namespace App\Questions\DataFixtures\ORM;

use App\Core\DataFixtures\ORM\BaseFixture;
use App\Core\Exception\AppException;
use App\Core\Exception\ServiceException;
use App\Questions\Dto\Answer\AnswerCreateForm;
use App\Questions\Dto\Category\CategoryCreateForm;
use App\Questions\Dto\Question\QuestionCreateForm;
use App\Questions\Entity\Category\CategoryInterface;
use App\Questions\UseCase\Answer\AnswerCreateCase;
use App\Questions\UseCase\Category\CategoryCreateCase;
use App\Questions\UseCase\Category\CategorySwitchStatusCase;
use App\Questions\UseCase\Question\QuestionCreateCase;
use Doctrine\Persistence\ObjectManager;

/**
 * Question Fixtures
 *
 * @psalm-suppress PropertyNotSetInConstructor
 */
final class QuestionFixtures extends BaseFixture
{
    /**
     * @var CategoryCreateCase Category Create Case
     */
    private CategoryCreateCase $categoryCreateCase;

    /**
     * @var CategorySwitchStatusCase Category Switch Status Case
     */
    private CategorySwitchStatusCase $categorySwitchStatusCase;

    /**
     * @var QuestionCreateCase Question Create Case
     */
    private QuestionCreateCase $questionCreateCase;

    /**
     * @var AnswerCreateCase Answer Create Case
     */
    private AnswerCreateCase $answerCreateCase;

    /**
     * @var CategoryInterface[] Categories
     */
    private array $categories = [];

    /**
     * Конструктор класса
     *
     * @param CategoryCreateCase $categoryCreateCase Category Create Case
     * @param CategorySwitchStatusCase $categorySwitchStatusCase Category Switch Status Case
     * @param QuestionCreateCase $questionCreateCase Question Create Case
     * @param AnswerCreateCase $answerCreateCase Answer Create Case
     *
     * @return void
     */
    public function __construct(
        CategoryCreateCase $categoryCreateCase,
        CategorySwitchStatusCase $categorySwitchStatusCase,
        QuestionCreateCase $questionCreateCase,
        AnswerCreateCase $answerCreateCase,
    )
    {
        $this->categoryCreateCase = $categoryCreateCase;
        $this->categorySwitchStatusCase = $categorySwitchStatusCase;
        $this->questionCreateCase = $questionCreateCase;
        $this->answerCreateCase = $answerCreateCase;
    }

    /**
     * @inheritdoc
     * @throws AppException
     */
    public function load(ObjectManager $manager): void
    {
        parent::load($manager);

        // загрузка Categories
        $this->loadCategories();

        // загрузка Questions & Answers
        $this->loadQuestions();
    }

    /**
     * Загрузка категорий к вопросам
     *
     * @throws ServiceException
     */
    private function loadCategories(): void
    {
        for ($i = 0; $i < 10; $i++) {
            $formData = new CategoryCreateForm();
            $formData->title = sprintf("Category #%s", $i+1);
            $formData->slug = sprintf("category-%s", $i+1);

            $category = $this->categoryCreateCase->create($formData);
            $this->categories[] = $category;

            $this->categorySwitchStatusCase->publish($category->getId());
        }
    }

    /**
     * Загрузка вопросов и ответов
     *
     * @throws ServiceException
     */
    private function loadQuestions(): void
    {
        $totalCategories = count($this->categories);
        for ($i = 0; $i < 100; $i++) {
            $formData = new QuestionCreateForm();
            $formData->category = $this->categories[rand(0, $totalCategories - 1)]->getId();
            $formData->title = sprintf("Title #%s", $i+1);
            $formData->text = $this->faker->realText();

            $question = $this->questionCreateCase->create($formData);
            $totalAnswers = rand(0, 20);
            for ($j = 0; $j < $totalAnswers; $j++) {
                $formData = new AnswerCreateForm();
                $formData->question = $question->getId();
                $formData->text = $this->faker->realText();

                $this->answerCreateCase->create($formData);
            }
        }
    }
}

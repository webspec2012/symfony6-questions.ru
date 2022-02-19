<?php
namespace App\Questions\Form\Question;

use App\Core\Exception\ServiceException;
use App\Questions\Dto\Question\QuestionCreateForm;
use App\Questions\UseCase\Category\CategoryListingCase;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Форма для создания вопроса
 */
final class QuestionCreateFormType extends AbstractType
{
    /**
     * @var CategoryListingCase Category Listing Case
     */
    private CategoryListingCase $categoryListingCase;

    /**
     * Конструктор
     *
     * @param CategoryListingCase $categoryListingCase Category Listing Case
     */
    public function __construct(
        CategoryListingCase $categoryListingCase,
    )
    {
        $this->categoryListingCase = $categoryListingCase;
    }

    /**
     * @inheritdoc
     * @throws ServiceException
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('category', ChoiceType::class, [
                'label' => 'Категория',
                'choices' => array_flip($this->categoryListingCase->getCategoriesForDropdown(true)),
                'placeholder' => '-- Выберите категорию --',
                'required' => true,
            ])
            ->add('title', TextType::class, [
                'label' => 'Заголовок',
                'required' => true,
            ])
            ->add('text', TextareaType::class, [
                'label' => 'Текст',
                'required' => false,
            ])
        ;
    }

    /**
     * @inheritdoc
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => QuestionCreateForm::class,
        ]);
    }
}

<?php
namespace App\Questions\Form\Question;

use App\Core\Exception\ServiceException;
use App\Questions\Dto\Question\QuestionSearchForm;
use App\Questions\Entity\Question\Question;
use App\Questions\UseCase\Category\CategoryListingCase;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Форма поиска по вопросам
 */
final class QuestionSearchFormType extends AbstractType
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
            ->setMethod('GET')
            ->add('orderBy', ChoiceType::class, [
                'label' => 'Сортировка по',
                'choices' => array_flip(QuestionSearchForm::getAvailableOrderBy()),
                'empty_data' => 'u.id_DESC',
                'attr' => [
                    'onchange' => 'this.form.submit()',
                ],
            ])
            ->add('id', TextType::class, [
                'label' => 'ID',
                'required' => false,
            ])
            ->add('status', ChoiceType::class, [
                'label' => 'Статус',
                'required' => false,
                'choices' => Question::getStatusList(),
                'attr' => [
                    'onchange' => 'this.form.submit()',
                ],
            ])
            ->add('category', ChoiceType::class, [
                'label' => 'Категория',
                'choices' => array_flip($this->categoryListingCase->getCategoriesForDropdown(false)),
                'required' => false,
                'attr' => [
                    'onchange' => 'this.form.submit()',
                ],
            ])
            ->add('query', TextType::class, [
                'label' => 'Поисковой запрос',
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
            'data_class' => QuestionSearchForm::class,

            // enable/disable CSRF protection for this form
            'csrf_protection' => false,
        ]);
    }
}

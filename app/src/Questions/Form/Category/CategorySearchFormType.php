<?php
namespace App\Questions\Form\Category;

use App\Questions\Dto\Category\CategorySearchForm;
use App\Questions\Entity\Category\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Форма поиска по категориям
 */
final class CategorySearchFormType extends AbstractType
{
    /**
     * @inheritdoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->setMethod('GET')
            ->add('orderBy', ChoiceType::class, [
                'label' => 'Сортировка по',
                'choices' => array_flip(CategorySearchForm::getAvailableOrderBy()),
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
                'choices' => Category::getStatusList(),
                'attr' => [
                    'onchange' => 'this.form.submit()',
                ],
            ])
            ->add('title', TextType::class, [
                'label' => 'Название',
                'required' => false,
            ])
            ->add('slug', TextType::class, [
                'label' => 'Slug',
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
            'data_class' => CategorySearchForm::class,

            // enable/disable CSRF protection for this form
            'csrf_protection' => false,
        ]);
    }
}

<?php
namespace App\Questions\Form\Question;

use App\Questions\Dto\Question\QuestionUpdateForm;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Форма для редактирования вопроса
 */
final class QuestionUpdateFormType extends AbstractType
{
    /**
     * @inheritdoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('category', ChoiceType::class, [
                'label' => 'Категория',
                'choices' => [],
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
            'data_class' => QuestionUpdateForm::class,
        ]);
    }
}

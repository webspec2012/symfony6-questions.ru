<?php
namespace App\Questions\Form\Answer;

use App\Questions\Dto\Answer\AnswerSearchForm;
use App\Questions\Dto\Question\QuestionSearchForm;
use App\Questions\Entity\Answer\Answer;
use App\Questions\Entity\Question\Question;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Форма поиска по ответам
 */
final class AnswerSearchFormType extends AbstractType
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
                'choices' => array_flip(AnswerSearchForm::getAvailableOrderBy()),
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
                'choices' => Answer::getStatusList(),
                'attr' => [
                    'onchange' => 'this.form.submit()',
                ],
            ])
            ->add('question', TextType::class, [
                'label' => 'ID вопроса',
                'required' => false,
            ])
            ->add('text', TextType::class, [
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
            'data_class' => AnswerSearchForm::class,

            // enable/disable CSRF protection for this form
            'csrf_protection' => false,
        ]);
    }
}

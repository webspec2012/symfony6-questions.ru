<?php
namespace App\Users\Form\User;

use App\Users\Dto\User\UserProfileUpdateForm;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Форма для редактирования профиля пользователя
 */
final class UserProfileUpdateFormType extends AbstractType
{
    /**
     * @inheritdoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Имя',
                'required' => true,
            ])
            ->add('email', EmailType::class, [
                'label' => 'E-mail',
                'required' => true,
            ])
            ->add('about', TextareaType::class, [
                'label' => 'О себе',
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
            'data_class' => UserProfileUpdateForm::class,
        ]);
    }
}

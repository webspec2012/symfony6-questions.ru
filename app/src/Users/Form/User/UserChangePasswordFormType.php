<?php
namespace App\Users\Form\User;

use App\Users\Dto\User\UserChangePasswordForm;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Форма изменения пароля пользователю
 */
final class UserChangePasswordFormType extends AbstractType
{
    /**
     * @inheritdoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'first_options' => [
                    'label' => 'Новый пароль',
                ],
                'second_options' => [
                    'label' => 'Повтор пароля',
                ],
                'invalid_message' => 'Указанные пароли не совпадают',
            ])
        ;
    }

    /**
     * @inheritdoc
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => UserChangePasswordForm::class,
        ]);
    }
}

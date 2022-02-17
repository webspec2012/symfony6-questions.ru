<?php
namespace App\Users\Form\User;

use App\Users\Dto\User\UserRegistrationForm;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Форма для регистрации пользователя
 */
final class UserRegistrationFormType extends AbstractType
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
            ->add('password', PasswordType::class, [
                'label' => 'Пароль',
                'required' => true,
            ])
        ;
    }

    /**
     * @inheritdoc
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => UserRegistrationForm::class,
        ]);
    }
}

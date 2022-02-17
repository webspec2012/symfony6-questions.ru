<?php
namespace App\Users\Form\User;

use App\Users\Dto\User\UserUpdateForm;
use App\Users\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Форма для редактирования пользователя
 */
final class UserUpdateFormType extends AbstractType
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
            ->add('is_admin', CheckboxType::class, [
                'label' => 'Администратор?',
                'required' => false,
            ])
            ->add('roles', ChoiceType::class, [
                'label' => 'Роли',
                'choices' => User::getRolesList(),
                'multiple' => true,
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
            'data_class' => UserUpdateForm::class,
        ]);
    }
}

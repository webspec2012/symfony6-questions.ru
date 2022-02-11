<?php
namespace App\Users\Form\User;

use App\Users\Dto\User\UserSearchForm;
use App\Users\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Форма поиска пользователей
 */
final class UserSearchFormType extends AbstractType
{
    /**
     * @inheritdoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->setMethod('GET')
            ->add('orderBy', ChoiceType::class, [
                'label' => 'Сортировка по',
                'choices' => array_flip(UserSearchForm::getAvailableOrderBy()),
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
                'choices' => User::getStatusList(),
                'attr' => [
                    'onchange' => 'this.form.submit()',
                ],
            ])
            ->add('name', TextType::class, [
                'label' => 'Имя пользователя',
                'required' => false,
            ])
            ->add('email', TextType::class, [
                'label' => 'E-mail',
                'required' => false,
            ])
            ->add('role', ChoiceType::class, [
                'label' => 'Роль',
                'choices' => User::getRolesList(),
                'required' => false,
                'attr' => [
                    'onchange' => 'this.form.submit()',
                ],
            ])
        ;
    }

    /**
     * @inheritdoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => UserSearchForm::class,

            // enable/disable CSRF protection for this form
            'csrf_protection' => false,
        ]);
    }
}

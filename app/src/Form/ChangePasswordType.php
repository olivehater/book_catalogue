<?php
/**
 * Change password type.
 */

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ChangePasswordType extends AbstractType
{
    /**
     * Builds form.
     *
     * @param \Symfony\Component\Form\FormBuilderInterface $builder Form builder interface
     * @param array $options The options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'password',
            RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'Hasła nie są takie same',
                'required' => true,
                'first_options' => ['label' => 'label_password_star'],
                'second_options' => ['label' => 'label_repeat_password_star'],
            ]
        );
    }

    /**
     * Configures options.
     *
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver Resolver for the options
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => User::class]);
    }

    /**
     * Gets the prefix.
     *
     * @return string Result
     */
    public function getBlockPrefix(): string
    {
        return 'user_change_password';
    }

}
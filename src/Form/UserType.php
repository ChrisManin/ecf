<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username')
            ->add('email')
            ->add('password', \Symfony\Component\Form\Extension\Core\Type\RepeatedType::class,
                    ['type' => \Symfony\Component\Form\Extension\Core\Type\PasswordType::class,
                     'invalid_message' => 'The password fields must match.',
                     'options' => ['attr' => ['class' => 'password-field']],
                     'required' => true,
                     'first_options'  => ['label' => 'Password'],
                     'second_options' => ['label' => 'Repeat Password'],
                ])
            ->add('Confirm', \Symfony\Component\Form\Extension\Core\Type\SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}

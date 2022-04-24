<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RegisterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('pseudo', TextType::class, [
                'label' => 'Votre pseudo :',
                'attr' => [
                    'placeholder' => 'Jdupt'
                ]
            ])
            ->add('firstname', TextType::class, [
                'label' => 'Votre prénom :',
                'attr' => [
                    'placeholder' => 'Jean'
                ]
            ])
            ->add('lastname', TextType::class, [
                'label' => 'Votre nom :',
                'attr' => [
                    'placeholder' => 'Dupont'
                ]
            ])
            ->add('email', EmailType::class, [
                'label' => 'Votre email :',
                'invalid_message' => 'Veuillez à bien remplir ce champ',
                'attr' => [
                    'placeholder' => 'Jean.Dupont@gmail.com'
                ]
            ])
            ->add('password', RepeatedType::class, [
                'type'=> PasswordType::class,
                'invalid_message'=> 'Le mot de passe et la confirmation doivent être identique',
                'label' => 'Mot de passe :',
                'required' => true,
                'first_options' => [
                    'label' => 'Mot de passe :',
                    'attr' => [
                        'placeholder' => 'Merci de saisir votre mot de passe.'
                    ]
                ],
                'second_options' => [
                    'label' => 'Mot de passe :',
                    'attr' => [
                        'placeholder' => 'Merci de confirmez votre mot de passe.'
                    ]
                ]
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Envoyer'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}

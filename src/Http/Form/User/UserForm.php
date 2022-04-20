<?php

namespace App\Http\Form\User;

use App\Domain\Auth\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Quentin Boitel <quentin.boitel@outlook.fr>
 */
class UserForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add("username", TextType::class, [
                "label" => "Username",
                "required" => true,
            ])
            ->add("password", RepeatedType::class, [
                "type" => PasswordType::class,
                "first_options" => [
                    "label" => "Password",
                    "attr" => [
                        "autocomplete" => "new-password",
                    ],
                ],
                "second_options" => [
                    "label" => "Repeat Password",
                    "attr" => [
                        "autocomplete" => "new-password",
                    ],
                ],
            ])
            ->add("email", TextType::class, [
                "label" => "Email",
                "required" => true,
                "attr" => [
                    "autocomplete" => "email",
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            "data_class" => User::class,
            "translation_domain" => "user",
        ]);
    }
}

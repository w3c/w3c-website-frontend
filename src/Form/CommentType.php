<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;

class CommentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label'              => 'blog.comments.form.name.label',
                'constraints'        => [new NotBlank(['message' => 'blog.comments.form.name.blank'])],
            ])
            ->add('email', EmailType::class, [
                'label'              => 'blog.comments.form.email.label',
                'constraints'        => [
                    new NotBlank(['message' => 'blog.comments.form.email.blank']),
                    new Email(['message' => 'blog.comments.form.email.format'])
                ],
            ])
            ->add('comment', TextareaType::class, [
                'label'              => 'blog.comments.form.comment.label',
                'help'               => 'blog.comments.form.comment.help',
                'constraints'        => [new NotBlank(['message' => 'blog.comments.form.comment.blank'])],
            ])
            ->add('post', HiddenType::class, [
                'constraints' => [new NotBlank(['message' => 'blog.comments.form.post.blank'])]
            ])
            ->add('parent', HiddenType::class, ['required'    => false]);
    }

    public function getBlockPrefix()
    {
        return '';
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'csrf_protection' => false
        ]);
    }
}

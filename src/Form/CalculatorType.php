<?php
namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;



class CalculatorType extends AbstractType
{
public function buildForm(FormBuilderInterface $builder, array $options)
{
$builder
->add('firstArgument', NumberType::class, array('attr'=>['class'=>'form-control']))
->add('operation', ChoiceType::class, [
'choices' => [
'+' => '+',
'-' => '-',
'*' => '*',
'/' => '/',
],'attr'=>['class'=>'form-control']
])
->add('secondArgument', NumberType::class, array('attr'=>['class'=>'form-control']))
->add('calculate', ButtonType::class, array('attr'=>['class'=>'btn btn-primary']))
    ->add('save', ButtonType::class, array('attr'=>['class'=>'btn btn-danger']))
    ->add('restore', ButtonType::class, array('attr'=>['class'=>'btn btn-success']));
}

public function configureOptions(OptionsResolver $resolver)
{
$resolver->setDefaults([
'data_class' => CalculatorData::class,
]);
}
}
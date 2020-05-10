<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Doctrine\ORM\EntityRepository;

class ComercialType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('last_name')
            ->add('email')
            ->add('phone')
            ->add('company', 'entity',array(
                    'class' => 'AppBundle:Company',
                    'required'  => false,
                    'query_builder' => function(EntityRepository $er){
                        return $er->createQueryBuilder('c')
                              ->select(array('c'))
                            ->orderBy("c.name");
                    }

            ))
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Comercial'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'appbundle_comercial';
    }
}

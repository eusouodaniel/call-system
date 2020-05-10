<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;

class SLAType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('item', 'entity',array(
                    'class' => 'AppBundle:Item',
                    'query_builder' => function(EntityRepository $er){
                        return $er->createQueryBuilder('i')
                              ->select(array('i'))
                            ->orderBy("i.name");
                    }
            ))
            ->add('attendance', 'entity',array(
                    'class' => 'AppBundle:Attendance',
                    'query_builder' => function(EntityRepository $er){
                        return $er->createQueryBuilder('a')
                              ->select(array('a'))
                            ->orderBy("a.name");
                    }
                ))
            ->add('hour', 'number');
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\SLA'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'appbundle_sla';
    }
}

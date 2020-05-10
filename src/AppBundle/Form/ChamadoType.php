<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;
use AppBundle\Entity\StatusChamado;
use AppBundle\Entity\Attendance;

class ChamadoType extends AbstractType
{

    protected $type;

    public function __construct($type = null) {
        $this->type = $type;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $type = $this->type;

        $builder
            ->add('description')
            ->add('dtEnd', 'date', array(
                'empty_value' => '',
                'widget' => 'single_text',
                'format' => 'dd/MM/yyyy',
                'attr' => array('class'=>'form-control date-picker'),
                'required' => false
            ))
            ->add('dtLimit', 'date', array(
                'empty_value' => '',
                'widget' => 'single_text',
                'format' => 'dd/MM/yyyy',
                'attr' => array('class'=>'form-control date-picker'),
                'required' => false
            ))
            ->add('conclusionEnd')
            ->add('telphone')
            ->add('phone')
            ->add('responsible', 'entity',array(
                    'class' => 'UserBundle:User',
                    'query_builder' => function(EntityRepository $er){
                        return $er->createQueryBuilder('u')
                              ->select(array('u'))
                              ->where('u.roles LIKE :roles')
                              ->andWhere('u.enabled = true')
                              ->setParameter('roles', '%"ROLE_SUPER_ADMIN"%')
                              ->orderBy('u.name');
                    },
                    'required' => false
            ))
            ->add('enterprise', 'entity', array(
                'class' => 'AppBundle:Company',
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('c')
                          ->select(array('c'))
                          ->orderBy('c.name');
                },
            ))
            ->add('file', 'hidden', array())
            ->add('fileTemp', 'file', array(
                'label' => 'Arquivo',
                'required' => false
            ))
            ->add('item', 'entity',array(
                    'class' => 'AppBundle:Item',
                    'query_builder' => function(EntityRepository $er){
                        return $er->createQueryBuilder('i')
                              ->select(array('i'))
                            ->orderBy("i.name");
                    },
                    'required' => false
            ))
            ->add('status', 'entity',array(
                    'class' => 'AppBundle:StatusChamado',
                    'query_builder' => function(EntityRepository $er){
                        return $er->createQueryBuilder('sc')
                              ->select(array('sc'))
                              ->andWhere("sc.id != ".StatusChamado::CONCLUIDO)
                              ->andWhere("sc.id != ".StatusChamado::CANCELADO)
                            ->orderBy("sc.name");
                    }
                ));
            if($type == "COMERCIAL"){
              $builder->add('attendance', 'entity',array(
                      'class' => 'AppBundle:Attendance',
                      'query_builder' => function(EntityRepository $er){
                          return $er->createQueryBuilder('a')
                                ->select(array('a'))
                                ->andWhere("a.attendance_profile = ".Attendance::COMERCIAL)
                              ->orderBy("a.name");
                      },
                      'required' => false
                  ));
            }elseif($type == "CLIENTE") {
              $builder->add('attendance', 'entity',array(
                      'class' => 'AppBundle:Attendance',
                      'query_builder' => function(EntityRepository $er){
                          return $er->createQueryBuilder('a')
                                ->select(array('a'))
                                ->andWhere("a.attendance_profile = ".Attendance::CLIENTE)
                              ->orderBy("a.name");
                      },
                      'required' => false
                  ));
            }else{
              $builder->add('attendance', 'entity',array(
                      'class' => 'AppBundle:Attendance',
                      'query_builder' => function(EntityRepository $er){
                          return $er->createQueryBuilder('a')
                                ->select(array('a'))
                              ->orderBy("a.name");
                      },
                      'required' => false
                  ));
            }
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Chamado'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'appbundle_chamado';
    }
}

<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\ORM\EntityRepository;
use AppBundle\Entity\Attendance;

class ChamadoSearchType extends AbstractType
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
            ->add('id', 'integer', array('required'  => false,))
            ->add('client', 'entity',array(
                    'class' => 'AppBundle:Client',
                    'choice_label' => 'name',
                    'required'  => false,
                    'query_builder' => function(EntityRepository $er){
                        return $er->createQueryBuilder('c')
                              ->select(array('c'))
                            ->orderBy("c.name, c.last_name");
                    }

            ))
            ->add('company', 'entity',array(
                    'class' => 'AppBundle:Company',
                    'choice_label' => 'name',
                    'required'  => false,
                    'query_builder' => function(EntityRepository $er){
                        return $er->createQueryBuilder('c')
                              ->select(array('c'))
                            ->orderBy("c.name");
                    }

            ))
            ->add('user', 'entity',array(
                    'class' => 'UserBundle:User',
                    'choice_label' => 'name',
                    'required'  => false,
                    'query_builder' => function(EntityRepository $er){
                        return $er->createQueryBuilder('u')
                              ->select(array('u'))
                              ->andWhere('u INSTANCE OF :user')
                              ->setParameter('user', 'user')
                              ->andWhere('u.enabled = true')
                            ->orderBy("u.name, u.last_name");
                    }

            ))
            ->add('begin_date', 'date', array(
                'empty_value' => '',
                'widget' => 'single_text',
                'format' => 'dd/MM/yyyy',
                'attr' => array('class'=>'form-control date-picker'),
                'required' => false
            ))
            ->add('end_date', 'date', array(
                'empty_value' => '',
                'widget' => 'single_text',
                'format' => 'dd/MM/yyyy',
                'attr' => array('class'=>'form-control date-picker'),
                'required' => false
            ))
            ->add('status', 'entity',array(
                    'class' => 'AppBundle:StatusChamado',
                    'required'  => false,
                    'query_builder' => function(EntityRepository $er){
                        return $er->createQueryBuilder('sc')
                              ->select(array('sc'))
                            ->orderBy("sc.name");
                    }
            ))
            ->add('type','choice', array(
                        'choices' => array ('' => 'Selecione uma opção',
                            'PF' => 'Pessoa Física',
                            'PJ' => 'Pessoa Jurídica',),
                            'required' => true,))
            ->add('item', 'entity',array(
                    'class' => 'AppBundle:Item',
                    'required'  => false,
                    'query_builder' => function(EntityRepository $er){
                        return $er->createQueryBuilder('i')
                              ->select(array('i'))
                            ->orderBy("i.name");
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


   public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
          'data_class' => 'AppBundle\Entity\ChamadoSearch',
          'csrf_protection' => false,
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'chamado_search';
    }
}

<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;

class CompanyType extends AbstractType
{

    protected $company;

    public function __construct($company = null) {
        $this->company = $company;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $company = $this->company;

        $builder
            ->add('name')
            ->add('email')
            ->add('telphone')
            ->add('cpf')
            ->add('cnpj')
            ->add('address')
            ->add('city')
            ->add('cep')
            ->add('contract','choice', array(
                        'choices' => array ('' => 'Selecione uma opção',
                            'Contrato' => 'Contrato',
                            'Avulsa' => 'Avulsa',),
                            'required' => true,))
            ->add('type','choice', array(
                        'choices' => array ('' => 'Selecione uma opção',
                            'PF' => 'Pessoa Física',
                            'PJ' => 'Pessoa Jurídica',),
                            'required' => true,))
            ->add('uf','choice', array(
                        'choices' => array ('' => 'UF',
                            'AC' => 'AC',
                            'AL' => 'AL',
                            'AP' => 'AP',
                            'AM' => 'AM',
                            'BA' => 'BA',
                            'CE' => 'CE',
                            'DF' => 'DF',
                            'ES' => 'ES',
                            'GO' => 'GO',
                            'MA' => 'MA',
                            'MT' => 'MT',
                            'MS' => 'MS',
                            'MG' => 'MG',
                            'PR' => 'PR',
                            'PB' => 'PB',
                            'PA' => 'PA',
                            'PE' => 'PE',
                            'PI' => 'PI',
                            'RJ' => 'RJ',
                            'RN' => 'RN',
                            'RS' => 'RS',
                            'RO' => 'RO',
                            'RR' => 'RR',
                            'SC' => 'SC',
                            'SE' => 'SE',
                            'SP' => 'SP',
                            'TO' => 'TO',),
                            'required' => true,));
            if($company!=null){
              $builder->add('user', 'entity', array(
                  'class' => 'UserBundle:User',
                  'empty_value' => "",
                  'required' => false,
                  'query_builder' => function(EntityRepository $er) use ($company) {
                      return $er->createQueryBuilder('u')
                              //->andWhere("u.company = :company")
                              //->setParameter("company", $company)
                              ->orderBy('u.name', 'ASC');
                  },
              ));

            }else{
              $builder->add('user');
            }
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Company'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'appbundle_company';
    }
}

<?php

namespace PKP\Bundle\AdminBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

class PartySubscriptionAdmin extends Admin
{
    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('party')
            ->add('user')
            ->add('post')
            ->add('description')
            ->add('accepted')
            ->add('updated')
            ->add('created')
        ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('party')
            ->add('user')
            ->add('post')
            ->add('description')
            ->add('accepted')
            ->add('updated')
            ->add('created')
            ->add('_action', 'actions', array(
                'actions' => array(
                    'show' => array(),
                    'edit' => array(),
                    'delete' => array(),
                )
            ))
        ;
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('party')
            ->add('user')
            ->add('post')
            ->add('description')
            ->add('accepted',null, array("required" => false))
            ->add('updated')
            ->add('created')
        ;
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('party')
            ->add('user')
            ->add('post')
            ->add('description')
            ->add('accepted')
            ->add('updated')
            ->add('created')
        ;
    }
}

<?php

namespace PKP\Bundle\ApiBundle\Controller;

use FOS\RestBundle\View\View;
use JMS\DiExtraBundle\Annotation as DI;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use FOS\RestBundle\Routing\ClassResourceInterface;

class UserController implements ClassResourceInterface
{

    /** @DI\Inject("doctrine.orm.entity_manager") */
    protected $em;

    /**
     * Cargar la lista de usuarios
     *
     * @ApiDoc(
     *  resource=true,
     *  description="Cargar la lista de usuarios"
     * )
     */
    public function cgetAction(){
        $view = View::create();
        $data = array();
        foreach($this->em->getRepository("ApplicationSonataUserBundle:User")->findAll() as $user){
            $data[] = array(
                "id" => $user->getId(),
                "username" => $user->getUsername()

            );
        }
        $view->setData($data);
        return $view;
    }

    /**
     * Actualizar datos del usuario
     *
     * @ApiDoc(
     *  resource=true,
     *  description="Actualizar datos del usuario"
     * )
     */
    public function updateAction(){
        $view = View::create();
        $data = array();
        foreach($this->em->getRepository("ApplicationSonataUserBundle:User")->findAll() as $user){
            $data[] = array(
                "id" => $user->getId(),
                "username" => $user->getUsername()

            );
        }
        $view->setData($data);
        return $view;
    }

    public function getAction($slug)
    {} // "get_user"      [GET] /users/{slug}

    // ...
    public function getCommentsAction($slug)
    {} // "get_user_comments"    [GET] /users/{slug}/comments

    // ...
}
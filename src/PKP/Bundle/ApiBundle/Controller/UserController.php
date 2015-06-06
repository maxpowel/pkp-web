<?php

namespace PKP\Bundle\ApiBundle\Controller;

use FOS\RestBundle\View\View;
use JMS\DiExtraBundle\Annotation as DI;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use FOS\RestBundle\Routing\ClassResourceInterface;
use PKP\Bundle\AuthBundle\Form\Model\UserRegister;
use PKP\Bundle\AuthBundle\Form\UserRegisterType;
use PKP\Bundle\AuthBundle\Form\UserType;
use PKP\Bundle\AuthBundle\Form\Model\User;
use FOS\RestBundle\Controller\FOSRestController as Controller;
use FOS\RestBundle\Controller\Annotations\Route;

class UserController extends Controller implements ClassResourceInterface
{

    /** @DI\Inject("doctrine.orm.entity_manager") */
    protected $em;

    /**
     * Cargar la lista de usuarios
     *
     * @ApiDoc(
     *  resource="User",
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
     *  resource="User",
     *  description="Actualizar datos del usuario"
     * )
     */
    public function putAction(){
        $form = $this->createForm(new UserType(), new User());
        $form->submit($this->get('request'));


        if ($form->isValid()) {

            $userModel = $form->getData();
            $userManager = $this->get('fos_user.user_manager');
            $user = $this->getUser();
            if($userModel->username)
                $user->setUsername($userModel->username);

            if($userModel->firstname)
                $user->setFirstname($userModel->firstname);

            if($userModel->lastname)
                $user->setLastname($userModel->lastname);

            if($userModel->email)
                $user->setEmail($userModel->email);

            if($userModel->password)
                $user->setPlainPassword($userModel->password);


            $userManager->updateUser($user);
            return $this->view(array("ok" => true));
        }

        return $this->view($form, 400);
    }

    /**
     * Obtener información del  usuario
     *
     * @ApiDoc(
     *  resource="User",
     *  description="Obtener información del usario"
     * )
     */
    public function getAction(){
        return $this->view($this->serializeUser($this->getUser()));
    }


    /**
     * Crear un usuario
     *
     * @ApiDoc(
     *  resource="User",
     *  description="Crear un usuario"
     * )
     * @Route("/user/new")
     */
    public function postCreateAction(){
        $form = $this->createForm(new UserRegisterType(), new UserRegister());
        $form->submit($this->get('request'));


        if ($form->isValid()) {

            $userModel = $form->getData();
            $userManager = $this->get('fos_user.user_manager');
            $user = $userManager->createUser();
            $user->setUsername($userModel->username);
            $user->setFirstname($userModel->firstname);
            $user->setLastname($userModel->lastname);
            $user->setEmail($userModel->email);
            $user->setPlainPassword($userModel->password);
            $user->setEnabled(true);


            $userManager->updateUser($user);
            return $this->view(array("ok" => true));
        }

        return $this->view($form, 400);
    }


    private function serializeUser($user){
        return array(
            "id" => $user->getId(),
            "username" => $user->getUsername(),
            "firstname" => $user->getFirstname(),
            "lastname" => $user->getLastname(),
            "email" => $user->getEmail()
        );
    }
}
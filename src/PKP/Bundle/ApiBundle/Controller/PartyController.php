<?php

namespace PKP\Bundle\ApiBundle\Controller;

use FOS\RestBundle\View\View;
use JMS\DiExtraBundle\Annotation as DI;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use FOS\RestBundle\Routing\ClassResourceInterface;
use PKP\Bundle\LanPartyBundle\Entity\PartySubscription;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class PartyController extends Controller implements ClassResourceInterface
{

    /** @DI\Inject("doctrine.orm.entity_manager") */
    protected $em;

    /**
     * Lista de parties
     *
     * @ApiDoc(
     *  resource=true,
     *  description="Lista de parties"
     * )
     */
    public function cgetAction(){
        $view = View::create();
        $data = $this->em->getRepository("PKPLanPartyBundle:Party")->findAll();
        $view->setData($data);
        return $view;
    }

    /**
     * Subscribirse a una party
     *
     * @ApiDoc(
     *  resource=true,
     *  description="Subscribirse a una party"
     * )
     */
    public function postSubscriptionAction($partyId){
        $view = View::create();

        $party = $this->em->getRepository("PKPLanPartyBundle:Party")->find($partyId);
        if($party) {
            $subs = new PartySubscription();
            $subs->setUser($this->getUser());
            $subs->setParty($party);
            $subs->setAccepted(true);
            $this->em->persist($subs);
            try {
                $this->em->flush();
                $view->setData(array("status" => "ok"));
            }catch(\Exception $e){
                $view->setData(array("error" => "Ya estas subscrito"));
            }

        }else{
            $view->setData(array("error" => "Party not found"));
        }
        return $view;
    }

    /**
     * Desapuntarse de una party
     *
     * @ApiDoc(
     *  resource=true,
     *  description="Desapuntarse de una party"
     * )
     */
    public function deleteSubscriptionAction($partyId){
        $view = View::create();

        $party = $this->em->getRepository("PKPLanPartyBundle:Party")->find($partyId);
        if($party) {
            $subs = $this->em->getRepository("PKPLanPartyBundle:PartySubscription")->findOneBy(array("party" => $party, "user" => $this->getUser()));
            if($subs) {
                $this->em->remove($subs);
                $this->em->flush();
                $view->setData(array("status" => "ok"));
            }else {
                $view->setData(array("error" => "No estas subscrito a esa party"));
            }


        }else{
            $view->setData(array("error" => "Party not found"));
        }
        return $view;
    }

    /**
     * Ver si estas subscrito a una party
     *
     * @ApiDoc(
     *  resource=true,
     *  description="Ver si estas subscrito a una party"
     * )
     */
    public function getSubscriptionAction($partyId){
        $view = View::create();

        $party = $this->em->getRepository("PKPLanPartyBundle:Party")->find($partyId);
        if($party) {
            $subs = $this->em->getRepository("PKPLanPartyBundle:PartySubscription")->findOneBy(array("party" => $party, "user" => $this->getUser()));
            if($subs) {
                $view->setData(array("subscribed" => true));
            }else {
                $view->setData(array("subscribed" => true));
            }


        }else{
            $view->setData(array("error" => "Party not found"));
        }
        return $view;
    }

}
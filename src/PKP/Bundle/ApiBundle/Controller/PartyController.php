<?php

namespace PKP\Bundle\ApiBundle\Controller;

use FOS\RestBundle\View\View;
use JMS\DiExtraBundle\Annotation as DI;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use FOS\RestBundle\Routing\ClassResourceInterface;
use PKP\Bundle\LanPartyBundle\Entity\Party;
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
     *  resource="Party",
     *  description="Lista de parties"
     * )
     */
    public function cgetAction(){
        $view = View::create();
        $data = $this->em->createQuery("SELECT p FROM PKPLanPartyBundle:Party p WHERE p.available = true")->execute();
        $view->setData($data);
        return $view;
    }

    /**
     * Subscribirse a una party
     *
     * @ApiDoc(
     *  resource="Party",
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
     * Subscribirse a una party y un puesto
     * EN UN FUTURO DESAPARECERA
     *
     * @ApiDoc(
     *  resource="Party",
     *  description="Subscribirse a una party y un puesto"
     * )
     */
    public function postPostSubscriptionAction($partyId, $postId){
        $view = View::create();

        $party = $this->em->getRepository("PKPLanPartyBundle:Party")->find($partyId);
        $post = $this->em->getRepository("PKPLanPartyBundle:Post")->find($postId);
        if($party) {
            if($post){
                $subs = $this->em->getRepository("PKPLanPartyBundle:PartySubscription")->findOneBy(array("user" => $this->getUser(), "party" => $party));
                if(!$subs){
                    $subs = new PartySubscription();
                    $subs->setUser($this->getUser());
                    $subs->setParty($party);
                    $subs->setAccepted(true);
                    $this->em->persist($subs);
                }

                $subs->setPost($post);

                try {
                    $this->em->flush();
                    $view->setData($this->getPartySubscriptions($party));
                }catch(\Exception $e){
                    $view->setData(array("error" => "Ya estas subscrito"));
                }
            }else $view->setData(array("error" => "Post not found"));


        }else{
            $view->setData(array("error" => "Party not found"));
        }
        return $view;
    }

    /**
     * Desapuntarse de una party
     *
     * @ApiDoc(
     *  resource="Party",
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
     *  resource="Party",
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

    /**
     * Lista de subscripciones a una party
     *
     * @ApiDoc(
     *  resource="Party",
     *  description="Lista de subscripciones a una party"
     * )
     */
    public function cgetSubscriptionsAction($partyId){
        $view = View::create();

        $party = $this->em->getRepository("PKPLanPartyBundle:Party")->find($partyId);
        if($party) {
            $view->setData($this->getPartySubscriptions($party));

        }else{
            $view->setData(array("error" => "Party not found"));
        }
        return $view;
    }


    private function getPartySubscriptions(Party $party){
        /*$query = $this->em->createQuery("SELECT p.id as post_id, p.name as post_name, u.id as user_id, u.username as username, pa.id as party_id FROM PKPLanPartyBundle:Post p LEFT JOIN p.subscriptions s LEFT JOIN s.party pa LEFT JOIN s.user u  WHERE pa = :party or pa is null");
        $query->setParameter("party", $party);
        echo $query->getSQL();
        $subs = array();
        foreach($query->execute() as $sub){
            //$subs[] = $this->serializeSubscription($sub);

            $owner = null;
            if(isset($sub['user_id'])){
                $owner = array("id" => $sub['user_id'],
                    "username" => $sub['username']
                );
            }
            $subs[] = array(
                "post" => array("id" => $sub['post_id'],
                    "name" => $sub['post_name']
                ),
                "owner" => $owner
            );
        }
        */

        //Optimizar esto
        $subs = array();
        foreach($this->em->getRepository("PKPLanPartyBundle:Post")->findAll() as $post){
            $query = $this->em->createQuery("SELECT count(ps.id) as total, ps.id, u.username as username, u.id as user_id FROM PKPLanPartyBundle:PartySubscription ps JOIN ps.user u WHERE ps.party = :party AND ps.post = :post");
            $query->setParameter("party", $party);
            $query->setParameter("post", $post);
            $sub = $query->getSingleResult();


            $owner = null;
            if($sub['total'] > 0){
                $owner = array("id" => $sub['user_id'],
                    "username" => $sub['username']
                );
            }
            $subs[] = array(
                "post" => array("id" => $post->getId(),
                    "name" => $post->getName()
                ),
                "owner" => $owner
            );


        }


        return $subs;

    }

}


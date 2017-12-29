<?php
/**
 * Created by PhpStorm.
 * User: alaak
 * Date: 29/12/17
 * Time: 11:11
 */

namespace AppBundle\Controller;


use AppBundle\Entity\Group;
use AppBundle\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class GroupController extends Controller
{

    private $serializer;

    /**
     * GroupController constructor.
     */
    public function __construct()
    {
        $encoders = array(new JsonEncoder());
        $normalizer = new ObjectNormalizer();
        $normalizer->setCircularReferenceLimit(2);
        $normalizer->setCircularReferenceHandler(function ($object) {
            return $object->getId();
        });

        $this->serializer = new Serializer(array($normalizer), $encoders);
    }


    /**
     * @Route("/groups", name="list_groups")
     * @Method("GET")
     */

    public function indexAction(){

        $groups = $this->getDoctrine()->getRepository(Group::class)->findAll();
        $json_content = $this->serializer->serialize($groups, 'json');

        return new JsonResponse($json_content);
    }

    /**
     * @Route("/groups/{id}", name="get_group")
     * @Method("GET")
     */

    public function getAction($id){

        $group = $this->getDoctrine()->getRepository(Group::class)->find($id);
        $json_content = $this->serializer->serialize($group, 'json');

        return new JsonResponse($json_content);
    }


    /**
     * @param Request $request
     *
     * @Route("/groups", name="store_group")
     * @Method("POST")
     */


    public function storeAction(Request $request, EntityManagerInterface $em){
        $group = new Group();
        $paramAsArray = array();
        if($content = $request->getContent()){

            $paramAsArray = json_decode($content,true);

            $group->setNom($paramAsArray['nom']);

            if(isset($paramAsArray["users"])){
                $users = $em->getRepository(User::class)->findByIds($paramAsArray["users"]);
                $group->setUsers($users);
            }

            $em->persist($group);
            $em->flush();

            return new JsonResponse(['data' => 'success'], 200);
        }

        return new JsonResponse(['error' => 'empty request'], 400);
    }

}
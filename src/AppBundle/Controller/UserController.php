<?php
/**
 * Created by PhpStorm.
 * User: alaak
 * Date: 29/12/17
 * Time: 10:31
 */

namespace AppBundle\Controller;


use AppBundle\Entity\Group;
use AppBundle\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class UserController extends Controller
{

    private $serializer;
    /**
     * UserController constructor.
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
     * @Route("/users", name="list_users")
     * @Method("GET")
     */

    public function indexAction(){

        $users = $this->getDoctrine()->getRepository(User::class)->findAll();
        $json_content = $this->serializer->serialize($users, 'json');

        return new JsonResponse($json_content);
    }

    /**
     * @Route("/users/{id}", name="get_user")
     * @Method("GET")
     */

    public function getAction($id){

        $user = $this->getDoctrine()->getRepository(User::class)->find($id);
        $json_content = $this->serializer->serialize($user, 'json');

        return new JsonResponse($json_content);
    }


    /**
     * @param Request $request
     *
     * @Route("/users", name="store_user")
     * @Method("POST")
     */
    public function storeAction(Request $request, EntityManagerInterface $em){
        $user = new User();
        $paramAsArray = array();
        if($content = $request->getContent()){
            $paramAsArray = json_decode($content,true);

            $user->setNom($paramAsArray['nom']);
            $user->setPrenom($paramAsArray['prenom']);
            $user->setEmail($paramAsArray['email']);
            $group = $em->getRepository(Group::class)->find($paramAsArray["group"]);
            $user->setGroup($group);

            $validator = $this->get('validator');
            $errors = $validator->validate($user);

            if (count($errors) > 0) {

                $errorsString = (string) $errors;

                return new Response($errorsString);
            }

            $em->persist($user);
            $em->flush();

            return new JsonResponse(['data' => 'success'], 200);
        }

        return new JsonResponse(['error' => 'empty request'], 400);
    }


    /**
     * @Route("/users/{id}", name="update_user")
     * @Method("PUT")
     */

    public function updateAction($id ,EntityManagerInterface $em, Request $request){

        $user = $em->getRepository(User::class)->find($id);

        $paramAsArray = array();
        if($content = $request->getContent()){
            $paramAsArray = json_decode($content,true);

            $user->setNom($paramAsArray['nom']);
            $user->setPrenom($paramAsArray['prenom']);
            $user->setEmail($paramAsArray['email']);

            $group = $em->getRepository(Group::class)->find($paramAsArray["group"]);

            $user->setGroup($group);


            $em->flush();

            return new JsonResponse(['data' => 'success'], 200);
        }

        $json_content = $this->serializer->serialize($user, 'json');

        return new JsonResponse($json_content);
    }



}
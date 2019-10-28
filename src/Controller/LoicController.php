<?php

namespace App\Controller;

use App\Entity\ManagerJSON;
use App\Repository\UserRepository;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Class LoicController
 * @package App\Controller
 * @Route("/api")
 */
class LoicController extends Controller
{
    /**
     * @Route("/getUserInfo")
     * @param Request $request
     * @param ValidatorInterface $validator
     * @param LoggerInterface $logger
     * @param SerializerInterface $serializer
     * @param UserRepository $repository
     * @return Response
     */
    public function test(Request $request, ValidatorInterface $validator, LoggerInterface $logger, SerializerInterface $serializer, UserRepository $repository)
    {
        //ManagerJSON::testRecupJSON();
        $user = $repository->findBy(["username" => $this->getUser()->getUsername()]);
        $tab["participant"] = $user;
        $tab['test'] = 'test';
        return ManagerJSON::renvoiJSON($tab, $serializer);
      //  }

        //version PLUG IN
        //return $this->renvoiJSON($tab, $logger);

    }



}
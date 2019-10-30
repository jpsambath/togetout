<?php

namespace App\Controller;

use App\Entity\ManagerJSON;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class LoicController
 * @package App\Controller
 * @Route("/api")
 */
class LoicController extends Controller
{
    /**
     * @Route("/getUserInfo")
     * @param UserRepository $repository
     * @return Response
     */
    public function test(UserRepository $repository)
    {
        $user = $repository->findBy(["username" => $this->getUser()->getUsername()]);
        $tab["participant"] = $user;
        $tab['action'] = 'test';

        return ManagerJSON::renvoiJSON($tab);
    }
}
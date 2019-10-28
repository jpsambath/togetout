<?php

namespace App\Controller;

use App\Entity\ManagerJSON;
use App\Entity\Participant;
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
 * @Route("/test")
 */
class LoicController extends Controller
{
    /**
     * @Route("/responseJSON")
     * @param Request $request
     * @param ValidatorInterface $validator
     * @param LoggerInterface $logger
     * @param SerializerInterface $serializer
     * @return Response
     */
    public function sendJSON(Request $request, ValidatorInterface $validator, LoggerInterface $logger, SerializerInterface $serializer)
    {
        $tab['test'] = 'test';
        return ManagerJSON::renvoiJSON($tab, $serializer);
      //  }

        //version PLUG IN
        //return $this->renvoiJSON($tab, $logger);

    }



}
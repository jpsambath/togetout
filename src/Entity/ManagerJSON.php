<?php


namespace App\Entity;


use App\Repository\UserRepository;
use Doctrine\Common\Persistence\ObjectManager;
use ErrorException;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class ManagerJSON
{
    /**
     * @param $data
     * @param SerializerInterface $serializer
     * @return Response
     */
    public static function renvoiJSON($data, SerializerInterface $serializer){
        $dataJSON = $serializer->serialize($data, 'json');

        $response = new Response($dataJSON);
        $response->headers->set('Content-Type', 'application/json');
        //$response->headers->set('Access-Control-Allow-Origin', '*');

        return $response;
    }

    /**
     * @param Request $request
     * @throws ErrorException
     */
    public static function testRecupJSON(Request $request): void
    {
        if ($request->getContent() == null) {
            throw new ErrorException("Aucune valeur recue !");
        }
    }

    /**
     * @param Participant $user
     * @param ObjectManager $objectManager
     * @return UserRepository[]|object[]
     */
    public static function test(Participant $user, ObjectManager $objectManager)
    {
        return $objectManager->getRepository(UserRepository::class)->findBy(["username" => $user->getUsername()]);
    }
}
<?php


namespace App\Entity;



use App\Repository\UserRepository;
use Doctrine\Common\Persistence\ObjectManager;
use ErrorException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;


final class ManagerJSON
{
    /**
     * @param $data
     * @return Response
     */
    public static function renvoiJSON($data){
        $normalizer = new ObjectNormalizer(null, null, null, new ReflectionExtractor());
        $serializer = new Serializer([new DateTimeNormalizer(), $normalizer], [new JsonEncoder()]);

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
     * @param $user
     * @param ObjectManager $objectManager
     * @return Participant[]|UserRepository[]|object[]
     */
    public static function getUser($user, ObjectManager $objectManager)
    {
        return $objectManager->getRepository(Participant::class)->findBy(["username" => $user->getUsername()]);
    }
}
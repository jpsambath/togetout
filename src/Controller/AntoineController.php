<?php

namespace App\Controller;
use App\Entity\Sortie;
use Doctrine\Common\Persistence\ObjectManager;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class AntoineController extends Controller
{
    /**
     * @Route("/antoine", name="antoine")
     */
    public function index()
    {
        return $this->render('antoine/index.html.twig', [
            'controller_name' => 'AntoineController',
        ]);
    }

    /**
     * @Route("/listeSorties", name="listeSorties")
     * @param Request $request
     * @return JsonResponse
     **/
    public function listeSorties(Request $request)
    {
        $listeSortie = $this->getDoctrine()
            ->getManager()
            ->getRepository('App\Entity\Sortie')
            ->findSortie(json_decode($request->get("selectSite")), //Id du site selectionné
                json_decode($request->get("checkFiltreOrganisateur")), //Boolean
                json_decode($request->get("checkFiltreInscrit")), //Boolean
                json_decode($request->get("checkFiltrePasInscrit")), //Boolean
                json_decode($request->get("checkFiltreSortiePasse")), //Boolean
                json_decode($request->get("filtreSaisieNom")), //Valeur de l'input
                json_decode($request->get("idParticipant")), //Id de l'utilisateur connecté
                json_decode($request->get("filtreDateDebut")), //valeur de l'input de type date du premier intervalle
                json_decode($request->get("filtreDateFin"))); //valeur de l'input de type date du deuxième intervalle
        return new JsonResponse(json_encode(['listeSortie' => $listeSortie]));
    }

    /**
     * @Route("/responseJSON")
     * @param Request $request
     * @param LoggerInterface $logger
     * @param ValidatorInterface $validator
     * @param ObjectManager $objectManager
     * @return Response
     */
    public function sendJSON(Request $request, LoggerInterface $logger, ValidatorInterface $validator, ObjectManager $objectManager)
    {
        try {
            if ($request->getContent() != null) {
                $sortieRecu = $request->getContent();
                $sortieRecu = $this->get('jms_serializer')->deserialize($sortieRecu, 'App\Entity\Sortie', 'json');
                $error = $validator->validate($sortieRecu);
            } else {
                throw new \ErrorException("Aucune valeur recue !");
            }

            if (count($error) > 0) {
                throw new \ErrorException("Erreur lors de la validation !");
            }

            $objectManager->persist($sortieRecu);
            $objectManager->flush();

            $tab['statut'] = "ok";
            $tab['sortie'] = $sortieRecu;


        } catch (\Exception $e) {
            $tab['statut'] = "erreur";
            $tab['messageErreur'] = $e->getMessage();

        } finally {
            return $this->renvoiJSON($tab);
        }
    }

    private function renvoiJSON($data){
        $dataJSON = $this->get('jms_serializer')->serialize($data, 'json');

        $response = new Response($dataJSON);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }
}

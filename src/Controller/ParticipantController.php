<?php

namespace App\Controller;

use App\Entity\ManagerJSON;
use App\Entity\Participant;
use Doctrine\Common\Persistence\ObjectManager;
use ErrorException;
use Exception;
use Swift_Mailer;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Class ParticipantController
 * @package App\Controller
 * @Route("", name="")
 */
class ParticipantController extends Controller
{
    /**
     * @Route("/modifierProfil", name="modifierProfil")
     * @param Request $request
     * @param ValidatorInterface $validator
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param ObjectManager $objectManager
     * @param SerializerInterface $serializer
     * @return Response
     */
    public function modifierProfil(Request $request, ValidatorInterface $validator, UserPasswordEncoderInterface $passwordEncoder,
                                   ObjectManager $objectManager, SerializerInterface $serializer)
        {
            try{
                ManagerJSON::testRecupJSON($request);

                $participantRecu = $request->getContent();

                $participantRecu = $serializer->deserialize($participantRecu, Participant::class, 'json');
                $error = $validator->validate($participantRecu);

                if (count($error) > 0) {
                    throw new \ErrorException("Erreur lors de la validation !");
                }

                $participantRecu->setPassword($passwordEncoder->encodePassword($participantRecu, $participantRecu->getPlainPassword()));

                $objectManager->persist($participantRecu);
                $objectManager->flush();

                $tab['statut'] = "ok";
                $tab['messageOk'] = "Update successfull";

            } catch (\Exception $e) {
                $tab['statut'] = "erreur";
                $tab['messageErreur'] = $e->getMessage();

            } finally {
                $tab['action'] = "modifierProfil";
            }

            return ManagerJSON::renvoiJSON($tab, $serializer);
        }


    /*----------------------------------DAVID-------------------------------------*/
    /**
     * @Route("/consulterProfil/{id}", name="consulterProfil")
     * @param Participant $participant
     * @param SerializerInterface $serializer
     * @param Request $request
     * @return Response
     */
    public function consulterProfil(Participant $participant, SerializerInterface $serializer, Request $request)
    {
        try {
            ManagerJSON::testRecupJSON($request);

            $tab['statut'] = "ok";
            $tab['participant'] = $participant;

        } catch (\Exception $e){
                $tab['statut'] = "erreur";
                $tab['messageErreur'] = $e->getMessage();

        } finally {
            $tab['action'] = "consulterProfil";
        }

        return ManagerJSON::renvoiJSON($tab, $serializer);
    }

    /**
     * @Route("/supprimerUtilisateur", name="supprimerUtilisateur")
     * @param Request $request
     * @param ValidatorInterface $validator
     * @param ObjectManager $objectManager
     * @param SerializerInterface $serializer
     * @return Response
     */
    public function supprimerUtilisateur(Request $request, ValidatorInterface $validator, ObjectManager $objectManager, SerializerInterface $serializer)
    {
        try {
            ManagerJSON::testRecupJSON($request);

            if (!$this->getUser()->isAdministrateur()) {
                throw new ErrorException("Acces reserver au administrateurs");
            }

            $listeIdRecue = $request->getContent();
            $listeIdDeserialise =$serializer->deserialize($listeIdRecue, Participant::class, 'json');
            $error = $validator->validate($listeIdDeserialise);

            foreach ($listeIdDeserialise as $participant) {
                $participant->setActif(false);
                $objectManager->persist($participant);
                $objectManager->flush();
            }

            $tab['statut'] = "ok";
            $tab['messageOk'] = "Participant supprimer avec success !";

        } catch (Exception $e) {
            $tab['statut'] = 'erreur';
            $tab['messageErreur'] = $e->getMessage();
        } finally {
            $tab['action'] = "supprimerUtilisateur";
        }
        return ManagerJSON::renvoiJSON($tab, $serializer);
    }

    /**
     * @Route("/inscrireUtilisateur", name="sinscrireUtilisateur")
     * @param Request $request
     * @param ValidatorInterface $validator
     * @param ObjectManager $objectManager
     * @param SerializerInterface $serializer
     * @return Response
     */
    public function inscrireManuellementUtilisateur (Request $request, ValidatorInterface $validator, ObjectManager $objectManager, SerializerInterface $serializer)
    {
        try{
            ManagerJSON::testRecupJSON($request);

            if (!$this->getUser()->isAdministrateur()){
                throw new ErrorException("Acces reserver au administrateurs");
            }

            $utilisateurRecue = $request->getContent();
            $utilisateurDeserialise = $serializer->deserialize($utilisateurRecue, 'App\Entity\Participant', 'json');
            $error = $validator->validate($utilisateurDeserialise);

            $objectManager->persist($utilisateurDeserialise);
            $objectManager->flush();

            $tab['statut'] = "ok";
            $tab['messageOk'] = "Participant inscrit avec success !";

        } catch (Exception $e) {
            $tab['statut'] = 'erreur';
            $tab['messageErreur'] = $e->getMessage();

        } finally {
            $tab['action'] = "inscrireManuellementUtilisateur";
        }
        return ManagerJSON::renvoiJSON($tab, $serializer);
    }

    /*-----------------------------------ANTOINE----------------------------------*/
    /**
     * @Route("/sendMailRecuperationMDP", name="sendMailRecuperationMDP")
     * @param Swift_Mailer $mailer
     * @param Request $request
     * @param SerializerInterface $serializer
     * @return Response
     */
    private function sendMailRecuperationMDP(Swift_Mailer $mailer, Request $request, SerializerInterface $serializer)
    {
        try {
            ManagerJSON::testRecupJSON($request);

            $participant = json_decode($request->get("objetParticipant"));
            $lien = json_decode($request->get("lienDuReset"));

            $message = (new \Swift_Message('Réinitialisation de mot de passe'))
                ->setFrom('togetouttest@gmail.com')
                ->setTo($participant->getEmail())
                ->setBody(
                    'Bonjour '.$participant->getPrenom(). ', pour réinitialiser votre mot de passe <a href="'.$lien.'">Cliquez ici</a>',
                    'text/html');
            $mailer->send($message);

            $tab['statut'] = "ok";

        } catch (\Exception $e){
            $tab['statut'] = "erreur";
            $tab['messageErreur'] = $e->getMessage();

        } finally {
            $tab['action'] = "sendMailRecuperationMDP";
        }

        return ManagerJSON::renvoiJSON($tab, $serializer);
    }
}

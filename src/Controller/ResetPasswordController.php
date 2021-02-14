<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ChangePasswordFormType;
use App\Form\ResetPasswordRequestFormType;
use App\Repository\ApprenantRepository;
use App\Repository\ResetPasswordRequestRepository;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Serializer\SerializerInterface;
use SymfonyCasts\Bundle\ResetPassword\Controller\ResetPasswordControllerTrait;
use SymfonyCasts\Bundle\ResetPassword\Exception\ResetPasswordExceptionInterface;
use SymfonyCasts\Bundle\ResetPassword\Model\ResetPasswordRequestInterface;
use SymfonyCasts\Bundle\ResetPassword\ResetPasswordHelperInterface;


class ResetPasswordController extends AbstractController
{
    use ResetPasswordControllerTrait;

    private $resetPasswordHelper, $apprenantRepository;

    public function __construct(ResetPasswordHelperInterface $resetPasswordHelper, ApprenantRepository $apprenantRepository)
    {
        $this->resetPasswordHelper = $resetPasswordHelper;
        $this->apprenantRepository = $apprenantRepository;
    }

    /**
     * Display & process form to request a password reset.
     *
     * @Route("api/reset-password", name="app_forgot_password_request")
     */
    public function request(Request $request, MailerInterface $mailer): Response
    {
        $form = $this->createForm(ResetPasswordRequestFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            return $this->processSendingPasswordResetEmail(
                $form->get('email')->getData(),
                $mailer
            );
        }

        return $this->render('reset_password/request.html.twig', [
            'requestForm' => $form->createView(),
        ]);
    }

    /**
     * Confirmation page after a user has requested a password reset.
     *
     * @Route("api/reset-password/check-email", name="app_check_email")
     */
    public function checkEmail(): Response
    {
        // We prevent users from directly accessing this page
        if (!$this->canCheckEmail()) {
            return $this->redirectToRoute('app_forgot_password_request');
        }

        return $this->render('reset_password/check_email.html.twig', [
            'tokenLifetime' => $this->resetPasswordHelper->getTokenLifetime(),
        ]);
    }

    /**
     * Validates and process the reset URL that the user clicked in their email.
     *
     * @Route("api/reset-password/reset/{token}", name="app_reset_password")
     */

    //  on met la methode en post
    // 
    public function reset(Request $request, UserPasswordEncoderInterface $passwordEncoder, string $token = null): Response
    {
        if ($token) {
            // We store the token in session and remove it from the URL, to avoid the URL being
            // loaded in a browser and potentially leaking the token to 3rd party JavaScript.
            $this->storeTokenInSession($token);

            return $this->redirectToRoute('app_reset_password');
        }

        $token = $this->getTokenFromSession();
        if (null === $token) {
            throw $this->createNotFoundException('No reset password token found in the URL or in the session.');
        }

        // try {
            $user = $this->resetPasswordHelper->validateTokenAndFetchUser($token);
        // } catch (ResetPasswordExceptionInterface $e) {
        //     $this->addFlash('reset_password_error', sprintf(
        //         'There was a problem validating your reset request - %s',
        //         $e->getReason()
        //     ));

        //     return $this->redirectToRoute('app_forgot_password_request');
        // }

        // The token is valid; allow the user to change their password.

        // les données doivent parvenir du front a l'aide d'un post
        // remplacer le form par request

        // $this->getResetData($request);

        
        $form = $this->createForm(ChangePasswordFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // A password reset token should be used only once, remove it.
            $this->resetPasswordHelper->removeResetRequest($token);

            // Encode the plain password, and set it.
            $encodedPassword = $passwordEncoder->encodePassword(
                $user,
                $form->get('plainPassword')->getData()
            );
            $keys=["prenom","nom", "adresse", "telephone", "genre", "avatar"];
            foreach ($keys as $value) {
                if($value != 'avatar'){
                    $setter = 'set'.$value;
                    $user->$setter($form->get($value)->getData());
                }
                // else{
                //     $avatar = $this->uploadAvatarService->uploadAvatar($request, "avatar");
                //     $object->setAvatar($avatar); 
                // }
                
            }
            $user->setHasJoinPromo(true);
            $user->setPassword($encodedPassword);
            $this->getDoctrine()->getManager()->flush();

            // The session is cleaned up after the password has been changed.
            $this->cleanSessionAfterReset();

            return $this->redirectToRoute('api_platform');
        }

        return $this->render('reset_password/reset.html.twig', [
            'resetForm' => $form->createView(),
        ]);
    }

    public function processSendingPasswordResetEmail(string $emailFormData, MailerInterface $mailer): JsonResponse
    {
        $user = $this->getDoctrine()->getRepository(User::class)->findOneBy([
            'email' => $emailFormData,
        ]);

        // Marks that you are allowed to see the app_check_email page.
        $this->setCanCheckEmailInSession();

        // Do not reveal whether a user account was found or not.
        if (!$user) {
            $this->redirectToRoute('app_check_email');
            return new JsonResponse('Utilisateur inexistante', 400);
        }

        try {
            $resetToken = $this->resetPasswordHelper->generateResetToken($user);
        } catch (ResetPasswordExceptionInterface $e) {
            // If you want to tell the user why a reset email was not sent, uncomment
            // the lines below and change the redirect to 'app_forgot_password_request'.
            // Caution: This may reveal if a user is registered or not.
            //
            // $this->addFlash('reset_password_error', sprintf(
            //     'There was a problem handling your password reset request - %s',
            //     $e->getReason()
            // ));

            $this->redirectToRoute('app_check_email');
            return new JsonResponse('Ereur', 400);
        }

        $email = (new TemplatedEmail())
            ->from(new Address('ndeyesalydione@gmail.com', 'Ndeye Saly Dione'))
            ->to($user->getEmail())
            ->subject('Your password reset request')
            ->htmlTemplate('reset_password/email.html.twig')
            ->context([
                'resetToken' => $resetToken,
                'tokenLifetime' => $this->resetPasswordHelper->getTokenLifetime(),
            ])
        ;

        $mailer->send($email);

        return new JsonResponse('Email envoyé avec succès', 200);
    }

    //gestion des relances

    //relance individuel


/**
 * @Route("api/reset-password/relance/apprenants/{id}")
 */
    public function relanceOneApprenant(int $id, ResetPasswordRequestRepository $resetRepository, MailerInterface $mailer){
        $apprenant = $this->apprenantRepository->findOneBy(["id" => $id]);
        $attentes = $resetRepository->findAll();
        foreach ($attentes as $attente) {
            $apprenantEnAttente = $attente->getUser();
            if($apprenant == $apprenantEnAttente){
                return $this->processSendingPasswordResetEmail($apprenant->getEmail(), $mailer);
            }
        }
       
    }

    //relance collectif

/**
 * @Route("api/reset-password/relance/apprenants")
 */
    public function relanceTout(ResetPasswordRequestRepository $resetRepository, MailerInterface $mailer){
        $attentes = $resetRepository->findAll();
        foreach ($attentes as $attente) {
            $this->processSendingPasswordResetEmail($attente->getUser()->getEmail(), $mailer);
        }
    }

    // liste des apprenants en attente

    /**
 * @Route("api/admin/promos/apprenants/attente", name="apprenantsEnAttente", methods={"GET"}, defaults={"_api_collection_operation_name"="apprenantsEnAttente"})
 */
    public function apprenantsEnAttente(ResetPasswordRequestRepository $resetRepository, SerializerInterface $serializerInterface){
        $attentes = $resetRepository->findAll();
        $apprenantEnAttente = [];
        $attentesJson = $serializerInterface->serialize($attentes, 'json',["groups"=>["attente:read"]]);
        $tab = $serializerInterface->decode($attentesJson, "json");
        foreach ($tab as $value) {
            if((!in_array($value, $apprenantEnAttente)) && ($value['user']['archive']==false)){
                $apprenantEnAttente[]=$value;
            }
        }
        return $this->json($apprenantEnAttente, 200, []);
    }

    // apprenants en attente d'une promo

/**
 * @Route("api/admin/promos/{id}/apprenants/attente", name="attenteOfOnePromo", methods={"GET"}, defaults={"_api_item_operation_name"="attenteOfOnePromo"})
 */
    public function attenteOfOnePromo(int $id, ResetPasswordRequestRepository $resetRepository, SerializerInterface $serializerInterface){
        $attentes = $resetRepository->findAll();
        $apprenantEnAttente = $attenteOfPromo = [];
        foreach ($attentes as $attente) {
            if($attente->getUser()->getGroupes()[0]->getPromo()->getId() == $id){
                $attenteOfPromo[] = $attente;
            }
        }
        $attentesJson = $serializerInterface->serialize($attenteOfPromo,'json', ["groups"=>["attente:read"]]);
        $tab = $serializerInterface->decode($attentesJson, "json", );
        foreach ($tab as $value) {
            if(!in_array($value, $apprenantEnAttente)){
                $apprenantEnAttente[]=$value;
            }
        }
        return $this->json($apprenantEnAttente, 200, []);
    }

    // fonction permettant de recuperer l'utlisateur courant

/**
 * @Route("api/admin/user", name="getCurrentUser", methods={"GET"})
 */
    public function getCurrentUser(){
        return $this->getUser();
    }

    // fonction permettant de recevoir les donnes du reset password à partir du front

        /**
 * @Route("api/admin/getResetData", name="getResetData", methods={"POST"})
 */
    // public function getResetData(Request $request)
    // {
    //     dd($request);
    // }

}

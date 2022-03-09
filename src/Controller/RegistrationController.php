<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Repository\SettingsRepository;
use App\Repository\UserRepository;
use App\Repository\UserTypeRepository;
use App\Security\EmailVerifier;
use App\Services\IntranetAPI;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

class RegistrationController extends AbstractController
{
    private EmailVerifier $emailVerifier;

    public function __construct(EmailVerifier $emailVerifier)
    {
        $this->emailVerifier = $emailVerifier;
    }

    #[Route('/inscription', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasherInterface, UserTypeRepository $userTypeRepository, UserRepository $userRepository, IntranetAPI $intranetAPI, SettingsRepository $settingsRepository): Response
    {


        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if(strlen(explode('@', $form->get("email")->getData())[0]) === 3){
                $teacher = $intranetAPI->teacher($form->get("email")->getData());

                $user
                    ->setEmail($teacher->getEmail())
                    ->setFamilyName($teacher->getLastname())
                    ->setGivenName($teacher->getFirstname());

                $userType = $userTypeRepository->findOneBySlug("teacher");

            }
            else{
                $student = $intranetAPI->student($form->get('email')->getData());

                if(!is_null($userRepository->findOneBy(['email' => $student->getEmail()]))){
                    return $this->json([
                        'type' => 'error',
                        'error' => "Une erreur est survenue."
                    ]);
                }

                $user
                    ->setEmail($student->getEmail())
                    ->setFamilyName($student->getLastname())
                    ->setGivenName($student->getFirstname());

                $userType = $userTypeRepository->findOneBySlug("student");
            }



            // encode the plain password
            $user->setPassword(
                $userPasswordHasherInterface->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );




            $user->setType($userType);

            $entityManager = $this->getDoctrine()->getManager();

            $entityManager->persist($user);
            $entityManager->flush();



            // generate a signed url and email it to the user
            $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user,
                (new TemplatedEmail())
                    ->from(new Address('no-reply@multimedia.mediamatique.ch', 'Espace Multimédia'))
                    ->to($user->getEmail())
                    ->subject('Confirmation de votre compte')
                    ->htmlTemplate('registration/confirmation_email.html.twig')
            );
            // do anything else you need here, like send an email
            return $this->json([
                'type' => 'success'
            ]);
            //return $this->redirectToRoute('home');
        } elseif ($form->isSubmitted() && !$form->isValid()){
            return $this->json([
                'type' => 'error',
                'error' => $form->getErrors()
            ]);
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
            'menu' => 'register'
        ]);
    }

    #[Route('/verify/email', name: 'app_verify_email')]
    public function verifyUserEmail(Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        // validate email confirmation link, sets User::isVerified=true and persists
        try {
            $this->emailVerifier->handleEmailConfirmation($request, $this->getUser());
            $em = $this->getDoctrine()->getManager();
            $user = $this->getUser();
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('verify_email_error', $exception->getReason());

            return $this->redirectToRoute('app_register');
        }

        $this->addFlash('success', 'Your email address has been verified.');

        return $this->redirectToRoute('app_register');
    }
}

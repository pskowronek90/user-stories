<?php

namespace UserStoriesBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use UserStoriesBundle\Entity\User;

class UserController extends Controller
{
    public function generateUniqueFileName()
    {
        return md5(uniqid());
    }

    /**
     * @Route("/new", name="new-get", methods={"GET"})
     */
    public function newGetAction()
    {
        $user = new User();

        $form = $this->createFormBuilder($user)->setAction($this->generateUrl('new-get'))->setMethod('POST')->add('name',
            TextType::class, ['label' => 'User name'])->add('email', TextType::class,
            ['label' => 'E-mail'])->add('password', PasswordType::class, ['label' => 'Password'])->add('image',
            FileType::class, ['label' => 'Profile image'])->add('create', SubmitType::class,
            ['label' => 'Create user'])->getForm();

        return $this->render('user-stories/new.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/new", name="new-post", methods={"POST"})
     */
    public function newPostAction(Request $request)
    {
        $user = new User();

        $form = $this->createFormBuilder($user)->setAction($this->generateUrl('new-get'))->setMethod('POST')->add('name',
            TextType::class, ['label' => 'User name'])->add('email', TextType::class,
            ['label' => 'E-mail'])->add('password', PasswordType::class, ['label' => 'Password'])->add('image',
            FileType::class, ['label' => 'Profile image'])->add('create', SubmitType::class,
            ['label' => 'Create user'])->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            /** @var UploadedFile $file */
            $file = $user->getImage();
            $fileName = $this->generateUniqueFileName() . '.' . $file->guessExtension();

            $file->move($this->getParameter('profile-pictures_directory'), $fileName);
            $user->setImage($fileName);
            $user = $form->getData();
            $em = $this->getDoctrine()->getManager();

            $em->persist($user);
            $em->flush();

            return new Response("User created");
        }

        return new Response("User not created");

    }

    /**
     * @Route("/modify/{id}", name="modify-get", methods={"GET"})
     */
    public function updateGetAction($id)
    {
        $userRepository = $this->getDoctrine()->getRepository(User::class);
        $user = $userRepository->find($id);

        if ($user) {
            $form = $this->createFormBuilder($user)->setAction($this->generateUrl('new-get'))->setMethod('POST')->add('name',
                TextType::class, ['label' => 'User name'])->add('email', TextType::class,
                ['label' => 'E-mail'])->add('password', PasswordType::class, ['label' => 'Password'])->add('image',
                FileType::class, ['label' => 'Profile image'])->add('create', SubmitType::class,
                ['label' => 'Create user'])->getForm();
            return $this->render('user-stories/new.html.twig', ['form' => $form->createView()]);
        }
        return new Response("Error");

    }

    /**
     * @Route("/modify/{id}", name="modify-post", methods={"POST"})
     */
    public function updatePostAction(Request $request, $id)
    {
        $userRepository = $this->getDoctrine()->getRepository(User::class);
        $user = $userRepository->find($id);

        $form = $this->createFormBuilder($user)->setAction($this->generateUrl('new-get'))->setMethod('POST')->add('name',
            TextType::class, ['label' => 'User name'])->add('email', TextType::class,
            ['label' => 'E-mail'])->add('password', PasswordType::class, ['label' => 'Password'])->add('image',
            FileType::class, ['label' => 'Profile image'])->add('create', SubmitType::class,
            ['label' => 'Create user'])->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            /** @var UploadedFile $file */
            $file = $user->getImage();
            $fileName = $this->generateUniqueFileName() . '.' . $file->guessExtension();

            $file->move($this->getParameter('profile-pictures_directory'), $fileName);
            $user->setImage($fileName);
            $user = $form->getData();
            $em = $this->getDoctrine()->getManager();

            $em->persist($user);
            $em->flush();

            return new Response("User updated");
        }

        return new Response("User not updated");
    }

    /**
     * @Route("/delete/{id}", name="delete-get", methods={"GET"})
     */
    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $userRepository = $this->getDoctrine()->getRepository(User::class);
        $user = $userRepository->find($id);

        $em->remove($user);
        $em->flush();

        return new Response("user removed");
    }

    /**
     * @Route("/{id}", name="showUserById", methods={"GET"}
     */
    public function showUserByIdAction($id)
    {
        $userRepository = $this->getDoctrine()->getRepository(User::class);
        $user = $userRepository->find($id);

        return new Response("User Data:", ['user' => $user]); // potem w widoku
    }

    /**
     * @Route("/all", name="allUsers", methods={"GET"})
     */
    public function showAllAction()
    {
        $userRepository = $this->getDoctrine()->getRepository(User::class);
        $user = $userRepository->findAll();

        return new Response("All:", ['user' => $user]); // potem w widoku
    }
}

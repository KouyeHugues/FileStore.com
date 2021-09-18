<?php

namespace App\Controller;


use App\Entity\Files;
use App\Form\FilesType;
use App\Repository\FileRepository;
use App\Service\FileUploader;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

    /**
     * @Route("/file") 
     */
class FilesController extends AbstractController
{

    /**
     * @Route("/", name="app_file_home", methods={"GET"})
     */

    public function home(FileRepository $fileRepository, PaginatorInterface $paginator, Request $request): Response
    {
        return $this->render('file/home.html.twig', [
            'files' => $paginator->paginate($fileRepository->getAllWithQueryBuilder(), $request->query->getInt('page', 1), 10)
        ]);
    }

    /**
     * @Route("/index", name="app_file_index", methods={"GET"})
     * 
     * @IsGranted("ROLE_ADMIN")
     */
    public function index(FileRepository $fileRepository, PaginatorInterface $paginator, Request $request): Response
    {
        return $this->render('file/index.html.twig', [
            'files' => $paginator->paginate($fileRepository->findAll(), $request->query->getInt('page', 1), 10)
        ]);
    }

    /**
     * @Route("/new", name="app_file_new", methods={"GET", "POST"})
     * 
     * @IsGranted("ROLE_ADMIN")
     */
    public function new(Request $request, FileUploader $fileUploader): Response
    {
        $file = new Files();
        $form = $this->createForm(FIlesType::class, $file);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $fileUpoloade = $form->get('fileName')->getData();
            if ($file) {
                $fileName = $fileUploader->upload($fileUpoloade);
                $file->setFileName($fileName);
            }
            $file->setIsActive(true);
            $file->setAddedAt(new \DateTime());
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($file);
            $entityManager->flush();

            return $this->redirectToRoute('app_file_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('file/new.html.twig', [
            'file' => $file,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="app_file_edit", methods={"GET", "POST"})
     * 
     * @IsGranted("ROLE_ADMIN")
     */
    public function edit(Request $request, Files $file): Response
    {
        $form = $this->createForm(FIlesType::class, $file);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('app_file_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('file/edit.html.twig', [
            'file' => $file,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_file_delete", methods={"POST"})
     * 
     * @IsGranted("ROLE_ADMIN")
     */
    public function delete(Request $request, Files $file): Response
    {
        if ($this->isCsrfTokenValid('delete'.$file->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($file);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_file_index', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * @Route("/activate/{id}", name="app_file_activate", methods={"POST"})
     * 
     * @IsGranted("ROLE_ADMIN")
     */
    public function activate(Request $request, Files $file): Response
    {
        if ($this->isCsrfTokenValid('activate' . $file->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $file->setIsActive(true);
            $file->setAddedAt(new \DateTime());
            $entityManager->persist($file);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_file_index', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * @Route("/desactivate/{id}", name="app_file_desactivate", methods={"POST"})
     * 
     * @IsGranted("ROLE_ADMIN")
     */
    public function desactivate(Request $request, Files $file): Response
    {
        if ($this->isCsrfTokenValid('desactivate' . $file->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $file->setIsActive(false);
            $file->setAddedAt(new \DateTime());
            $entityManager->persist($file);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_file_index', [], Response::HTTP_SEE_OTHER);
    }
}
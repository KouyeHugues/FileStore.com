<?php

namespace App\Controller;

use App\Form\SearchFileType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\FileRepository;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;

class HomeController extends AbstractController
{
     /**
     * @Route("/", name="app_home")
     */
    public function index(FileRepository $fileRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $files = $paginator->paginate($fileRepository->getAllWithQueryBuilder(), $request->query->getInt('page', 1), 10);
        $form =$this->createForm(SearchFileType::class);
        $search=$form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $files = $fileRepository->search($search->get('mots')->getData());

        }
        return $this->render('file/home.html.twig', [
        'files' => $files,
        'form' => $form->createView()
        ]);

    }
   
     /**
     * @Route("/admin", name="app_dashboard_index", methods={"GET"})
     * 
     * @IsGranted("ROLE_ADMIN")
     */
    public function dashboard(): Response
    {
        return $this->render('user/dashboard.html.twig', []);
    }
}
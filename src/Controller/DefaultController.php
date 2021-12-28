<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use App\Entity\Series;
use Doctrine\ORM\EntityManagerInterface;


class DefaultController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(EntityManagerInterface $entityManager): Response
    {

        // TO DO => filtrer pour de vrai lel
        $best = $entityManager
        ->getRepository(Series::class)->findAll()[10];

        // TO DO => filtrer sur 5 meilleur snotes
        $top_5 = $entityManager
            ->getRepository(Series::class)
            ->findBy([], null, 5);
        
        // TODO
        $trending = $entityManager
            ->getRepository(Series::class)
            ->findBy([], null, 10, 10); // multiple de 5 obligé

        return $this->render('default/home.html.twig', [
            'best' => $best,
            'top_5' => $top_5,
            'trending' => $trending,
        ]);
    }
    
    #[Route('/about', name: 'about')]
    public function about(): Response
    {
        return $this->render('default/about.html.twig');
    }
}

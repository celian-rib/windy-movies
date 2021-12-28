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

        return $this->render('default/index.html.twig', [
            'best' => $best,
            'top_5' => $top_5,
        ]);
    }
}

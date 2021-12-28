<?php

namespace App\Controller;

use App\Entity\Episode;
use App\Entity\Season;
use App\Entity\Series;
use App\Form\SeriesType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/series')]
class SeriesController extends AbstractController
{
    #[Route('/', name: 'series_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $series = $entityManager
            ->getRepository(Series::class)
            ->findBy([], null, 20);

        return $this->render('series/index.html.twig', [
            'series' => $series,
        ]);
    }
    
    #[Route('/library', name: 'library', methods: ['GET'])]
    public function library(): Response
    {
        return $this->render('series/library.html.twig');
    }

    #[Route('/{id}', name: 'series_show', methods: ['GET'])]
    public function show(Series $series, EntityManagerInterface $entityManager): Response
    {
        $seasons = $entityManager
            ->getRepository(Season::class)
            ->findBy(
                ['series' => $series->getId()],
                ['number' => 'ASC']
            );
        $seasons_with_epcount = [];
        foreach($seasons as $s) {
            $ep_count = count($entityManager
                ->getRepository(Episode::class)
                ->findBy(
                    ['season' => $s->getId()],
                    ['number' => 'ASC']
                ));
            array_push($seasons_with_epcount, [$s, $ep_count]);
        }

        return $this->render('series/show.html.twig', [
            'series' => $series,
            'seasons' => $seasons_with_epcount,
        ]);
    }

    #[Route('/poster/{id}', name: 'series_poster', methods: ['GET'])]
    public function poster(Series $series): Response
    {
        $resp = new Response(stream_get_contents($series->getPoster()));
        $resp->headers->set('Content-Type', 'image/png');
        return $resp;
    }
}

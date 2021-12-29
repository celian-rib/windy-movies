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
use Doctrine\ORM\Query\ResultSetMapping;

#[Route('/series')]
class SeriesController extends AbstractController
{
    #[Route('/', name: 'series_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager, Request $request): Response
    {
        $series = [];
        if($request->query->get('search')) {
            $query = $entityManager->createQuery("SELECT s FROM App\Entity\Series s WHERE s.title like ?1");
            $query->setParameter(1, '%'. $request->query->get('search') . '%');
            $series = $query->getResult();
        } else {
            $series = $entityManager
            ->getRepository(Series::class)
            ->findBy([], null, 100);
        }

        return $this->render('series/index.html.twig', [
            'series' => $series
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
        foreach ($seasons as $s) {
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

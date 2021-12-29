<?php

namespace App\Controller;

use App\Entity\Episode;
use App\Entity\Genre;
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
        $search_filter = $request->query->get('search');
        $genre_filter = $request->query->get('genre');
        $rating_filter = $request->query->get('genre');

        $query = $entityManager
            ->createQueryBuilder()
            ->select('s')
            ->from(Series::class, 's');
        if (isset($search_filter))
            $query
                ->andWhere('s.title like :search')
                ->setParameter('search', '%' . ($search_filter ?? 'title') . '%');
        if (isset($genre_filter))
            $query
                ->join('s.genre', 'g')
                ->andWhere('g.id = :genre')
                ->setParameter('genre', $genre_filter);
        // if (isset($rating_filter)) // TO DO
        //     $query
        //         ->join('s.genre', 'g')
        //         ->andWhere('g.id = :genre')
        //         ->setParameter('genre', $genre_filter);

        return $this->render('series/browse_series.html.twig', [
            'series' => $query->getQuery()->getResult(),
            'genres' => $entityManager->getRepository(Genre::class)->findAll(),
            'ratings' => ["Less than 5/10", "More than 5/10", "More than 9/10"],
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

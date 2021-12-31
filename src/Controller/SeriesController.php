<?php

namespace App\Controller;

use App\Entity\Episode;
use App\Entity\Genre;
use App\Entity\Season;
use App\Entity\Series;
use App\Entity\User;
use App\Form\SeriesType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\Security\Core\User\UserInterface;

#[Route('/series')]
class SeriesController extends AbstractController
{
    #[Route('/', name: 'series_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager, Request $request): Response
    {
        $MAX_PER_PAGE = 20;
        $search_filter = $request->query->get('search');
        $genre_filter = $request->query->get('genre');
        // $rating_filter = $request->query->get('rating');

        $offset = $request->query->get('offset') ?? 0;
        if ($offset < 0)
            $offset = 0;

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

        $result_counts = count($query->getQuery()->getResult());
        $query->setFirstResult($offset * $MAX_PER_PAGE)->setMaxResults($MAX_PER_PAGE);

        return $this->render('series/browse_series.html.twig', [
            'series' => $query->getQuery()->getResult(),
            'genres' => $entityManager->getRepository(Genre::class)->findAll(),
            'page_count' => ceil($result_counts / $MAX_PER_PAGE),
            'is_last' => (($offset + 1) * $MAX_PER_PAGE) >= $result_counts,
        ]);
    }

    #[Route('/library', name: 'library', methods: ['GET'])]
    public function library(): Response
    {
        return $this->render('series/library.html.twig');
    }

    #[Route('/{id}', name: 'series_show', methods: ['GET', 'POST'])]
    public function show(Request $request, Series $series, EntityManagerInterface $entityManager): Response
    {

        $is_following = false;
        $user = $this->getUser();
        if ($user != null) {
            foreach ($user->getSeries() as $s) {
                if ($s->getId() == $series->getId())
                    $is_following = true;
            }

            if ($request->isMethod("post")) {
                if ($is_following) {
                    $user->removeSeries($series);
                } else {
                    $user->addSeries($series);
                }
                $entityManager->flush();
            }
        }

        return $this->render('series/show.html.twig', [
            'serie' => $series,
            'is_following' => $is_following,
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

<?php

namespace App\Controller;

use App\Entity\Episode;
use App\Entity\Genre;
use App\Entity\Rating;
use App\Entity\Series;
use App\Entity\User;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


#[Route('/series')]
class SeriesController extends AbstractController
{
    #[Route('/', name: 'series_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager, Request $request): Response
    {
        $MAX_PER_PAGE = 20;
        $search_filter = $request->query->get('search');
        $genre_filter = $request->query->get('genre');

        $offset = $request->query->get('offset') ?? 0;
        if ($offset < 0)
            $offset = 0;

        $query = $entityManager
            ->createQueryBuilder()
            ->select('s')
            ->orderBy('s.id', 'DESC')
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

        return $this->render('pages/browse.html.twig', [
            'series' => $query->getQuery()->getResult(),
            'genres' => $entityManager->getRepository(Genre::class)->findAll(),
            'page_count' => ceil($result_counts / $MAX_PER_PAGE),
            'is_last' => (($offset + 1) * $MAX_PER_PAGE) >= $result_counts,
        ]);
    }

    private function handleSeriePost(Request $request, Series $series, User $user, EntityManagerInterface $entityManager, $followed)
    {
        $comment = $request->get('comment');

        $episode = $request->get('episode');
        if (isset($episode)) {
            $episode = $entityManager
                ->getRepository(Episode::class)
                ->findOneBy(["id" => $episode]);

            if ($episode->seenByUser($user))
                $user->removeEpisode($episode);
            else
                $user->addEpisode($episode);

            $entityManager->flush();
            return $followed;
        }

        $rating = $request->get('rating');
        if (isset($rating)) {
            $new_rating = new Rating();
            $new_rating->setComment($comment);
            $new_rating->setValue($rating * 2);
            $new_rating->setDate(new DateTime());
            $new_rating->setSeries($series);
            $new_rating->setUser($user);

            $entityManager->persist($new_rating);
            $entityManager->flush();
            return $followed;
        }

        $delete_review = $request->get('delete_review');
        if (isset($delete_review)) {
            $rating = $entityManager
                ->getRepository(Rating::class)
                ->findOneBy(array('id' => $delete_review));
            if (!$user->getAdmin() && !($rating->getUser()->getId() == $user->getId()))
                return $this->redirectToRoute('index', array('toasterr' => 'You are not admin'));
            $entityManager->remove($rating);
            $entityManager->flush();
            return $followed;
        }

        // Post with no params => toggle follow state
        if ($followed)
            $user->removeSeries($series);
        else
            $user->addSeries($series);
        $entityManager->flush();
        return !$followed;
    }

    #[Route('/{id}', name: 'series_show', methods: ['GET', 'POST'])]
    public function show(Request $request, Series $serie, EntityManagerInterface $entityManager): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        if ($user == null)
            return $this->render('pages/serie.html.twig', [
                'serie' => $serie,
                'is_following' => false
            ]);

        $followed = $serie->followedByUser($user);

        if ($request->isMethod("post"))
            $followed = $this->handleSeriePost($request, $serie, $user, $entityManager, $followed);

        return $this->render('pages/serie.html.twig', [
            'serie' => $serie,
            'is_following' => $followed,
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

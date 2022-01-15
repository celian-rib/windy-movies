<?php

namespace App\Controller;

use App\Entity\Rating;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

class UserPagesController extends AbstractController
{
    #[Route('/account', name: 'account')]
    public function account(Request $request, EntityManagerInterface $entityManager): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        if ($user == null)
            return $this->redirectToRoute('index');

        $delete_account = $request->get('delete_account');
        $delete_review = $request->get('delete_review');
        if ($request->isMethod("post")) {
            if (isset($delete_account)) {
                if ($user->getAdmin())
                    return $this->redirectToRoute('index', array('toasterr' => 'You cannot delete an admin account'));
                $entityManager->remove($user);
                $entityManager->flush();
                $request->getSession()->invalidate(1);
                return $this->redirectToRoute('index');
            }
            if (isset($delete_review)) {
                $rating = $entityManager
                    ->getRepository(Rating::class)
                    ->findOneBy(array('id' => $delete_review));
                if (!$user->getAdmin() && !($rating->getUser()->getId() == $user->getId()))
                    return $this->redirectToRoute('index', array('toasterr' => 'You are not admin'));
                $entityManager->remove($rating);
                $entityManager->flush();
            }
        }

        return $this->render('pages/users/account.html.twig', [
            'reviews' => $user->getRatings()
        ]);
    }

    #[Route('/library', name: 'library', methods: ['GET'])]
    public function library(EntityManagerInterface $entityManager): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        if ($user == null)
            return $this->redirectToRoute('index');

        $conn = $entityManager->getConnection();
        $sql = "SELECT user_s_id as serie_id, 100 * nb_ep_watched.total / nb_ep_total.total as percent
        from (
            select series.id as user_s_id, count(episode.id) as total 
            from user_series
            inner join series on series.id = user_series.series_id
            inner join season on season.series_id = series.id
            inner join episode on episode.season_id = season.id
            inner join user_episode on user_episode.episode_id = episode.id
            where user_series.user_id = ?
            group by series.id
        ) as nb_ep_watched
        join (
            select series.id as all_s_id, count(episode.id) as total from episode
            inner join season on season.id = episode.season_id
            inner join series on series.id = season.series_id
            group by series.id
        ) as nb_ep_total
        group by user_s_id";

        $stmt = $conn->prepare($sql);
        $result = $stmt->executeQuery([$user->getId()]);

        $percents = [];
        foreach ($result->fetchAllAssociative() as $r)
            $percents[$r['serie_id']] = round($r['percent']);

        return $this->render('pages/users/library.html.twig', [
            'series' => $user->getSeries(),
            'percents_watched' => $percents
        ]);
    }
}

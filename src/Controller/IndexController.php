<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use App\Entity\Series;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

class IndexController extends AbstractController
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
            ->findBy([], null, 5); // ne pas afficher si pas connecté

        // $top_series = $entityManager
        //     ->createQueryBuilder()
        //     ->select('s')
        //     ->from(Series::class, 's')
        //     ->join('s.ratings', 'r')
        //     // ->join('s.series_id', 'sea')
        //     // ->join('sea.seaon_id', 'ep')
        //     // ->join('ep.episode_id', 'uep')
        //     // ->where(uep.user_id=)
        //     ->orderBy('r.value', 'DESC')
        //     ->setFirstResult(1)
        //     ->setMaxResults(12)
        //     ->getQuery()->getResult();

        // TODO
        $trending = $entityManager
            ->getRepository(Series::class)
            ->findBy([], null, 10, 10); // multiple de 5 obligé, en afficher plus que 10 si pas connecté

        return $this->render('pages/home.html.twig', [
            'best' => $best,
            'top_5' => $top_5,
            'trending' => $trending,
        ]);
    }

    #[Route('/about', name: 'about')]
    public function about(): Response
    {
        return $this->render('pages/about.html.twig');
    }
}

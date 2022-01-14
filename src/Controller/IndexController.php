<?php

namespace App\Controller;

use App\Entity\Genre;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use App\Entity\Series;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

function limit_result($result, $count) {
    $arr = [];
    for ($i=0; $i < min($count, count($result)); $i++)
        $arr[] = $result[$i];
    return $arr;
}
class IndexController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(EntityManagerInterface $entityManager): Response
    {

        $random_genre = $entityManager
            ->getRepository(Genre::class)
            ->findAll();

        shuffle($random_genre);

        $random_series = $entityManager 
            ->createQueryBuilder()
            ->select('s')
            ->from(Series::class, 's')
            ->join('s.genre', 'g')
            ->join('s.ratings', 'r')
            ->orderBy('s.id', 'DESC')
            ->andWhere('r.value > 4')
            ->andWhere('g.id = :genre')
            ->setParameter('genre', $random_genre[1]->getId())
            ->getQuery()->getResult();

        shuffle($random_series);

        $top_5 = limit_result($random_series, 5);
        $best = null;
        if(count($random_series)>5)
            $best = $random_series[5];
        else
            $best = $random_series[count($random_series) - 1];
        
        $trending = $entityManager 
            ->createQueryBuilder()
            ->select('s')
            ->from(Series::class, 's')
            ->orderBy('s.id', 'DESC')
            ->andWhere('s.awards IS NOT NULL')
            ->setMaxResults(30)
            ->getQuery()->getResult();

        shuffle($trending);

        return $this->render('pages/home.html.twig', [
            'best' => $best,
            'top_5' => $top_5,
            'trending' => limit_result($trending, 10),
        ]);
    }

    #[Route('/about', name: 'about')]
    public function about(): Response
    {
        return $this->render('pages/about.html.twig');
    }
}

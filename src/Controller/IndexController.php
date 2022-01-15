<?php

namespace App\Controller;

use App\Entity\Genre;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use App\Entity\Series;
use Doctrine\ORM\EntityManagerInterface;

function limit_result($result, $count, $offset = 0) {
    $arr = [];
    for ($i = $offset; $i < min($count, count($result)); $i++)
        $arr[] = $result[$i];
    return $arr;
}
class IndexController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(EntityManagerInterface $entityManager): Response
    {

        $random_series = $entityManager 
            ->createQueryBuilder()
            ->select('s')
            ->from(Series::class, 's')
            ->join('s.ratings', 'r')
            ->orderBy('s.id', 'DESC')
            ->andWhere('r.value > 7')
            ->andWhere('s.awards IS NOT NULL')
            ->getQuery()->getResult();
        
        shuffle($random_series);

        return $this->render('pages/home.html.twig', [
            'best' => $random_series[5],
            'top_5' => array_slice($random_series, 0, 5),
            'trending' => array_slice($random_series, 5, 10),
        ]);
    }

    #[Route('/about', name: 'about')]
    public function about(): Response
    {
        return $this->render('pages/about.html.twig');
    }
}

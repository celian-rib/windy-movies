<?php

namespace App\Controller;

use App\Entity\Rating;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use App\Entity\Series;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

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

        // $best = $top_series[0];
        // array_shift($top_series);
        return $this->render('default/home.html.twig', [
            'best' => $best,
            'top_5' => $top_5,
            'trending' => $trending,
        ]);
    }
    
    #[Route('/about', name: 'about')]
    public function about(): Response
    {
        return $this->render('default/about.html.twig');
    }
    
    #[Route('/admin', name: 'admin')]
    public function admin(EntityManagerInterface $entityManager): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        if ($user == null)
            return $this->redirectToRoute('index');
        if ($user->getAdmin() == false)
            return $this->redirectToRoute('index', array('toasterr' => 'You are not admin'));

        $reviews = $entityManager
            ->createQueryBuilder()
            ->select('r')
            ->from(Rating::class, 'r')
            ->orderBy('r.date', 'DESC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();

        return $this->render('default/admin.html.twig', [
            'reviews' => $reviews
        ]);
    }
   
    #[Route('/account', name: 'account')]
    public function account(Request $request, EntityManagerInterface $entityManager): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        if ($user == null)
            return $this->redirectToRoute('index');

        if ($request->isMethod("post")) {
            $delete_user = $user->getId();
            $user_dl = $entityManager
                ->getRepository(User::class)
                ->findOneBy(array('id' => $delete_user));
            $entityManager->remove($user_dl);

            $request->getSession()->invalidate(1);
            $entityManager->flush();
        }

        return $this->render('default/account.html.twig');
    }
}

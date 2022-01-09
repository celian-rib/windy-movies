<?php

namespace App\Controller;

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
        if ($request->isMethod("post") && $delete_account != null) {
            if ($user->getAdmin())
                return $this->redirectToRoute('index', array('toasterr' => 'You cannot delete an admin account'));
            $entityManager->remove($user);
            $entityManager->flush();
            $request->getSession()->invalidate(1);
            return $this->redirectToRoute('index');
        }

        return $this->render('pages/users/account.html.twig', [
            'reviews' => $user->getRatings()
        ]);
    }

    #[Route('/library', name: 'library', methods: ['GET'])]
    public function library(): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        if ($user == null)
            return $this->redirectToRoute('index');
        return $this->render('pages/users/library.html.twig', [
            'series' => $user->getSeries()
        ]);
    }
}

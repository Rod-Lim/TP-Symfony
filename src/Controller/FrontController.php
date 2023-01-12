<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use App\Repository\DishRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FrontController extends AbstractController
{
    #[Route('/', name: 'homepage')]
    public function index(): Response
    {
        return $this->render('front/index.html.twig');
    }

    #[Route('/equipe', name: 'front_team', methods: ['GET'])]
    public function front_team(UserRepository $userRepository): Response
    {
        $users = $userRepository->findAll();

        return $this->render('front/front_team.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('/carte', name: 'front_dishes', methods: ['GET'])]
    public function front_dishes(DishRepository $dishRepository, CategoryRepository $categoryRepository): Response
    {
        $count = $dishRepository->getCountByCategoryId();

        return $this->render('front/front_menu.html.twig', [
            'count' => $count,
        ]);
    }

    #[Route('/carte/{id}', name: 'front_dishes_category', methods: ['GET'], condition: "params['id'] >= 1 && params['id'] <= 3")]
    public function front_dishes_category(DishRepository $dishRepository, CategoryRepository $categoryRepository, int $id): Response
    {
        $category = $categoryRepository->findOneBy(array('id' => $id));
        $dishes =  $dishRepository->findBy(array('category' => $id));
        return $this->render('front/front_dishes.html.twig', [
            'category' => $category,
            'dishes' => $dishes,
        ]);
    }
}

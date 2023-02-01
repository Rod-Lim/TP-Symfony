<?php

namespace App\Controller;

use App\Repository\DishRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class BlockController extends AbstractController
{
    public function dayDishesAction(DishRepository $dishRepository,int $max = 3): Response
    {
        $category = 2;

        $dishes = $dishRepository->findStickies($category,$max);
        return $this->render(
            'Partials/day_dishes.html.twig',
            array('dishes' => $dishes)
        );
    }
}

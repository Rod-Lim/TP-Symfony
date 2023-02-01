<?php

namespace App\Controller;

use App\Entity\Allergen;
use App\Entity\Category;
use App\Entity\Dish;
use App\Entity\User;
use App\Repository\AllergenRepository;
use App\Repository\CategoryRepository;
use App\Repository\DishRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    #[Route('/admin/', name: 'admin_home', methods: ['GET'])]
    public function admin_home(): Response
    {
        return $this->render('admin/index.html.twig');
    }

    #[Route('/admin/import_dishes', name: 'admin_import_dishes', methods: ['GET'])]
    public function admin_import_dishes(EntitymanagerInterface $em): Response
    {
        $data = json_decode(file_get_contents("./files/dishes.json", true), true);

        $dishRepository = $em->getRepository(Dish::class);
        $categoryRepository = $em->getRepository(Category::class);
        $allergenRepository = $em->getRepository(Allergen::class);
        $userRepository = $em->getRepository(User::class);


        foreach (["Desserts", "Entrées", "Plats"] as $type) {
            $category = $categoryRepository->findOneBy(array("Name" => ucfirst($type)));
            // If category does not exist, create it.
            if ($category && isset($data[$type])) {
                foreach ($data[$type] as $dishArray) {
                    $dish = $dishRepository->findOneBy(
                        array("Name" => $dishArray["name"])
                    );
                    if (!$dish) {
                        $dish = new Dish(); // Insert
                    }
                    $dish->setName($dishArray["name"]);
                    $dish->setCategory($category);
                    $dish->setCalories($dishArray["calories"]);
                    $dish->setDescription($dishArray["text"]);
                    $dish->setImage($dishArray["image"]);
                    $dish->setPrice(floatval($dishArray["price"]));
                    $dish->setSticky($dishArray["sticky"]);
                    $dish->setUser($userRepository->find(1));

                    foreach ($dishArray["allergens"] as $allergenName) {
                        $allergen = $allergenRepository->findOneBy(
                            array("Name" => $allergenName)
                        );
                        if (!$allergen) {
                            $allergen = new Allergen();
                        }
                        $allergen->setName($allergenName);

                        $dish->addAllergen($allergen);
                    }
                    $em->persist($dish);
                    $em->flush();
                }
            }
        }
        return $this->redirect('../carte');
    }
}

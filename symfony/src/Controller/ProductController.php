<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{
    #[Route('', name: 'app_product')]
    public function index(ProductRepository $productRepository)
    {
        return $this->render('products/index.html.twig', [
            'products' => $productRepository->findAll(),
        ]);
    }
}

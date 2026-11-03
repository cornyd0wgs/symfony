<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ProductController extends AbstractController
{
    #[Route('/admin/products', name: 'admin_products')]
    public function adminProducts(ProductRepository $productRepository): Response
    {
        $products = $productRepository->findBy([], ['id' => 'DESC']);

        return $this->render('admin/products.html.twig', [
            'current_page' => 'products',
            'products' => $products,
        ]);
    }

    #[Route('/products', name: 'app_products')]
    public function catalog(ProductRepository $productRepository): Response
    {
        $products = $productRepository->findBy(['status' => 'active'], ['id' => 'DESC']);

        return $this->render('product/index.html.twig', [
            'current_page' => 'gallery',
            'products' => $products,
        ]);
    }

    #[Route('/products/{id<\d+>}', name: 'app_product_detail')]
    public function detail(Product $product, ProductRepository $productRepository): Response
    {
        if ($product->getStatus() !== 'active') {
            throw $this->createNotFoundException('Product is not currently available.');
        }

        $relatedProducts = $productRepository->createQueryBuilder('p')
            ->where('p.id != :id')
            ->andWhere('p.status = :status')
            ->setParameter('id', $product->getId())
            ->setParameter('status', 'active')
            ->setMaxResults(3)
            ->getQuery()
            ->getResult();

        return $this->render('product/detail.html.twig', [
            'current_page' => 'product',
            'product' => $product,
            'related_products' => $relatedProducts,
        ]);
    }
}

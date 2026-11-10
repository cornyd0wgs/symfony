<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use App\Security\Voter\ProductVoter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/staff/products')]
#[IsGranted('ROLE_STAFF')]
final class StaffProductController extends AbstractController
{
    #[Route('', name: 'staff_products_index', methods: ['GET'])]
    public function index(Request $request, ProductRepository $productRepository): Response
    {
        $search = trim((string) $request->query->get('q', ''));
        $scope = $request->query->get('scope', 'all'); // 'all' or 'mine'

        $qb = $productRepository->createQueryBuilder('p')
            ->leftJoin('p.createdBy', 'u')
            ->addSelect('u')
            ->leftJoin('p.category', 'c')
            ->addSelect('c')
            ->orderBy('p.id', 'DESC');

        if ($scope === 'mine') {
            $qb->andWhere('p.createdBy = :user')
               ->setParameter('user', $this->getUser());
        }

        if (!empty($search)) {
            $qb->andWhere('p.name LIKE :search OR p.sku LIKE :search')
               ->setParameter('search', '%' . $search . '%');
        }

        $products = $qb->getQuery()->getResult();

        return $this->render('staff/products/index.html.twig', [
            'current_page' => 'staff_products',
            'products' => $products,
            'search' => $search,
            'scope' => $scope,
        ]);
    }

    #[Route('/new', name: 'staff_products_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        $product = new Product();
        $product->setStatus('active');
        $product->setIsfeatured(false);
        $product->setCreatedBy($this->getUser());

        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($product);
            $entityManager->flush();

            $this->addFlash('success', sprintf('Product "%s" created successfully.', $product->getName()));

            return $this->redirectToRoute('staff_products_index');
        }

        return $this->render('staff/products/new.html.twig', [
            'current_page' => 'staff_products',
            'form' => $form,
        ]);
    }

    #[Route('/{id}/edit', name: 'staff_products_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        Product $product,
        EntityManagerInterface $entityManager
    ): Response {
        // Enforce ownership: Staff can ONLY edit their own records, Admin can edit all
        $this->denyAccessUnlessGranted(ProductVoter::EDIT, $product);

        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', sprintf('Product "%s" updated successfully.', $product->getName()));

            return $this->redirectToRoute('staff_products_index');
        }

        return $this->render('staff/products/edit.html.twig', [
            'current_page' => 'staff_products',
            'product' => $product,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'staff_products_delete', methods: ['POST'])]
    public function delete(
        Request $request,
        Product $product,
        EntityManagerInterface $entityManager
    ): Response {
        // Enforce ownership: Staff can ONLY delete their own records
        $this->denyAccessUnlessGranted(ProductVoter::DELETE, $product);

        if ($this->isCsrfTokenValid('delete' . $product->getId(), $request->request->getString('_token'))) {
            $productName = $product->getName();
            $entityManager->remove($product);
            $entityManager->flush();

            $this->addFlash('success', sprintf('Product "%s" deleted successfully.', $productName));
        } else {
            $this->addFlash('error', 'Invalid security token.');
        }

        return $this->redirectToRoute('staff_products_index');
    }
}

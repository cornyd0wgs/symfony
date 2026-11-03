<?php

namespace App\Controller;

use App\Entity\Cart;
use App\Entity\CartItem;
use App\Entity\Product;
use App\Entity\ProductVariant;
use App\Entity\User;
use App\Repository\CartRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class CartController extends AbstractController
{
    private function getOrCreateActiveCart(User $user, EntityManagerInterface $em): Cart
    {
        $cartRepo = $em->getRepository(Cart::class);
        $cart = $cartRepo->findOneBy(['user' => $user, 'status' => 'active']);

        if (!$cart) {
            $cart = new Cart();
            $cart->setUser($user)
                ->setStatus('active')
                ->setCreatedat(new \DateTimeImmutable());
            $em->persist($cart);
            $em->flush();
        }

        return $cart;
    }

    #[Route('/cart', name: 'app_cart')]
    public function index(EntityManagerInterface $em): Response
    {
        /** @var User|null $user */
        $user = $this->getUser();

        if (!$user) {
            return $this->render('cart/index.html.twig', [
                'current_page' => 'cart',
                'items' => [],
                'subtotal' => 0.00,
                'shipping' => 150.00,
                'total' => 150.00,
            ]);
        }

        $cart = $this->getOrCreateActiveCart($user, $em);
        $cartItems = $cart->getItems();

        $subtotal = 0.00;
        $itemsData = [];

        foreach ($cartItems as $item) {
            $variant = $item->getVariant();
            $product = $variant ? $variant->getProduct() : null;

            if (!$variant || !$product) {
                continue;
            }

            $price = (float) ($product->getSaleprice() ?? $product->getBaseprice() ?? 0.00);
            $price += (float) ($variant->getPriceadjustment() ?? 0.00);
            $lineSubtotal = $price * $item->getQuantity();
            $subtotal += $lineSubtotal;

            $itemsData[] = [
                'id' => $item->getId(),
                'product_code' => $variant->getSku() ?: $product->getSku(),
                'name' => $product->getName() . ($variant->getVariantname() ? ' (' . $variant->getVariantname() . ')' : ''),
                'price' => $price,
                'quantity' => $item->getQuantity(),
                'image' => 'images/jacket.png',
                'subtotal' => $lineSubtotal,
            ];
        }

        $shipping = $subtotal > 0 ? 150.00 : 0.00;
        $total = $subtotal + $shipping;

        return $this->render('cart/index.html.twig', [
            'current_page' => 'cart',
            'items' => $itemsData,
            'subtotal' => $subtotal,
            'shipping' => $shipping,
            'total' => $total,
        ]);
    }

    #[Route('/cart/add/{id<\d+>}', name: 'app_cart_add', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function add(Product $product, Request $request, EntityManagerInterface $em): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $cart = $this->getOrCreateActiveCart($user, $em);

        $variantId = $request->request->get('variant_id');
        $quantity = max(1, (int) $request->request->get('quantity', 1));

        $variant = null;
        if ($variantId) {
            $variant = $em->getRepository(ProductVariant::class)->find($variantId);
        }

        // Fallback to the first variant if none selected
        if (!$variant && $product->getVariants()->count() > 0) {
            $variant = $product->getVariants()->first();
        }

        if (!$variant) {
            $this->addFlash('error', 'Selected configuration is unavailable.');
            return $this->redirectToRoute('app_products');
        }

        // Check if item is already in cart
        $existingItem = null;
        foreach ($cart->getItems() as $item) {
            if ($item->getVariant() && $item->getVariant()->getId() === $variant->getId()) {
                $existingItem = $item;
                break;
            }
        }

        if ($existingItem) {
            $existingItem->setQuantity($existingItem->getQuantity() + $quantity);
        } else {
            $newItem = new CartItem();
            $newItem->setCart($cart)
                ->setVariant($variant)
                ->setQuantity($quantity);
            $em->persist($newItem);
        }

        $em->flush();

        $this->addFlash('success', "Allocated {$quantity} unit(s) of {$product->getName()} to your tactical cart manifest.");
        return $this->redirectToRoute('app_cart');
    }

    #[Route('/cart/remove/{id<\d+>}', name: 'app_cart_remove', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function remove(int $id, EntityManagerInterface $em): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $item = $em->getRepository(CartItem::class)->find($id);

        if ($item && $item->getCart()->getUser()->getId() === $user->getId()) {
            $em->remove($item);
            $em->flush();
            $this->addFlash('info', 'Item retracted from tactical cart manifest.');
        }

        return $this->redirectToRoute('app_cart');
    }

    #[Route('/cart/checkout', name: 'app_cart_checkout', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function checkout(EntityManagerInterface $em): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $cart = $em->getRepository(Cart::class)->findOneBy(['user' => $user, 'status' => 'active']);

        if (!$cart || $cart->getItems()->count() === 0) {
            $this->addFlash('error', 'Your cart manifest is empty.');
            return $this->redirectToRoute('app_cart');
        }

        $this->addFlash('success', 'Order requisition dispatched to Caballero Logistics.');
        return $this->redirectToRoute('app_account_orders');
    }
}

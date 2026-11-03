<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Product;
use App\Entity\ProductVariant;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(
        private readonly UserPasswordHasherInterface $passwordHasher
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        // 1. Create Admin User
        $admin = new User();
        $admin->setEmail('admin@caballeroindustries.com')
            ->setFirstname('Site')
            ->setLastname('Admin')
            ->setPhone('+63 917 000 0001')
            ->setStatus('active')
            ->setRoles(['ROLE_ADMIN'])
            ->setCreatedat(new \DateTimeImmutable());
        $admin->setPassword($this->passwordHasher->hashPassword($admin, 'Admin123!'));
        $manager->persist($admin);

        // 2. Create Sample Customer User
        $customer = new User();
        $customer->setEmail('customer@caballeroindustries.com')
            ->setFirstname('Marcus')
            ->setLastname('Vance')
            ->setPhone('+63 917 555 1234')
            ->setStatus('active')
            ->setRoles(['ROLE_USER'])
            ->setCreatedat(new \DateTimeImmutable());
        $customer->setPassword($this->passwordHasher->hashPassword($customer, 'Customer123!'));
        $manager->persist($customer);

        // 3. Create Categories
        $outerwear = new Category();
        $outerwear->setName('Outerwear')
            ->setSlug('outerwear')
            ->setDescription('Tactical jackets, armored vests, and heavy-duty storm shells.')
            ->setStatus('active');
        $manager->persist($outerwear);

        $gear = new Category();
        $gear->setName('Gear & Bags')
            ->setSlug('gear-and-bags')
            ->setDescription('Modular deployment packs, utility pouches, and tactical bags.')
            ->setStatus('active');
        $manager->persist($gear);

        $headwear = new Category();
        $headwear->setName('Headwear')
            ->setSlug('headwear')
            ->setDescription('Low-profile ripstop operator caps and weather-resistant gear.')
            ->setStatus('active');
        $manager->persist($headwear);

        // 4. Products Data
        $catalog = [
            [
                'category' => $outerwear,
                'name' => 'Tactical Recon Vest MK-IV',
                'slug' => 'tactical-recon-vest-mk-iv',
                'sku' => 'CI-VEST-04',
                'description' => 'Lightweight ballistic nylon tactical vest with modular MOLLE webbing, reinforced drag handle, and rapid-release side buckles.',
                'basePrice' => '3900.00',
                'salePrice' => '3510.00',
                'isFeatured' => true,
                'variants' => [
                    ['name' => 'Standard Black', 'size' => 'M', 'color' => 'Black', 'sku' => 'CI-VEST-04-BLK-M', 'adj' => '0.00', 'stock' => 15],
                    ['name' => 'Standard Black', 'size' => 'L', 'color' => 'Black', 'sku' => 'CI-VEST-04-BLK-L', 'adj' => '0.00', 'stock' => 8],
                    ['name' => 'OD Green', 'size' => 'L', 'color' => 'Olive Drab', 'sku' => 'CI-VEST-04-ODG-L', 'adj' => '150.00', 'stock' => 5],
                ],
            ],
            [
                'category' => $outerwear,
                'name' => 'Stealth Ops Heavy Hoodie',
                'slug' => 'stealth-ops-heavy-hoodie',
                'sku' => 'CI-HOOD-01',
                'description' => '500 GSM heavy-fleece tactical hoodie with water-resistant DWR coating, magnetic storm pockets, and reinforced elbow guards.',
                'basePrice' => '2900.00',
                'salePrice' => null,
                'isFeatured' => true,
                'variants' => [
                    ['name' => 'Stealth Grey', 'size' => 'M', 'color' => 'Grey', 'sku' => 'CI-HOOD-01-GRY-M', 'adj' => '0.00', 'stock' => 20],
                    ['name' => 'Stealth Grey', 'size' => 'L', 'color' => 'Grey', 'sku' => 'CI-HOOD-01-GRY-L', 'adj' => '0.00', 'stock' => 12],
                    ['name' => 'Midnight Black', 'size' => 'L', 'color' => 'Black', 'sku' => 'CI-HOOD-01-BLK-L', 'adj' => '0.00', 'stock' => 18],
                ],
            ],
            [
                'category' => $gear,
                'name' => 'Modular Deployment Bag',
                'slug' => 'modular-deployment-bag',
                'sku' => 'CI-BAG-02',
                'description' => 'Weatherproof 1000D Cordura pack featuring tactical organizer dividers, padded laptop sleeve, and laser-cut attachment slots.',
                'basePrice' => '2500.00',
                'salePrice' => '2375.00',
                'isFeatured' => false,
                'variants' => [
                    ['name' => 'Standard 35L', 'size' => '35L', 'color' => 'Tactical Black', 'sku' => 'CI-BAG-02-35L', 'adj' => '0.00', 'stock' => 18],
                    ['name' => 'Expedition 50L', 'size' => '50L', 'color' => 'Tactical Black', 'sku' => 'CI-BAG-02-50L', 'adj' => '400.00', 'stock' => 7],
                ],
            ],
            [
                'category' => $headwear,
                'name' => 'Operator Tactical Cap',
                'slug' => 'operator-tactical-cap',
                'sku' => 'CI-CAP-03',
                'description' => 'Ripstop low-profile cap with hook-and-loop patch panel, breathable mesh ventilation, and quick-dry sweatband.',
                'basePrice' => '1200.00',
                'salePrice' => null,
                'isFeatured' => false,
                'variants' => [
                    ['name' => 'One Size Fits All', 'size' => 'OSFA', 'color' => 'Coyote Tan', 'sku' => 'CI-CAP-03-TAN', 'adj' => '0.00', 'stock' => 30],
                    ['name' => 'One Size Fits All', 'size' => 'OSFA', 'color' => 'Black', 'sku' => 'CI-CAP-03-BLK', 'adj' => '0.00', 'stock' => 25],
                ],
            ],
        ];

        foreach ($catalog as $item) {
            $product = new Product();
            $product->setCategory($item['category'])
                ->setName($item['name'])
                ->setSlug($item['slug'])
                ->setSku($item['sku'])
                ->setDescription($item['description'])
                ->setBaseprice($item['basePrice'])
                ->setSaleprice($item['salePrice'])
                ->setStatus('active')
                ->setIsfeatured($item['isFeatured']);
            $manager->persist($product);

            foreach ($item['variants'] as $vData) {
                $variant = new ProductVariant();
                $variant->setProduct($product)
                    ->setVariantname($vData['name'])
                    ->setSize($vData['size'])
                    ->setColor($vData['color'])
                    ->setSku($vData['sku'])
                    ->setPriceadjustment($vData['adj'])
                    ->setStockquantity($vData['stock'])
                    ->setStatus('active');
                $manager->persist($variant);
            }
        }

        $manager->flush();
    }
}

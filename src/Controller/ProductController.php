<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\Category;
use App\Entity\User;
use App\Form\ProductType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ProductController extends AbstractController
{
    /**
     * @Route("/product", name="product")
     */
    public function index(ManagerRegistry $doctrine): Response
    {
        $category = new Category();
        $category->setCatName('Computer Peripherals');

        $product = new Product();
        $product->setName('Keyboard');
        $product->setPrice(19.99);
        $product->setQuantity(2);
        $product->setDate(new \DateTime(0-2-0));
        $product->setDescription('Ergonomic and stylish!');


        // relates this product to the category
        $product->setCategory($category);

        $entityManager = $doctrine->getManager();
        $entityManager->persist($category);
        $entityManager->persist($product);
        $entityManager->flush();

        return new Response(
            'Saved new product with id: '.$product->getId()
            .' and new category with id: '.$category->getId()
        );
    }
    /**
     * @Route("/insertUser", name="product")
     */
    public function insertAction(ManagerRegistry $doctrine): Response
    {
        $user= new User();

        $user->setEmail('abc@gmail.com');
        $user->setPassword("123@abc");
        // relates this product to the category

        $entityManager = $doctrine->getManager();
        $entityManager->persist($user);
        $entityManager->persist($user);
        $entityManager->flush();

        return new Response(
            'Saved new product with id: '.$user->getId()
            .' and new category with id: '.$user->getId()
        );
    }
    #[Route('/', name: 'product_list')]
    public function listAction(ManagerRegistry $doctrine): Response
    {
        $products = $doctrine->getRepository(Product::class)->findAll();
        // $categoryName = $products->getCategory()->getCatName()->toArray();
        $categories = $doctrine->getRepository(Category::class)->findAll();
        return $this->render('product/index.html.twig', ['products' => $products,
            'categories'=>$categories
        ]);
    }
    /**
     * @Route("/product/productByCat/{id}", name="productByCat")
     */
    public  function productByCatAction(ManagerRegistry $doctrine ,$id):Response
    {
        $category = $doctrine->getRepository(Category::class)->find($id);
        $products = $category->getProducts();
        $categories = $doctrine->getRepository(Category::class)->findAll();
        return $this->render('category/index.html.twig', ['products' => $products,
            'categories'=>$categories]);
    }

    /**
     * @Route("/product/details/{id}", name="product_details")
     */
    public  function detailsAction(ManagerRegistry $doctrine ,$id)
    {
        $categories = $doctrine->getRepository(Category::class)->findAll();
        $products = $doctrine->getRepository(Product::class)->find($id);

        return $this->render('product/details.html.twig', ['products' => $products, 'categories'=>$categories]);
    }
    /**
     * @Route("/product/delete/{id}", name="product_delete")
     */
    public function deleteAction(ManagerRegistry $doctrine,$id)
    {
        $em = $doctrine->getManager();
        $product = $em->getRepository(Product::class)->find($id);
        $em->remove($product);
        $em->flush();

        $this->addFlash(
            'error',
            'Product deleted'
        );

        return $this->redirectToRoute('product_list');
    }

    /**
     * @Route("/product/create", name="product_create", methods={"GET","POST"})
     */
    public function createAction(ManagerRegistry$doctrine,Request $request, SluggerInterface $slugger)
    {
        $categories = $doctrine->getRepository(Category::class)->findAll();
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // uplpad file
            $productImage = $form->get('productImage')->getData();
            if ($productImage) {
                $originalFilename = pathinfo($productImage->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $productImage->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $productImage->move(
                        $this->getParameter('productImages_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    $this->addFlash(
                        'error',
                        'Cannot upload'
                    );// ... handle exception if something happens during file upload
                }
                $product->setProductImage($newFilename);
            }else{
                $this->addFlash(
                    'error',
                    'Cannot upload'
                );// ... handle exception if something happens during file upload
            }
            $em = $doctrine->getManager();
            $em->persist($product);
            $em->flush();

            $this->addFlash(
                'notice',
                'Product Added'
            );
            return $this->redirectToRoute('product_list');
        }
        return $this->renderForm('product/create.html.twig', ['form' => $form, 'categories'=>$categories]);
    }

    /**
     * @Route("/product/edit/{id}", name="product_edit")
     */
    public function editAction(ManagerRegistry $doctrine, int $id,Request $request,SluggerInterface $slugger): Response{
        $entityManager = $doctrine->getManager();
        $categories = $doctrine->getRepository(Category::class)->findAll();
        $product = $entityManager->getRepository(Product::class)->find($id);
        $form = $this->createForm(ProductType::class, @$product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // uplpad file
            $productImage = $form->get('productImage')->getData();
            if ($productImage) {
                $originalFilename = pathinfo($productImage->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $productImage->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $productImage->move(
                        $this->getParameter('productImages_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    $this->addFlash(
                        'error',
                        'Cannot upload'
                    );// ... handle exception if something happens during file upload
                }
                $product->setProductImage($newFilename);
            }else{
                $this->addFlash(
                    'error',
                    'Cannot upload'
                );// ... handle exception if something happens during file upload
            }
            $em = $doctrine->getManager();
            $em->persist($product);
            $em->flush();
            return $this->redirectToRoute('product_list', [
                'id' => $product->getId()
            ]);

        }
        return $this->renderForm('product/edit.html.twig', ['form' => $form, 'categories'=>$categories]);
    }
    public function saveChanges(ManagerRegistry $doctrine,$form, $request, $product)
    {
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $product->setName($request->request->get('product')['name']);
            $product->setCategory($request->request->get('product')['category']);
            $product->setPrice($request->request->get('product')['price']);
            $product->setPrice($request->request->get('product')['quantity']);
            $product->setDescription($request->request->get('product')['description']);
            $product->setDate(\DateTime::createFromFormat('Y-m-d', $request->request->get('product')['date']));
            $em = $doctrine->getManager();
            $em->persist($product);
            $em->flush();

            return true;
        }

        return false;
    }
}

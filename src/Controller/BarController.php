<?php

namespace App\Controller;

use App\Entity\Beer;
use App\Entity\Category;
use App\Entity\Client;
use App\Entity\Country;
use App\Entity\Statistic;
use App\Form\StatFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class BarController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(): Response
    {
        $beerRepository = $this->getDoctrine()->getRepository(Beer::class);
        $beers = $beerRepository->findAll();


        return $this->render('bar/index.html.twig', [
            'beers' => $beers,
            'title' => 'Home'
        ]);
    }


    #[Route('/country/{id}', name:'show_beer')]
    public function showBeer(Country $country) {
/*        $id = $request->get('id');*/
        $beerRepository = $this->getDoctrine()->getRepository(Beer::class);
        $beers = $beerRepository->findBy(['country' => $country->getId()]);
        
        return $this->render('country/index.html.twig', [
            'beers' => $beers,
            'title' => 'Country'
        ]);
    }

    #[Route('/category/{id}', name:'show_beer_category')]
    public function category(Category $category){

        return $this->render('category/index.html.twig', [
            'beers' => $category->getBeers() ?? [],
            'title' => $category->getName()
        ]);
    }

    #[Route('/menu', name:'menu')]
    public function mainMenu(string $routeName, int $cateId = null): Response {

        $categories = $this->getDoctrine()->getRepository(Category::class)->findBy(['term' => 'normal']);

        return $this->render('partials/menu.html.twig', [
            'route_name' => $routeName,
            'category_id' => $cateId,
            'categories' => $categories
        ]);
    }

    /**
     * @Route("/showbeer/{id_beer}/{id_client}", name="show_beer_byid")
     *
     * @ParamConverter("beer", options={"mapping": {"id_beer" : "id"}})
     * @ParamConverter("client", options={"mapping": {"id_client"   : "id"}})
     *
     */
    public function showBeerById(Request $request, Beer $beer, Client $client = null): Response
    {
        $statistic = new Statistic();

        $form = $this->createForm(StatFormType::class, $statistic);

        $form->handleRequest($request);

        if($client != null) {
            $notVoted = true;
            $statistics = $client->getStatistics();
            foreach($statistics as $statistic) {
                if($beer->getId() === $statistic->getBeer()->getId()) {
                    $notVoted = false;
                }
            }
        }

        if($form->isSubmitted() ) {
            /**
             * @var Statistic $statisticFilled
             */
            $statisticFilled = $form->getData();
            $statisticFilled->setClient($client);
            $statisticFilled->setBeer($beer);

            $em = $this->getDoctrine()->getManager();
            $em->persist($statistic);
            $em->flush();
        }

    return $this->render('bar/beer.html.twig', [
        'beer' => $beer,
        'title' => 'Show Beer',
        'form' => $form->createView()
    ]);
    }
}

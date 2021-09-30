<?php

namespace App\Controller;

use App\Entity\Statistic;
use App\Entity\Beer;
use App\Entity\Client;
use App\Form\StatFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StatisticController extends AbstractController
{
    #[Route('/statistics', name:'statistics')]
    public function showStatistics() {
        $beerRepository = $this->getDoctrine()->getRepository(Beer::class);
        $beers = $beerRepository->findAll();

        return $this->render('statistic/statistics.html.twig', [
            'beers' => $beers,
            'title' => 'statistics'
        ]);
    }
}

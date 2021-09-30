<?php

namespace App\Twig;

use App\Entity\Beer;
use Twig\TwigFilter;
use Twig\TwigFunction;
use Twig\Extension\AbstractExtension;
use Doctrine\Persistence\ObjectManager;

class Custom extends AbstractExtension
{
    private $manager;

    public function __construct(ObjectManager $manager)
    {
        $this->manager = $manager;
    }

    public function getFilters()
    {
        return [
            // avg est le nom du filter : [11, 16,19] | avg
            new TwigFilter('avg', function (array $numbers) {
                return array_sum($numbers);
            })
        ];
    }

    public function getFunctions()
    {
        return [
            // special est le nom de la fonction appelée dans les templates Twig
            new TwigFunction('special', function (Beer $beer) {
                // on fait une requête from scratch directement dans le repository Beer
                return $this->manager->getRepository(Beer::class)->findByCatTerm('special', $beer->getId());
            }),
            new TwigFunction('normal', function (Beer $beer) {
                return $this->manager->getRepository(Beer::class)->findByCatTerm('normal', $beer->getId());
            }),

        ];
    }
}

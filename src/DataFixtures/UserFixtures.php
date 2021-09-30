<?php

namespace App\DataFixtures;

use App\Entity\Client;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    private $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager)
    {
        $user = new User();
        $user->setEmail('test@email.fr');
        $user->setRoles(['ROLE_VISITEUR', 'ROLE_ADMIN']);

        $client = new Client();
        $client->setName('Quentin');
        $client->setWeight(105);
        $client->setEmail($user->getEmail());
        $client->setNumberBeer(0);
        $client->setAge(45);

        $user->setClient($client);
        $user->setPassword($this->passwordHasher->hashPassword(
            $user,
            'test'
        ));

        $manager->persist($user);
        $manager->persist($client);

        $manager->flush();
    }
}

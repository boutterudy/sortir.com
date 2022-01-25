<?php

namespace App\DataFixtures;

use App\Entity\Campus;
use App\Entity\Outing;
use App\Entity\Place;
use App\Entity\Status;
use App\Entity\Town;
use App\Entity\User;
use App\Repository\CampusRepository;
use App\Repository\OutingRepository;
use App\Repository\PlaceRepository;
use App\Repository\StatusRepository;
use App\Repository\TownRepository;
use App\Repository\UserRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $hasher;
    private UserRepository $userRepository;
    private CampusRepository $campusRepository;
    private OutingRepository $outingRepository;
    private PlaceRepository $placeRepository;
    private TownRepository $townRepository;
    private StatusRepository $statusRepository;

    public function __construct(
        UserPasswordHasherInterface $hasher,
        UserRepository $userRepository,
        CampusRepository $campusRepository,
        OutingRepository $outingRepository,
        PlaceRepository $placeRepository,
        TownRepository $townRepository,
        StatusRepository $statusRepository
    )
    {
        $this->hasher = $hasher;
        $this->userRepository = $userRepository;
        $this->campusRepository = $campusRepository;
        $this->outingRepository = $outingRepository;
        $this->placeRepository = $placeRepository;
        $this->townRepository = $townRepository;
        $this->statusRepository = $statusRepository;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr-FR');

        for($i = 0 ; $i < 5 ; $i++){
            $campus = new Campus();
            $campus->setName($faker->company());

            $manager->persist($campus);
        }
        $manager->flush();

        $campuses = $this->campusRepository->findAll();

        $user = new User();
        $user->setNickName('Usertest')
            ->setLastName('Test')
            ->setFirstName('User')
            ->setPhoneNumber($faker->phoneNumber())
            ->setEmail($faker->email())
            ->setIsAdmin(true)
            ->setIsActive(true)
            ->setPassword($this->hasher->hashPassword($user, 'test'))
            ->setCampus($faker->randomElement($campuses))
            ->setRoles(['ROLE_ADMIN']);

        $manager->persist($user);


        for ($i = 0; $i < 10 ; $i++){
            $user = new User();
            $user->setNickName($faker->userName())
                ->setLastName($faker->name())
                ->setFirstName($faker->firstName())
                ->setPhoneNumber($faker->phoneNumber())
                ->setEmail($faker->email())
                ->setIsAdmin(false)
                ->setIsActive($faker->randomElement([true,false]))
                ->setPassword($this->hasher->hashPassword($user, $user->getNickName()))
                ->setCampus($faker->randomElement($campuses))
                ->setRoles(['ROLE_USER']);

            $manager->persist($user);

            $town = new Town();
            $town->setName($faker->city())
                ->setPostalCode($faker->postcode());
            $manager->persist($town);

            $status = new Status();
            $status->setLibelle($faker->currencyCode());
            $manager->persist($status);
        }

        $manager->flush();

        $towns = $this->townRepository->findAll();
        for ($i = 0; $i < 20; $i++)
        {
            $place = new Place();
            $place->setName($faker->name)
                ->setStreet($faker->address())
                ->setLatitude($faker->latitude())
                ->setLongitude($faker->longitude())
                ->setTown($faker->randomElement($towns));
            $manager->persist($place);
        }

        $manager->flush();

        $users = $this->userRepository->findAll();
        $places = $this->placeRepository->findAll();
        $statuses = $this->statusRepository->findAll();

        for ($i = 0; $i < 30; $i++)
        {
            $outing = new Outing();
            $daysDuration = random_int(1,14);
            $outing->setName($faker->firstName())
                ->setStartAt($faker->dateTimeBetween('- 6 months', '+ 6 months'))
                ->setLimitSubscriptionDate(\DateTime::createFromFormat('U', $outing->getStartAt()->getTimestamp()-7*60*60*24))
                ->setDuration(\DateInterval::createFromDateString($daysDuration . ' days'))
                ->setAbout($faker->realText)
                ->setMaxUsers(random_int(5,20))
                ->setCampus($faker->randomElement($campuses))
                ->setPlace($faker->randomElement($places))
                ->setStatus($faker->randomElement($statuses))
                ->setOrganizer($faker->randomElement($users));

            $manager->persist($outing);
        }
        $manager->flush();

        $outings = $this->outingRepository->findAll();

        foreach ($outings as $outing){
            foreach ($users as $user){
                $subscribe = random_int(0,10);
                if ($subscribe > 6){
                    $outing->addUser($user);
                }
                $manager->persist($user);
                $manager->persist($outing);
            }
        }
        $manager->flush();
    }


}

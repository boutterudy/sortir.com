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
    ) {
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


        $campusNantes = new Campus();
        $campusNantes->setName('Nantes');
        $manager->persist($campusNantes);

        $campusRennes = new Campus();
        $campusRennes->setName('Rennes');
        $manager->persist($campusRennes);

        $campusQuimper = new Campus();
        $campusQuimper->setName('Quimper');
        $manager->persist($campusQuimper);

        $campusNiort = new Campus();
        $campusNiort->setName('Niort');
        $manager->persist($campusNiort);

        $manager->flush();
        $campuses = $this->campusRepository->findAll();

        $userAdmin = new User();
        $userAdmin->setNickName('Admin')
            ->setLastName('Istrateur')
            ->setFirstName('Admin')
            ->setPhoneNumber('0102030405')
            ->setEmail($faker->email())
            ->setIsAdmin(true)
            ->setIsActive(true)
            ->setPassword($this->hasher->hashPassword($userAdmin, 'Pa$$w0rd'))
            ->setCampus($faker->randomElement($campuses));

        $manager->persist($userAdmin);


        for ($i = 0; $i < 50 ; $i++) {
            $phoneNumber = '0';
            for ($j = 0; $j < 9 ; $j++) {
                $phoneNumber.= random_int(0, 9);
            }

            $user = new User();
            $user->setNickName($faker->userName())
                ->setLastName($faker->name())
                ->setFirstName($faker->firstName())
                ->setPhoneNumber($phoneNumber)
                ->setEmail($faker->email())
                ->setIsAdmin(false)
                ->setIsActive($faker->randomElement([true,false]))
                ->setPassword($this->hasher->hashPassword($user, 'Pa$$w0rd'))
                ->setCampus($faker->randomElement($campuses));
            $manager->persist($user);
        }


        $townNantes = new Town();
        $townNantes->setName('Nantes')
            ->setPostalCode('44000');
        $manager->persist($townNantes);

        $townRennes = new Town();
        $townRennes->setName('Rennes')
            ->setPostalCode('35000');
        $manager->persist($townRennes);

        $townQuimper = new Town();
        $townQuimper->setName('Quimper')
            ->setPostalCode('29000');
        $manager->persist($townQuimper);

        $townNiort = new Town();
        $townNiort->setName('Niort')
            ->setPostalCode('79000');
        $manager->persist($townNiort);

        $manager->flush();
        $towns = $this->townRepository->findAll();

        $statusCreating = new Status();
        $statusCreating->setLibelle('En cr??ation');
        $manager->persist($statusCreating);

        $statusOpened = new Status();
        $statusOpened->setLibelle('Ouverte');
        $manager->persist($statusOpened);

        $statusClosed = new Status();
        $statusClosed->setLibelle('Cl??tur??e');
        $manager->persist($statusClosed);

        $statusInProgress = new Status();
        $statusInProgress->setLibelle('En cours');
        $manager->persist($statusInProgress);

        $statusEnded = new Status();
        $statusEnded->setLibelle('Termin??e');
        $manager->persist($statusEnded);

        $statusCanceled = new Status();
        $statusCanceled->setLibelle('Annul??e');
        $manager->persist($statusCanceled);

        $statusFiled = new Status();
        $statusFiled->setLibelle('Archiv??e');
        $manager->persist($statusFiled);

        $statuses = $this->statusRepository->findAll();

        foreach ($towns as $town) {
            for ($i = 0; $i < 10; $i++) {
                $place = new Place();
                $place->setName($faker->name)
                    ->setStreet($faker->address())
                    ->setLatitude($faker->latitude())
                    ->setLongitude($faker->longitude())
                    ->setTown($town);
                $manager->persist($place);
            }
        }
        $manager->flush();

        $users = $this->userRepository->findAll();
        $places = $this->placeRepository->findAll();

        $activities = ['Mus??e', 'Bowling', 'Lasergame', 'Karting', 'Parapente', 'Ski nautique', 'Surf', 'P??te ?? modeler', 'Equitation', 'Danse classique'];

        //Cr??ation d'activit??s 'En cr??ation'
        for ($i = 0; $i < 10; $i++) {
            $daysToSubscribe = random_int(1, 14)*60*60*24;
            $outing = new Outing();
            $outing->setName($faker->randomElement($activities))
                ->setStartAt($faker->dateTimeBetween('+ 1 month', '+ 6 months'))
                ->setLimitSubscriptionDate(\DateTime::createFromFormat('U', $outing->getStartAt()->getTimestamp()-$daysToSubscribe))
                ->setDuration(random_int(60, 360))
                ->setAbout($faker->realText)
                ->setMaxUsers(random_int(5, 20))
                ->setCampus($faker->randomElement($campuses))
                ->setPlace($faker->randomElement($places))
                ->setOrganizer($faker->randomElement($users))
                ->setStatus($statusCreating);

            $manager->persist($outing);
        }

        //Cr??ation d'activit??s 'Ouverte'
        for ($i = 0; $i < 10; $i++) {
            $daysToSubscribe = random_int(1, 14)*60*60*24;
            $outing = new Outing();
            $outing->setName($faker->randomElement($activities))
                ->setStartAt($faker->dateTimeBetween('+ 2 weeks', '+ 6 weeks'))
                ->setLimitSubscriptionDate(\DateTime::createFromFormat('U', $outing->getStartAt()->getTimestamp()-$daysToSubscribe))
                ->setDuration(random_int(60, 360))
                ->setAbout($faker->realText)
                ->setMaxUsers(random_int(5, 20))
                ->setCampus($faker->randomElement($campuses))
                ->setPlace($faker->randomElement($places))
                ->setOrganizer($faker->randomElement($users))
                ->setStatus($statusOpened);

            $manager->persist($outing);
        }

        //Cr??ation d'activit??s 'Cl??tur??e'
        for ($i = 0; $i < 10; $i++) {
            $daysToSubscribe = random_int(1, 14)*60*60*24;
            $outing = new Outing();
            $outing->setName($faker->randomElement($activities))
                ->setStartAt($faker->dateTimeBetween('+ 1 day', '+ ' . $daysToSubscribe . ' seconds'))
                ->setLimitSubscriptionDate(\DateTime::createFromFormat('U', $outing->getStartAt()->getTimestamp()-$daysToSubscribe))
                ->setDuration(random_int(60, 360))
                ->setAbout($faker->realText)
                ->setMaxUsers(random_int(5, 20))
                ->setCampus($faker->randomElement($campuses))
                ->setPlace($faker->randomElement($places))
                ->setOrganizer($faker->randomElement($users))
                ->setStatus($statusClosed);

            $manager->persist($outing);
        }

        //Cr??ation d'activit??s 'En cours'
        for ($i = 0; $i < 10; $i++) {
            $daysToSubscribe = random_int(1, 14)*60*60*24;
            $outing = new Outing();
            $outing->setName($faker->randomElement($activities))
                ->setStartAt(new \DateTime())
                ->setLimitSubscriptionDate(\DateTime::createFromFormat('U', $outing->getStartAt()->getTimestamp()-$daysToSubscribe))
                ->setDuration(random_int(60, 360))
                ->setAbout($faker->realText)
                ->setMaxUsers(random_int(5, 20))
                ->setCampus($faker->randomElement($campuses))
                ->setPlace($faker->randomElement($places))
                ->setOrganizer($faker->randomElement($users))
                ->setStatus($statusInProgress);

            $manager->persist($outing);
        }

        //Cr??ation d'activit??s 'Termin??e'
        for ($i = 0; $i < 10; $i++) {
            $daysToSubscribe = random_int(1, 14)*60*60*24;
            $outing = new Outing();
            $outing->setName($faker->randomElement($activities))
                ->setStartAt($faker->dateTimeBetween('- 1 month', '- 1 day'))
                ->setLimitSubscriptionDate(\DateTime::createFromFormat('U', $outing->getStartAt()->getTimestamp()-$daysToSubscribe))
                ->setDuration(random_int(60, 360))
                ->setAbout($faker->realText)
                ->setMaxUsers(random_int(5, 20))
                ->setCampus($faker->randomElement($campuses))
                ->setPlace($faker->randomElement($places))
                ->setOrganizer($faker->randomElement($users))
                ->setStatus($statusEnded);

            $manager->persist($outing);
        }

        //Cr??ation d'activit??s 'Annul??e'
        for ($i = 0; $i < 10; $i++) {
            $daysToSubscribe = random_int(1, 14)*60*60*24;
            $outing = new Outing();
            $outing->setName($faker->randomElement($activities))
                ->setStartAt($faker->dateTimeBetween('- 1 month', '+ 1 month'))
                ->setLimitSubscriptionDate(\DateTime::createFromFormat('U', $outing->getStartAt()->getTimestamp()-$daysToSubscribe))
                ->setDuration(random_int(60, 360))
                ->setAbout($faker->realText)
                ->setMaxUsers(random_int(5, 20))
                ->setCampus($faker->randomElement($campuses))
                ->setPlace($faker->randomElement($places))
                ->setOrganizer($faker->randomElement($users))
                ->setStatus($statusCanceled);

            $manager->persist($outing);
        }

        //Cr??ation d'activit??s 'Archiv??e'
        for ($i = 0; $i < 10; $i++) {
            $daysToSubscribe = random_int(1, 14)*60*60*24;
            $outing = new Outing();
            $outing->setName($faker->randomElement($activities))
                ->setStartAt($faker->dateTimeBetween('- 1 year', '- 1 month'))
                ->setLimitSubscriptionDate(\DateTime::createFromFormat('U', $outing->getStartAt()->getTimestamp()-$daysToSubscribe))
                ->setDuration(random_int(60, 360))
                ->setAbout($faker->realText)
                ->setMaxUsers(random_int(5, 20))
                ->setCampus($faker->randomElement($campuses))
                ->setPlace($faker->randomElement($places))
                ->setOrganizer($faker->randomElement($users))
                ->setStatus($statusCanceled);

            $manager->persist($outing);
        }

        $manager->flush();

        $outings = $this->outingRepository->findAll();

        foreach ($outings as $outing) {
            if ($outing->getStatus()->getLibelle() != 'En cr??ation') {
                foreach ($users as $user) {
                    $subscribe = random_int(0, 10);
                    if ($subscribe > 8 && $outing->getUsers()->count() < $outing->getMaxUsers()) {
                        $outing->addUser($user);
                    }
                    $manager->persist($user);
                    $manager->persist($outing);
                }
                if ($outing->getStatus()->getLibelle() == 'Ouverte' && $outing->getUsers()->count() == $outing->getMaxUsers()) {
                    $outing->setStatus($statusClosed);
                }
                $manager->persist($outing);
            }
        }
        $manager->flush();
    }
}

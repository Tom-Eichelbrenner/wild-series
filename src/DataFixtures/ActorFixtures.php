<?php

namespace App\DataFixtures;

use Faker;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use App\Entity\Actor;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class ActorFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $faker = Faker\Factory::create('us_US');
        for ($i = 1; $i<=50;$i++){
            $actor = new Actor();
            $actor->setName($faker->name);
            $actor->addProgram($this->getReference('program_'.rand(0,5)));
            $actor->addProgram($this->getReference('program_'.rand(0,5)));
            $manager->persist($actor);
            $this->addReference('actor_'. $i, $actor);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [ProgramFixtures::class];
    }
}

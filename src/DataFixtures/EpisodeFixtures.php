<?php

namespace App\DataFixtures;

use App\Entity\Episode;
use App\Entity\Season;
use Faker;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use App\Entity\Actor;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class EpisodeFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $ep = 0;
        $sea = 1;
        $faker = Faker\Factory::create('fr_FR');
        for ($i = 0; $i<=59;$i++){

            if ($ep == 5){
                $sea++;
            }
            $episode = new Episode();
            if($i%5==0){
                $ep=0;
            }
            $ep++;
            $episode->setNumber($ep);

            $episode->setTitle($faker->word);
            $episode->setSynopsis($faker->text);
            $episode->setSeason($this->getReference('season_'.$sea));
            $manager->persist($episode);
            $this->addReference('episode_'. $i, $episode);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [SeasonFixtures::class];
    }
}

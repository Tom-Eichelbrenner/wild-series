<?php

namespace App\DataFixtures;

use App\Entity\Season;
use Faker;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use App\Entity\Actor;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class SeasonFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $faker = Faker\Factory::create('us_US');
        for ($i = 1; $i<=12;$i++){
            $season = new Season();
            if ($i%2==1){
                $season->setNumber(1);
            }else{
                $season->setNumber(2);
            }
            $season->setYear($faker->year);
            $season->setDescription($faker->text);
            if ($i==1 or $i==2){
                $season->setProgram($this->getReference('program_0'));
            }elseif($i==3 or $i ==4){
                $season->setProgram($this->getReference('program_1'));
            }elseif($i==6 or $i ==5){
                $season->setProgram($this->getReference('program_2'));
            }elseif($i==7 or $i ==8){
                $season->setProgram($this->getReference('program_3'));
            }elseif($i==10 or $i ==9){
                $season->setProgram($this->getReference('program_4'));
            }elseif($i==11 or $i ==12){
                $season->setProgram($this->getReference('program_5'));
            }
            $manager->persist($season);
            $this->addReference('season_'. $i, $season);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [ProgramFixtures::class];
    }
}

<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Contract;
use App\Service\Helper;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\Persistence\ObjectManager;

class ContractFixtures extends Fixture implements FixtureGroupInterface
{
    public function load(ObjectManager $manager)
    {
        foreach ($this->getContractList() as $contractItem) {
            $contract = new Contract();
            $contract->setName($contractItem);
            $manager->persist($contract);
        }
        $manager->flush();
    }

    public function getContractList()
    {
        return [
            'Full Time',
            'Part Time'
        ];
    }

    public static function getGroups(): array
    {
        return ['contract'];
    }
}

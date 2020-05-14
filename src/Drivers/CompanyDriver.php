<?php


namespace App\Drivers;


use App\Entity\Company;
use App\Entity\Location;
use App\Repository\CompanyRepository;
use App\Service\FileManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Geocoder\ProviderAggregator;
use Geocoder\Query\GeocodeQuery;
use Symfony\Contracts\Translation\TranslatorInterface;

class CompanyDriver
{
    protected $repo;
    protected $em;
    protected $aggregator;

    public function __construct(
        CompanyRepository $companyRepository,
        EntityManagerInterface $entityManager,
        ProviderAggregator $aggregator
    )
    {
        $this->repo = $companyRepository;
        $this->em = $entityManager;
        $this->aggregator = $aggregator;
    }

    public function saveCompany(Company $company) : void
    {
        $locationData = $this->aggregator->geocodeQuery(GeocodeQuery::create($company->getAddress()));
        $location = $company->getLocation() ?? new Location();

        $location->setAddress($locationData->first()->getFormattedAddress())
            ->setCity($locationData->first()->getLocality())
            ->setCountry($locationData->first()->getCountry())
            ->setState($locationData->first()->getAdminLevels()->first()->getName())
            ->setLat($locationData->first()->getCoordinates()->getLatitude())
            ->setLon($locationData->first()->getCoordinates()->getLongitude());
        $company->setLocation($location);
        $company->setAddress($locationData->first()->getFormattedAddress());

        $this->em->persist($company);
        $this->em->flush();
    }


}
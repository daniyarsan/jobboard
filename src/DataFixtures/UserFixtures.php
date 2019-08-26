<?php

namespace App\DataFixtures;

use App\Entity\Company;
use App\Entity\Job;
use App\Entity\Profile;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{
    public const JS_NUM = 20;

    public const EMP_NUM = 20;
    public const JOBS_NUM = 5;

    public const USER_PASS = '121212';
    public const USER_ROLE = 'ROLE_PROFILE';
    public const COMPANY_ROLE = 'ROLE_COMPANY';

    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
        $this->faker = Factory::create();
    }

    public function load(ObjectManager $manager)
    {
        /* Load super User */
        $superUser = new User();
        $superUser->setEmail('daniyar.san@gmail.com');
        $superUser->setPassword($this->encoder->encodePassword($superUser, '121212'));
        $superUser->setRoles(['ROLE_ADMIN']);
        $manager->persist($superUser);
        /* Load super User */

        /* Load Job Seekers */
        for ($i = 0; $i <= self::JS_NUM; $i++) {
            $name = $this->faker->firstName;
            $lastName = $this->faker->lastName;

            $user = new User();
            $user->setPassword($this->encoder->encodePassword($user, self::USER_PASS));
            $user->setEmail($this->faker->email);
            $user->setRoles([self::USER_ROLE]);

            $profile = new Profile();
            $profile->setFirstName($name);
            $profile->setLastName($lastName);
            $profile->setCountry($this->faker->country);
            $profile->setDescription($this->faker->text);
            $profile->setAddress($this->faker->address);
            $user->setProfile($profile);
            $manager->persist($user);
        }

        /* Load Job Seekers */

        /* Load Employers */
        for ($i = 0; $i <= self::EMP_NUM; $i++) {
            $user = new User();
            $user->setPassword($this->encoder->encodePassword($user, self::USER_PASS));
            $user->setEmail($this->faker->companyEmail);
            $user->setRoles([self::COMPANY_ROLE]);

            $company = new Company();
            $company->setName($this->faker->company);
            $company->setIsVerified(true);
            $company->setCountry($this->faker->country);
            $company->setAddress($this->faker->address);
            $company->setDescription($this->faker->text);
            $company->setEmail($this->faker->companyEmail);
            $company->setLongitude($this->faker->longitude);
            $company->setLatitude($this->faker->latitude);
            $company->getWebsite($this->faker->domainName);
            $company->setState($this->faker->state);
            $company->getPhone($this->faker->phoneNumber);
            $company->setJobs($this->getJobsCollection($company));

            $user->setCompany($company, $manager);
            $manager->persist($user);
        }
        /* Load Employers */

        $manager->flush();
    }

    public function getJobsCollection(Company $company): ArrayCollection
    {
        $jobs = new ArrayCollection();

        for ($i = 0; $i < self::JOBS_NUM; $i++) {
            $jobEntity = new Job();
            $jobEntity->setTitle($this->faker->jobTitle);
            $jobEntity->setDescription($this->faker->realText(rand(10,20)));
            $jobEntity->setCompany($company);
            $jobEntity->setIsPublished(true);
            $jobEntity->setCountry($this->faker->country);
            $jobEntity->setSalary($this->faker->numberBetween(0, 100000));

            $jobs->add($jobEntity);
        }

        return $jobs;
    }

}

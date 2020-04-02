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
    public const PROFILES_NUM = 3;
    public const COMPANIES_NUM = 3;
    public const JOBS_NUM = 3;
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
        $superUser->setEmail(User::DEMO_EMAIL);
        $superUser->setPassword($this->encoder->encodePassword($superUser, USER::DEMO_PASSWORD));
        $superUser->setRoles([User::ROLE_ADMIN]);
        $manager->persist($superUser);
        /* Load super User */

        /* Load Job Seekers */
        for ($i = 0; $i <= self::PROFILES_NUM; $i++) {
            $name = $this->faker->firstName;
            $lastName = $this->faker->lastName;

            $email = $this->faker->email;
            $user = new User();
            $user->setPassword($this->encoder->encodePassword($user, USER::DEMO_PASSWORD));
            $user->setEmail($email);
            $user->setRoles([User::ROLE_PROFILE]);

            $profile = new Profile();
            $profile->setFirstName($name);
            $profile->setEmail($email);
            $profile->setLastName($lastName);
            $user->setProfile($profile);
            $manager->persist($user);
        }

        /* Load Job Seekers */

        /* Load Employers */
        for ($i = 0; $i <= self::COMPANIES_NUM; $i++) {
            $user = new User();
            $user->setPassword($this->encoder->encodePassword($user, USER::DEMO_PASSWORD));
            $user->setEmail($this->faker->companyEmail);
            $user->setRoles([User::ROLE_COMPANY]);

            $company = new Company();
            $company->setName($this->faker->company);
            $company->setIsVerified(true);
            $company->setCountry($this->faker->country);
            $company->setAddress($this->faker->address);
            $company->setDescription($this->faker->text);
            $company->setEmail($this->faker->companyEmail);
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
            $jobEntity->setActive(true);
            $jobEntity->setCountry($this->faker->country);

            $jobs->add($jobEntity);
        }

        return $jobs;
    }

}

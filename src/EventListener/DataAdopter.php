<?php


namespace App\EventListener;


use App\Entity\Category;
use App\Entity\Job;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Doctrine\ORM\EntityManagerInterface;

class DataAdopter
{

    protected $manager;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();

        // if this listener only applies to certain entity types,
        // add some code to check the entity type as early as possible
        if (!$entity instanceof Job) {
            return;
        }

        $job = $args->getObject();
        $categoryName = $job->getCategoryString();

        /* Store Category if exist or Create new Category if doesnt exist */
        if (!empty($categoryName)) {
            $categories = $this->manager->getRepository('App:Category')->findCategoryByKeyword($categoryName);

            if ($categories) {
                $job->setCategories(new ArrayCollection($categories));
            } else {
                $category = new Category();
                $category->setName($categoryName);
                $job->addCategory($category);
                $this->manager->persist($category);
                $this->manager->flush();
            }
        }

        /* Store Category if exist or Create new Category if doesnt exist */

    }
}
<?php


namespace App\EventListener;


use App\Entity\Category;
use App\Entity\Discipline;
use App\Entity\Job;
use App\Service\Data\States;
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


        /* Job Data Adoption */
        if ($entity instanceof Job) {
            $job = $args->getObject();
            $disciplineString = $job->getDiscipline();
            if (is_string($disciplineString)) {
                $disciplineEntity = $this->manager->getRepository('App:Discipline')->findDisciplineByKeyword($disciplineString);
                $job->setDiscipline($disciplineEntity);
            }

            /** Store Category if exist or Create new Category if doesnt exist */
            $categoryName = $job->getCategoryString();
            if (!empty($categoryName)) {
                $categories = $this->manager->getRepository('App:Category')->findCategoryByKeyword($categoryName);

                if ($categories) {
                    $job->setCategoriesCollection(new ArrayCollection($categories));
                } else {
                    $category = new Category();
                    $category->setName($categoryName);
                    $job->addCategory($category);
                    $this->manager->persist($category);
                    $this->manager->flush();
                }
            }
            /** Store Category if exist or Create new Category if doesnt exist */

            /** Transform short State into Long State during import */
            if (preg_match('/^[A-Z]{2}$/', $job->getState())) {
                $job->setState((States::list())[ $job->getState() ]);
            }
            /** Transform short State into Long State during import */
        }

        return;

    }
}
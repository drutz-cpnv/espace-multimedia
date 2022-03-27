<?php

namespace App\Services\Tasks;

use App\Entity\Teacher;
use App\Repository\TeacherRepository;
use App\Services\IntranetAPI;
use Doctrine\ORM\EntityManagerInterface;

class UpdateTeachers
{

    private IntranetAPI $intranetAPI;

    public function __construct(IntranetAPI $intranetAPI, private TeacherRepository $teacherRepository, private EntityManagerInterface $entityManager)
    {
        $this->intranetAPI = $intranetAPI;
    }

    public function update()
    {

        foreach ($this->intranetAPI->teachers() as $teacher) {
            $dbTeacher = $this->teacherRepository->findOneByFriendlyId($teacher->getFriendlyId());
            if(is_null($dbTeacher)) {
                $this->entityManager->persist($teacher);
            }
            else {
                $dbTeacher->setLastname($teacher->getLastname());
                $dbTeacher->setFirstname($teacher->getFirstname());
                $dbTeacher->setEmail($teacher->getEmail());
                $dbTeacher->setAcronym($teacher->getAcronym());
                $dbTeacher->setFriendlyID($teacher->getFriendlyId());
                $dbTeacher->setUpdatedAt(new \DateTimeImmutable());
            }
        }

        $this->entityManager->flush();

        return true;

    }

}
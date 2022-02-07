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
        }

        $this->entityManager->flush();

        return true;

    }

}
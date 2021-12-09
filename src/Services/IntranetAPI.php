<?php

namespace App\Services;

use App\Entity\Intranet\Classes;
use App\Entity\Intranet\Student;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use JetBrains\PhpStorm\Pure;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class IntranetAPI
{

    const BASE_URI = "https://intranet.cpnv.ch";

    private array $query = [];

    const CLASS_ENDPOINT = "/classes";
    const STUDENT_ENDPOINT = "/current_students";

    const RESPONSE_FORMAT = ".json";

    private string $queryString;

    private array $requests = [];

    public function __construct(private string $api_secret, private string $api_key, private HttpClientInterface $httpClient)
    {
    }

    #[Pure] public function getRequest(): ArrayCollection
    {
        return new ArrayCollection($this->requests);
    }

    /**
     * @param null $scope
     * @param bool[] $extra
     * @return ArrayCollection
     */
    public function classes($scope = null, array $extra = ['STUDENTS' => true, 'SECTION' => true, 'SECTIONS' => true, 'STUDENTS_COUNT' => true, 'FINAL' => true]): ArrayCollection
    {
        $extra = new ArrayCollection($extra);
        if($extra->contains(true)) {
            $extras = "";
            if ($extra->get('SECTION')) {
                $extras .= "sections";
            }
            if ($extra->get('STUDENTS')) {
                $extras .= ",students";
            }
            if ($extra->get('SECTIONS')) {
                $extras .= ",sections";
            }
            if ($extra->get('STUDENTS_COUNT')) {
                $extras .= ",students_count";
            }
            if ($extra->get('FINAL')) {
                $extras .= ",final";
            }

            $this->query['alter[extra]'] = $extras;
        }

        if(!is_null($scope)){
            $query = self::BASE_URI . "/$scope" . self::CLASS_ENDPOINT . self::RESPONSE_FORMAT . '?' . $this->getQueryString();
        }
        else{
            $query = self::BASE_URI . self::CLASS_ENDPOINT . self::RESPONSE_FORMAT . '?' . $this->getQueryString();
        }

        return new ArrayCollection($this->fetch($query));

    }

    /**
     * @param bool[] $extra
     */
    public function collaborators(array $extra = ['ACRONYM' => true, 'SECTION' => true, 'PHONE_NUMBER' => true])
    {
        $extra = new ArrayCollection($extra);

        if($extra->contains(true)) {
            $extras = "";

            if ($extra->get('SECTION')) {
                $extras .= "sections";
            }
            if ($extra->get('ACRONYM')) {
                $extras .= ",acronym";
            }
            if ($extra->get('PHONE_NUMBER')) {
                $extras .= ",phone_number";
            }

            $this->query['alter[extra]'] = $extras;

        }

    }

    /**
     * @param bool[] $extra
     */
    public function teachers(array $extra = ['SECTION' => true, 'CURRENT_CLASS_MASTERIES' => true])
    {
        $extra = new ArrayCollection($extra);

        if($extra->contains(true)) {
            $extras = "";

            if ($extra->get('SECTION')) {
                $extras .= "section";
            }
            if ($extra->get('CURRENT_CLASS_MASTERIES')) {
                $extras .= ",current_class_masteries";
            }

            $this->query['alter[extra]'] = $extras;
        }

    }

    /**
     * @param null $scope
     * @param bool[] $extra
     * @return ArrayCollection
     */
    public function students($scope = null, array $extra = ['CLASS' => true]): ArrayCollection
    {
        $extra = new ArrayCollection($extra);
        if($extra->contains(true)) {
            $extras = "";
            if ($extra->get('CLASS')) {
                $extras .= "current_class";
            }

            $this->query['alter[extra]'] = $extras;
        }

        if(!is_null($scope)){
            $query = self::BASE_URI . "/$scope" . self::STUDENT_ENDPOINT . self::RESPONSE_FORMAT . '?' . $this->getQueryString();
        }
        else{
            $query = self::BASE_URI . self::STUDENT_ENDPOINT . self::RESPONSE_FORMAT . '?' . $this->getQueryString();
        }

        return new ArrayCollection($this->fetch($query));
    }

    /**
     * @param string $friendly_id
     * @param bool[] $extra
     * @return Student
     */
    public function student(string $friendly_id, array $extra = ['CLASS' => true]): Student
    {
        $extra = new ArrayCollection($extra);
        if($extra->contains(true)) {
            $extras = "";
            if ($extra->get('CLASS')) {
                $extras .= "current_class";
            }

            $this->query['alter[extra]'] = $extras;
        }

        $query = self::BASE_URI . self::STUDENT_ENDPOINT . "/" . $friendly_id . self::RESPONSE_FORMAT . '?' . $this->getQueryString();

        $student = new Student();
        return $this->studentToEntity($student, $this->fetch($query));
    }

    public function searchStudent(string $value, $scope = null, $field = 'email')
    {
        $students = $this->students($scope);
        return $this->searchedValue($students, $field, $value);
    }

    public function studentToEntity(Student $entity, $student): Student
    {
        $entity->setFriendlyId($student->friendly_id ?? null);
        $entity->setType($student->type ?? null);
        $entity->setId($student->id ?? null);
        $entity->setFirstname($student->firstname ?? null);
        $entity->setLastname($student->lastname ?? null);
        $entity->setPhoneToken($student->phone_token ?? null);
        $entity->setPhoneTokenSentAt(new DateTime($student->phone_token_sent_at) ?? null);
        $entity->setPhoneVerifiedAt(new DateTime($student->phone_verified_at) ?? null);
        $entity->setUpdatedAt(new DateTime($student->updated_on) ?? null);
        $entity->setOccupation($student->occupation ?? null);
        $entity->setEmail($student->email ?? null);
        $entity->setExternalUuid((int)$student->external_uuid ?? null);

        if(!$entity->issetClass()){
            $entity->setClass(new Classes());
        }
        $entity->getClass()->setId($student->current_class->link->id ?? null);
        $entity->getClass()->setType($student->current_class->link->type ?? null);
        $entity->getClass()->setName($student->current_class->link->name ?? null);
        return $entity;
    }

    public function searchClass(string $value, $scope = null, $field = 'name')
    {
        $classes = $this->classes($scope);
        return $this->searchedValue($classes, $field, $value);
    }

    private function searchedValue(ArrayCollection $elements, $field, $value)
    {
        return $elements->filter(function ($el) use ($field, $value) {
            $toArray = (array)$el;
            return strtolower($value) == strtolower($toArray[$field]);
        })->first();
    }





    private function getQueryString(): string
    {
        $args = $this->query;

        $args['api_key'] = $this->api_key;

        ksort($args);

        $request_str = implode('', array_map(
            function ($v, $k) { return sprintf("%s%s", $k, $v); },
            $args,
            array_keys($args)
        ));

        $signature = md5($request_str . $this->api_secret);

        $args['signature'] = $signature;

        return implode('&', array_map(
            function ($v, $k) { return sprintf("%s=%s", $k, $v); },
            $args,
            array_keys($args)
        ));
    }

    private function fetch($uri, $method = 'GET')
    {
        $result = $this->httpClient->request($method, $uri);
        $this->requests[] = [
            'uri' => $uri,
            'method' => $method,
            'queryParams' => $this->query
        ];
        return json_decode($result->getContent());
    }



}
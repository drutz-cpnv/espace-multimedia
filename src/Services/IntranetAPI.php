<?php

namespace App\Services;

use App\Entity\Intranet\Classes;
use App\Entity\Intranet\Student;
use App\Entity\Intranet\Teacher;
use App\Entity\Room;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use JetBrains\PhpStorm\Pure;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class IntranetAPI
{

    const BASE_URI = "https://intranet.cpnv.ch";

    private array $query = [];

    const CLASS_ENDPOINT = "/classes";
    const STUDENT_ENDPOINT = "/current_students";
    const TEACHERS_ENDPOINT = "/teachers";
    const ROOMS_ENDPOINT = "/salles";

    const RESPONSE_FORMAT = ".json";

    private string $queryString;

    private array $requests = [];
    private string $api_secret;
    private string $api_key;
    private int $responseCode;


    public function __construct(string $api_secret, string $api_key, private readonly HttpClientInterface $httpClient)
    {
        $this->api_secret = $api_secret;
        $this->api_key = $api_key;

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
     * @return Teacher[]
     */
    public function teachers(array $extra = ['SECTION' => true, 'CURRENT_CLASS_MASTERIES' => true]): array
    {
        /*$extra = new ArrayCollection($extra);

        if($extra->contains(true)) {
            $extras = "";

            if ($extra->get('SECTION')) {
                $extras .= "section";
            }
            if ($extra->get('CURRENT_CLASS_MASTERIES')) {
                $extras .= ",current_class_masteries";
            }

            $this->query['alter[extra]'] = $extras;
        }*/

        $query = self::BASE_URI . self::TEACHERS_ENDPOINT . self::RESPONSE_FORMAT . '?' . $this->getQueryString();

        $teachers = [];

        foreach ($this->fetch($query) as $teacher) {
            $teachers[] = $this->teacherToEntity($teacher);
        }

        return $teachers;

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
     * @param string $user
     * @param bool[] $extra
     * @return Student|null
     */
    public function student(string $user, array $extra = ['CLASS' => true]): ?Student
    {
        $extra = new ArrayCollection($extra);
        if($extra->contains(true)) {
            $extras = "";
            if ($extra->get('CLASS')) {
                $extras .= "current_class";
            }

            $this->query['alter[extra]'] = $extras;
        }

        if(filter_var($user, FILTER_VALIDATE_EMAIL)) {
            $friendly_id = $this->studentEmailToFriendlyID($user);
        }
        else{
            $friendly_id = strtolower($user);
        }

        $query = self::BASE_URI . self::STUDENT_ENDPOINT . "/" . $friendly_id . self::RESPONSE_FORMAT . '?' . $this->getQueryString();

        $result = $this->fetch($query);

        if($this->responseCode === 404) return null;

        $student = new Student();
        return $this->studentToEntity($student, $result);
    }

    /**
     * @param string $user
     * @param bool[] $extra
     * @return \App\Entity\Teacher
     */
    public function teacher(string $user, array $extra = ['CURRENT_CLASS_MASTERIES' => true]): \App\Entity\Teacher
    {
        $extra = new ArrayCollection($extra);

        if($extra->contains(true)) {
            $extras = "";

            if ($extra->get('CURRENT_CLASS_MASTERIES')) {
                $extras .= "current_class_masteries";
            }

            $this->query['alter[extra]'] = $extras;
        }

        if(filter_var($user, FILTER_VALIDATE_EMAIL)) {
            $friendly_id = $this->teacherEmailToFriendlyID($user);
        }
        else{
            $friendly_id = strtolower($user);
        }

        $query = self::BASE_URI . self::TEACHERS_ENDPOINT . "/" . $friendly_id . self::RESPONSE_FORMAT . '?' . $this->getQueryString();

        return $this->teacherToEntity($this->fetch($query));
    }

    /**
     * @return ArrayCollection
     */
    public function rooms(): ArrayCollection
    {
        $query = self::BASE_URI . self::ROOMS_ENDPOINT . self::RESPONSE_FORMAT . '?' . $this->getQueryString();
        return new ArrayCollection($this->fetch($query));
    }

    private function studentFromEmail(string $email)
    {
        $f_id = $this->studentEmailToFriendlyID($email);
        return $this->student($f_id);
    }

    public function studentEmailToFriendlyID(string $email): string
    {
        if(str_contains($email, '.')) {
            $email = strtolower($email);
            $toReplace = ['@cpnv.ch', '.'];
            $replaceBy = ['', '_'];
            return str_replace($toReplace, $replaceBy, $email);
        }
        return false;
    }

    public function teacherEmailToFriendlyID(string $email): string
    {
        $email = strtolower($email);
        $toReplace = ['@cpnv.ch', '.'];
        $replaceBy = ['', '_'];
        return str_replace($toReplace, $replaceBy, $email);
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

    public function teacherToEntity($teacher): \App\Entity\Teacher
    {
        $entity = new \App\Entity\Teacher();
        $entity->setFriendlyId($teacher->friendly_id ?? null);
        $entity->setFirstname($teacher->firstname);
        $entity->setLastname($teacher->lastname);
        $entity->setEmail($teacher->email ?? null);
        $entity->setAcronym($teacher->acronym ?? null);

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

        $this->responseCode = $result->getStatusCode();
        try {
            return json_decode($result->getContent());
        } catch (ClientExceptionInterface $e) {
            if($e->getCode() === 404) {
                return json_decode('{"error": true}');
            }
        }
        return json_decode($result->getContent());
    }



}
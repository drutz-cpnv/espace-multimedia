<?php

namespace App\Services;

use App\Entity\Equipment;
use App\Entity\EquipmentType;
use App\Entity\Item;
use App\Entity\Settings;
use App\Entity\State;
use App\Entity\User;
use App\Entity\UserType;
use App\Repository\BrandRepository;
use App\Repository\EquipmentRepository;
use App\Repository\UserRepository;
use App\Repository\UserTypeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\MakerBundle\FileManager;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\KernelInterface;

class SetupService
{

    public function __construct(
        private UserRepository $userRepository,
        private EntityManagerInterface $entityManager,
        private ContentManager $contentManager,
        private KernelInterface $kernel,
        private UserTypeRepository $userTypeRepository,
        private BrandRepository $brandRepository,
        private EquipmentRepository $equipmentRepository,
    )
    {
    }

    public function setupAuth()
    {
        // Création des types d'utilisateurs
        $toPersist[] = (new UserType())->setName("Étudiant")->setSlug("student");
        $toPersist[] = (new UserType())->setName("Enseignant")->setSlug("teacher");

        foreach ($toPersist as $item) {
           $this->entityManager->persist($item);
        }

        $this->entityManager->flush();

    }

    public function setup() {
        $drutz = $this->userRepository->find(1);

        $drutz->setRoles([
            'ROLE_USER',
            'ROLE_WEBMASTER'
        ]);

        $toPersist = [];

        // Création des settings
        $toPersist[] = (new Settings())->setName("Nouvelle commande autorisée")->setSettingKey("authorize.order.new")->setUpdatedBy($drutz)->setUpdatedAt(new \DateTimeImmutable())->setValue("true");
        $toPersist[] = (new Settings())->setName("Nouvelle inscription")->setSettingKey("authorize.user.new")->setUpdatedBy($drutz)->setUpdatedAt(new \DateTimeImmutable())->setValue("true");
        $toPersist[] = (new Settings())->setName("Changmement de statut des commandes")->setSettingKey("authorize.order.status_change")->setUpdatedBy($drutz)->setUpdatedAt(new \DateTimeImmutable())->setValue("true");
        $toPersist[] = (new Settings())->setName("Envoi d'email")->setSettingKey("email.send")->setUpdatedBy($drutz)->setUpdatedAt(new \DateTimeImmutable())->setValue("true");

        // Création des status des commandes
        $toPersist['pending'] = (new State())->setName("En attente")->setSlug("pending")->setColor("ff7b1e")->setContentTemplate($this->contentManager->arrayToContent(new ArrayCollection([
            'Email — Commande reçue (Client)' => [
                "Introduction de l'email" => [
                    "Salutations" => "Bonjour,",
                    "Début du mail" => "Nous vous informons que votre commande à été enregistrée avec succès. Votre demande devrait être traitée d'ici à mardi prochain."
                ],
                "Fin de l'email" => [
                    "Avertissement" => "Dans le cas ou votre commande n'est plus d'actualité, veuillez vous rendre, dans les plus brefs délais, sur la page de gestion des commandes sur l'application web.",
                    "Salutations" => "En espérant que l'équipement que vous avez commander vous aidera dans la réalisation de votre projet, nous vous adressons nos meilleures salutations."
                ]
            ],
            'email.order.client.new'
        ]))->setCreatedBy($drutz)->setUpdatedBy($drutz));
        $toPersist['accepted'] = (new State())->setName("Acceptée")->setSlug("accepted")->setColor("38ce35")->setContentTemplate($this->contentManager->arrayToContent(new ArrayCollection([
            'Email — Commande acceptée (client)' => [
                "Introduction de l'email" => [
                    "Salutations" => "Bonjour,",
                    "Début du mail" => "Nous avons le plaisir de vous annoncez que votre commande a été vérifiée et acceptée par notre équipe.",
                    "Paragraphe 2" => "Vous recevrez bientôt un email pour vous prévenir que l'équipement demandé a été déposé à l'emplacement de récupération.",
                ],
                "Fin de l'email" => [
                    "Avertissement" => "Dans le cas ou votre commande n'est plus d'actualité, veuillez vous rendre, dans les plus brefs délais, sur la page de gestion des commandes sur l'application web.",
                    "Salutations" => "En espérant que l'équipement que vous avez commander vous aidera dans la réalisation de votre projet, nous vous adressons nos meilleures salutations.",
                ]
            ],
            'email.order.client.accepted'
        ]))->setCreatedBy($drutz)->setUpdatedBy($drutz));
        $toPersist['refused'] = (new State())->setName("Refusée")->setSlug("refused")->setColor("e01313")->setContentTemplate($this->contentManager->arrayToContent(new ArrayCollection([
            'Email — Commande refusée (client)' => [
                "Introduction de l'email" => [
                    "Salutations" => "Bonjour,",
                    "Début du mail" => "Nous sommes au regret de vous informer que nous n'avons pas pu donner suite à votre commande. La raison de ce refus est indiquée ci-dessous.",
                ],
                "Fin de l'email" => [
                    "Informations suplémentaires" => "Dans le cas ou vous pensez que cela est une erreur, vous pouvez contacter l'Espace Multimédia via le formulaire de contact de l'interface web.",
                    "Salutations" => "Meilleures salutations.",
                ]
            ],
            'email.order.client.refused'
        ]))->setCreatedBy($drutz)->setUpdatedBy($drutz));
        $toPersist['late'] = (new State())->setName("En retard")->setSlug("late")->setColor("ce7535")->setContentTemplate($this->contentManager->arrayToContent(new ArrayCollection([
            'Email — Matériel non rendu (client)' => [
                "Introduction de l'email" => [
                    "Salutations" => "Bonjour,",
                    "Début du mail" => "Le matériel que nous avons mis à votre disposition, n'a pas été rendu. Attention, ce message est automatique, par conséquent il est possible que vous ayez déjà ramener le matériel. Dans ce cas, vous pouvez ignorer ce message.",
                    "Cas contraire" => "Dans le cas contraire, veuillez le déposer à l'endroit prévu dans les plus brefs délais.",
                ],
                "Fin de l'email" => [
                    "Informations suplémentaires" => "Dans le cas ou vous pensez que cela est une erreur, vous pouvez contacter l'Espace Multimédia via le formulaire de contact de l'interface web.",
                    "Salutations" => "Meilleures salutations.",
                ]
            ],
            'email.order.client.late'
        ]))->setCreatedBy($drutz)->setUpdatedBy($drutz));
        $toPersist['error'] = (new State())->setName("Erreur")->setSlug("error")->setColor("ff0000")->setContentTemplate($this->contentManager->arrayToContent(new ArrayCollection([
            'Email — Une erreur avec la commande (client)' => [
                "Introduction de l'email" => [
                    "Salutations" => "Bonjour,",
                    "Début du mail" => "Votre commande présente un problème, plus d'information ci-dessous.",
                ],
                "Fin de l'email" => [
                    "Informations suplémentaires" => "Dans le cas ou vous pensez que cela est une erreur, vous pouvez contacter l'Espace Multimédia via le formulaire de contact de l'interface web.",
                    "Salutations" => "Meilleures salutations.",
                ]
            ],
            'email.order.client.error'
        ]))->setCreatedBy($drutz)->setUpdatedBy($drutz));
        $toPersist['termianted'] = (new State())->setName("Terminée")->setSlug("termianted")->setColor("1c6fe1")->setContentTemplate($this->contentManager->arrayToContent(new ArrayCollection([
            'Email — Commande refusée (client)' => [
                "Introduction de l'email" => [
                    "Salutations" => "Bonjour,",
                    "Début du mail" => "Votre commande est désormais finie, nous avons été ravis de pouvoir vous offrir nos services.",
                    "Demande de sondage" => "Afin de pouvoir continuellement nous améliorer pour vous offrir le meilleure service possible, nous serions reconnaissant si vous pouviez remplir le petit sondage en cliquant sur le lien ci-dessous.",
                ],
                "Fin de l'email" => [
                    "Informations suplémentaires" => "Dans le cas ou vous pensez que cela est une erreur, vous pouvez contacter l'Espace Multimédia via le formulaire de contact de l'interface web.",
                    "Salutations" => "En espérant pouvoir à nouveau être le compagnon de vos futures projets, nous vous adressons, nos meilleures salutations.",
                ]
            ],
            'email.order.client.terminated'
        ]))->setCreatedBy($drutz)->setUpdatedBy($drutz));
        $toPersist['cancelled'] = (new State())->setName("Annulée")->setSlug("cancelled")->setColor("353b48")->setContentTemplate($this->contentManager->arrayToContent(new ArrayCollection([
            'Email — Commande annulée (client)' => [
                "Introduction de l'email" => [
                    "Salutations" => "Bonjour,",
                    "Début du mail" => "Nous sommes au regret de vous informer que nous n'avons pas pu donner suite à votre commande. La raison de ce refus est indiquée ci-dessous.",
                ],
                "Fin de l'email" => [
                    "Informations suplémentaires" => "Dans le cas ou vous pensez que cela est une erreur, vous pouvez contacter l'Espace Multimédia via le formulaire de contact de l'interface web.",
                    "Salutations" => "Meilleures salutations.",
                ]
            ],
            'email.order.client.cancelled'
        ]))->setCreatedBy($drutz)->setUpdatedBy($drutz));
        $toPersist['in_progress'] = (new State())->setName("Matériel déliveré")->setSlug("in_progress")->setColor("8e44ad")->setContentTemplate($this->contentManager->arrayToContent(new ArrayCollection([
            'Email — Matériel mis à disposition (client)' => [
                "Introduction de l'email" => [
                    "Salutations" => "Bonjour,",
                    "Début du mail" => "Votre matériel vous attends à l'emplacement prévu. Une fois récupérer, merci de signé le document placé dans le bac prévu à cet effet.",
                ],
                "Fin de l'email" => [
                    "Informations suplémentaires" => "Dans le cas ou du matériel venait à manquer, vous pouvez contacter l'Espace Multimédia via le formulaire de contact de l'interface web.",
                    "Salutations" => "En espérant que le matériel vous sera d'une grande utilité, nous vous adressons, nos meilleures salutations.",
                ]
            ],
            'email.order.client.in_progress'
        ]))->setCreatedBy($drutz)->setUpdatedBy($drutz));

        $toPersist[] = $this->contentManager->arrayToContent(new ArrayCollection([
            'Email — Nouvelle commande (staff)' => [
                "Introduction de l'email" => [
                    "Salutations" => "Bonjour,",
                    "Premier paragraphe" => "Nous avons reçu une nouvelle commande. Vous pouvez dès à présent accéder à l'espace d'administration afin d'apporter une réponse au client le plus rapidement possible.",
                    "Second paragraphe" => "En vous demandant de bien vouloir vous assurer que toutes les conditions sont remplies avant d'accepter la demande.",
                ],
                "Fin de l'email" => [
                    "Salutations" => "Meilleures salutations."
                ]
            ],
            'email.order.staff.new'
        ]))->setCreatedBy($drutz)->setUpdatedBy($drutz);

        $toPersist[] = $this->contentManager->arrayToContent(new ArrayCollection([
            'Email — Commande annulée (staff)' => [
                "Introduction de l'email" => [
                    "Salutations" => "Bonjour,",
                    "Premier paragraphe" => "Un utilisateur, a annulé sa commande. La raison est indiquée ci-dessous.",
                ],
                "Fin de l'email" => [
                    "Salutations" => "Meilleures salutations."
                ]
            ],
            'email.order.staff.cancelled'
        ]))->setCreatedBy($drutz)->setUpdatedBy($drutz);


        $toPersist[] = $this->contentManager->arrayToContent(new ArrayCollection([
            'Contenu de la page "Informations"' => [
                "TODO: Modifier la première section" => [
                    "Paragrpahe 1" => "TODO: Remplir le premier paragraphe de la première section",
                    "Paragraphe 2" => "TODO: Remplir le second paragraphe de la première section",
                ],
                "TODO: Modifier la seconde section" => [
                    "Paragrpahe 1" => "TODO: Remplir le premier paragraphe de la seconde section",
                    "Paragraphe 2" => "TODO: Remplir le second paragraphe de la seconde section",
                ],
            ],
            'page.support'
        ]))->setCreatedBy($drutz)->setUpdatedBy($drutz);

        foreach ($toPersist as $item) {
            $this->entityManager->persist($item);
        }

        $this->entityManager->flush();

    }

    public function importUsers() {
        $fileContent = file_get_contents($this->kernel->getProjectDir().'/user.json');

        if(!$fileContent){
            return false;
        }

        $users = json_decode($fileContent);

        $studentType = $this->userTypeRepository->findOneBySlug('student');

        $dbUsers = [];

        foreach ($users as $user) {
            $dbUsers[] = (new User())
                ->setRoles(json_decode($user->roles))
                ->setPassword($user->password)
                ->setFamilyName($user->family_name)
                ->setGivenName($user->given_name)
                ->setStatus(1)
                ->setIsVerified((bool)$user->is_verified)
                ->setEmail($user->email)
                ->setCreatedAt(\DateTimeImmutable::createFromFormat("Y-m-d H:i:s", $user->created_at))
                ->setUpdatedAt(\DateTimeImmutable::createFromFormat("Y-m-d H:i:s", $user->updated_at))
                ->setType($studentType)
            ;

        }

        foreach ($dbUsers as $dbUser) {
            $this->entityManager->persist($dbUser);
        }

        $this->entityManager->flush();

        return $dbUsers;
    }

    public function setupRoomEquipment()
    {
        $brand = $this->brandRepository->findOneBy(['name' => 'Inconnue']);
        $drutz = $this->userRepository->findOneBy(['email' => 'dimitri.rutz@cpnv.ch']);

        $pastRooms = $this->equipmentRepository->findRoom();

        $rooms = [];

        $rooms[] = (new Equipment())
            ->setName("Cabine son")
            ->setDescription("Petite salle insonorisée permettant l'enregistrement de contenu audio sans bruits parasites.")
            ->setIsRoom(true)
            ->setEnabled(true)
            ->setBrand($brand)
            ->setType((new EquipmentType())->setName("Cabine son")->setSlug("cabine-son"))
            ->setCreatedBy($drutz)
            ->setUpdatedBy($drutz)
            ->addItem((new Item())->setState(1)->setCreatedBy($drutz)->setUpdatedBy($drutz)->setTag("SC01"))
        ;

        $rooms[] = (new Equipment())
            ->setName("Fond vert")
            ->setDescription("Espace avec pour fond un drap vert permettant l'enregistrement d'une vidéo pouvant ensuite être détourée et incrustée avec arrière plan personnalisé.")
            ->setIsRoom(true)
            ->setEnabled(true)
            ->setBrand($brand)
            ->setType((new EquipmentType())->setName("Fond vert")->setSlug("fond-vert"))
            ->setCreatedBy($drutz)
            ->setUpdatedBy($drutz)
            ->addItem((new Item())->setState(1)->setCreatedBy($drutz)->setUpdatedBy($drutz)->setTag("GS01"))
        ;

        $rooms[] = (new Equipment())
            ->setName("Imprimante 3D")
            ->setDescription("Imprimante permettant l'impression de modèle 3D avec du plastique.")
            ->setIsRoom(true)
            ->setEnabled(true)
            ->setBrand($brand)
            ->setType((new EquipmentType())->setName("Imprimante 3D")->setSlug("imprimante-3d"))
            ->setCreatedBy($drutz)
            ->setUpdatedBy($drutz)
            ->addItem((new Item())->setState(1)->setCreatedBy($drutz)->setUpdatedBy($drutz)->setTag("3D01"))
            ->addItem((new Item())->setState(1)->setCreatedBy($drutz)->setUpdatedBy($drutz)->setTag("3D02"))
        ;

        foreach ($pastRooms as $room) {
            $this->entityManager->remove($room);
        }

        $this->entityManager->flush();

        foreach ($rooms as $room) {
            $this->entityManager->persist($room);
        }

        $this->entityManager->flush();
    }

}
<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 */
class User implements UserInterface, PasswordAuthenticatedUserInterface
{

    public const ROLES = [
        'ROLE_USER' => "Utilisateur",
        'ROLE_TEACHER' => "Enseignant",
        'ROLE_EDITOR' => "Editeur",
        'ROLE_ADMIN' => "Administrateur",
        'ROLE_WEBMASTER' => "Webmaster"
    ];

    public const STATUS = [
        "En attente de confirmation",
        "Activé",
        "Suspendu",
        "Désactivé"
    ];

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = ['ROLE_USER'];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $family_name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $given_name;

    /**
     * @ORM\Column(type="integer")
     */
    private $status = 1;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isVerified = false;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private $updatedAt;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    #[
        Assert\Regex("/\bcpnv.ch\b$/"),
        Assert\Email()
    ]
    private $email;

    /**
     * @ORM\ManyToOne(targetEntity=UserType::class, inversedBy="users")
     * @ORM\JoinColumn(nullable=false)
     */
    private $type;

    /**
     * @ORM\OneToMany(targetEntity=Order::class, mappedBy="client")
     */
    private $orders;

    /**
     * @ORM\OneToOne(targetEntity=Teacher::class, mappedBy="userTeacher", cascade={"persist", "remove"})
     */
    private $teacher;

    /**
     * @ORM\OneToMany(targetEntity=Cart::class, mappedBy="user", orphanRemoval=true)
     */
    private $carts;

    /**
     * @ORM\ManyToOne(targetEntity=User::class)
     */
    private $updatedBy;

    /**
     * @ORM\OneToMany(targetEntity=Message::class, mappedBy="createdBy")
     */
    private $messages;

    public function __construct()
    {
        $this->setCreatedAt(new \DateTimeImmutable());
        $this->setUpdatedAt(new \DateTimeImmutable());
        $this->orders = new ArrayCollection();
        $this->carts = new ArrayCollection();
        $this->messages = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @deprecated since Symfony 5.3, use getUserIdentifier instead
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getFamilyName(): ?string
    {
        return $this->family_name;
    }

    public function setFamilyName(string $family_name): self
    {
        $this->family_name = $family_name;

        return $this;
    }

    public function getGivenName(): ?string
    {
        return $this->given_name;
    }

    public function setGivenName(string $given_name): self
    {
        $this->given_name = $given_name;

        return $this;
    }

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function setStatus(int $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): self
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getType(): ?UserType
    {
        return $this->type;
    }

    public function setType(?UserType $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getStatusName()
    {
        return self::STATUS[$this->getStatus()];
    }

    public function getRoleString()
    {
        $string = "";
        $roles = new ArrayCollection($this->getRoles());
        $last = $roles->count() - 1;
        /** @var string[] $roles */
        foreach ($roles as $key => $role){
            if($key === $last){
                $string .= $role;
            }
            elseif($key === $last-1) {
                $string .= $role . " et ";
            }
            else{
                $string .= $role . ", ";
            }
        }
        return $string;
    }

    /**
     * @return Collection|Order[]
     */
    public function getOrders(): Collection
    {
        return $this->orders;
    }

    public function addOrder(Order $order): self
    {
        if (!$this->orders->contains($order)) {
            $this->orders[] = $order;
            $order->setClient($this);
        }

        return $this;
    }

    public function removeOrder(Order $order): self
    {
        if ($this->orders->removeElement($order)) {
            // set the owning side to null (unless already changed)
            if ($order->getClient() === $this) {
                $order->setClient(null);
            }
        }

        return $this;
    }

    public function getFullname()
    {
        return $this->getGivenName() . " " . $this->getFamilyName();
    }

    public function __toString(): string
    {
        return $this->getFullname();
    }

    public function getRoleName()
    {
        $roles = new ArrayCollection($this->getRoles());
        return self::ROLES[$roles->last()];
    }

    public function getTeacher(): ?Teacher
    {
        return $this->teacher;
    }

    public function setTeacher(?Teacher $teacher): self
    {
        // unset the owning side of the relation if necessary
        if ($teacher === null && $this->teacher !== null) {
            $this->teacher->setUserTeacher(null);
        }

        // set the owning side of the relation if necessary
        if ($teacher !== null && $teacher->getUserTeacher() !== $this) {
            $teacher->setUserTeacher($this);
        }

        $this->teacher = $teacher;

        return $this;
    }

    public function isAdmin()
    {
        if (in_array("ROLE_WEBMASTER", $this->getRoles())) return true;
        elseif (in_array("ROLE_ADMIN", $this->getRoles())) return true;
        elseif (in_array("ROLE_EDITOR", $this->getRoles())) return true;
        else return false;
    }

    /**
     * @return Collection|Cart[]
     */
    public function getCarts(): Collection
    {
        return $this->carts;
    }

    public function addCart(Cart $cart): self
    {
        if (!$this->carts->contains($cart)) {
            $this->carts[] = $cart;
            $cart->setUser($this);
        }

        return $this;
    }

    public function getUpdatedBy(): ?self
    {
        return $this->updatedBy;
    }

    public function setUpdatedBy($updatedBy): self
    {
        $this->updatedBy = $updatedBy;

        return $this;
    }

    public function getEquipmentCart(Equipment $equipment): Cart|null
    {
        foreach ($this->getCarts() as $cart) {
            if($cart->getEquipment()->getId() === $equipment->getId()) return $cart;
        }
        return null;
    }

    public function isInCart(Equipment $equipment): bool
    {
        return !is_null($this->getEquipmentCart($equipment));
    }

    public function isCartEmpty(): bool
    {
        return $this->getCarts()->isEmpty();
    }

    public function getCartEquipment()
    {
        $output = new ArrayCollection();

        foreach ($this->getCarts() as $cart) {
            $output->add($cart->getEquipment());
        }

        return $output;
    }

    /**
     * @return Collection|Message[]
     */
    public function getMessages(): Collection
    {
        return $this->messages;
    }

    public function addMessage(Message $message): self
    {
        if (!$this->messages->contains($message)) {
            $this->messages[] = $message;
            $message->setCreatedBy($this);
        }

        return $this;
    }

    public function removeMessage(Message $message): self
    {
        if ($this->messages->removeElement($message)) {
            // set the owning side to null (unless already changed)
            if ($message->getCreatedBy() === $this) {
                $message->setCreatedBy(null);
            }
        }

        return $this;
    }
}

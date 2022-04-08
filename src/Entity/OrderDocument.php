<?php

namespace App\Entity;

use App\Repository\OrderDocumentRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\String\Slugger\AsciiSlugger;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity(repositoryClass=OrderDocumentRepository::class)
 * @Vich\Uploadable()
 */
class OrderDocument
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var string|null
     * @ORM\Column(type="string", length=255)
     */
    private $filename;

    /**
     * @var File|null
     * @Assert\File(
     *     mimeTypes="application/pdf"
     * )
     * @Vich\UploadableField(mapping="order_document", fileNameProperty="filename")
     */
    private $pdfFile;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private $createdAt;

    /**
     * @ORM\ManyToOne(targetEntity=User::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $createdBy;

    /**
     * @ORM\ManyToOne(targetEntity=Order::class, inversedBy="documents")
     * @ORM\JoinColumn(nullable=false)
     */
    private $document_order;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $slug;

    public function __construct()
    {
        $this->setCreatedAt(new \DateTimeImmutable());
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getCreatedBy(): ?User
    {
        return $this->createdBy;
    }

    public function setCreatedBy(User|UserInterface $createdBy): self
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    public function getDocumentOrder(): ?Order
    {
        return $this->document_order;
    }

    public function setDocumentOrder(?Order $documet_order): self
    {
        $this->document_order = $documet_order;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getFilename(): ?string
    {
        return $this->filename;
    }

    /**
     * @param string|null $filename
     * @return OrderDocument
     */
    public function setFilename(?string $filename): OrderDocument
    {
        $this->filename = $filename;
        return $this;
    }

    /**
     * @return File|null
     */
    public function getPdfFile(): ?File
    {
        return $this->pdfFile;
    }

    /**
     * @param File|null $pdfFile
     * @return OrderDocument
     */
    public function setPdfFile(?File $pdfFile): OrderDocument
    {
        $this->pdfFile = $pdfFile;
        return $this;
    }

    public function __toString(): string
    {
        return $this->getTitle();
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getInitialZeroId(): string
    {
        $base = "000000";
        $base = substr($base, -0, -strlen((string)$this->getId()));
        $base .= $this->getId();
        return $base;
    }

    public function setDocumetOrder(Order $param)
    {
    }


}

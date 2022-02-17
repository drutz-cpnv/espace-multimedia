<?php

namespace App\Controller\Admin;

use App\Entity\Content;
use App\Entity\Paragraph;
use App\Entity\Section;
use App\Form\AdminType\AdminAddParagraphType;
use App\Form\AdminType\AdminAddSectionType;
use App\Form\AdminType\AdminContentType;
use App\Form\AdminType\AdminParagraphType;
use App\Repository\ContentRepository;
use App\Services\ContentManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/admin/content")]
class AdminContentController extends AbstractController
{

    #[Route("", name: "admin.content.index")]
    public function index(ContentRepository $contentRepository, EntityManagerInterface $entityManager): Response
    {
        return $this->render("admin/content/index.html.twig", [
            'menu' => 'admin.content',
            'contents' => $contentRepository->findAll()
        ]);
    }


    #[Route('/paragraph/{id}', name: 'admin.content.sections.paragraph')]
    public function showParagraph(Paragraph $paragraph, Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(AdminParagraphType::class, $paragraph);
        $form->handleRequest($request);

        $contentParent = $paragraph->getSection()->getContent();
        $sectionParent = $paragraph->getSection();

        if($form->isSubmitted() && $form->isValid()){
            $contentParent->setUpdatedAt(new \DateTimeImmutable());
            $contentParent->setUpdatedBy($this->getUser());

            $entityManager->flush();

            return $this->redirectToRoute('admin.content.sections.paragraph', ['id' => $paragraph->getId()]);
        }

        $formSection = $this->createForm(AdminAddSectionType::class, $sectionParent);
        $formSection->handleRequest($request);

        if($formSection->isSubmitted() && $formSection->isValid()){
            $contentParent->setUpdatedAt(new \DateTimeImmutable());
            $contentParent->setUpdatedBy($this->getUser());

            $entityManager->flush();

            return $this->redirectToRoute('admin.content.sections.paragraph', ['id' => $paragraph->getId()]);
        }

        return $this->renderForm('admin/content/show_content.html.twig', [
            'menu' => 'admin.content',
            'content' => $contentParent,
            'paragraph' => $paragraph,
            'form' => $form,
            'form_section' => $formSection
        ]);
    }

    #[Route('/section/{id}/new', name: 'admin.content.sections.new_paragaph')]
    public function newParagraph(Section $section, Request $request, EntityManagerInterface $entityManager): Response
    {
        $paragraph = new Paragraph();
        $form = $this->createForm(AdminAddParagraphType::class, $paragraph, [
            'attr' => [
                'action' => $this->generateUrl('admin.content.sections.new_paragaph', ['id' => $section->getId()])
            ]
        ]);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $paragraph->setSection($section);

            $entityManager->persist($paragraph);
            $entityManager->flush();

            return $this->redirectToRoute('admin.content.sections.paragraph', ['id' => $paragraph->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/content/new_paragraph.twig', [
            'form' => $form
        ]);
    }

    #[Route('/{id}/new', name: 'admin.content.new_section')]
    public function newSection(Content $content, Request $request, EntityManagerInterface $entityManager): Response
    {
        $section = new Section();
        $form = $this->createForm(AdminAddSectionType::class, $section, [
            'attr' => [
                'action' => $this->generateUrl('admin.content.new_section', ['id' => $content->getId()])
            ]
        ]);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $paragraph = new Paragraph();
            $paragraph->setName("Premier paragraphe");
            $section->setContent($content);
            $section->addParagraph($paragraph);

            $entityManager->persist($section);
            $entityManager->flush();

            return $this->redirectToRoute('admin.content.sections.paragraph', ['id' => $paragraph->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/content/new_paragraph.twig', [
            'form' => $form
        ]);
    }

    #[Route('/paragraph/{id}/delete', name: 'admin.content.paragraph.delete', methods: ['POST'])]
    public function delete(Request $request, Paragraph $paragraph, EntityManagerInterface $entityManager, ): Response
    {
        if ($this->isCsrfTokenValid('delete'.$paragraph->getId(), $request->request->get('_token'))) {
            $entityManager->remove($paragraph);
            $entityManager->flush();
        }

        $parentSection = $paragraph->getSection();

        return $this->redirectToRoute('admin.content.sections.paragraph', ['id' => $parentSection->getParagraphs()->first()->getId()], Response::HTTP_SEE_OTHER);
    }

    #[Route('/new', name: 'admin.content.new')]
    public function new(Request $request, EntityManagerInterface $entityManager, ContentManager $contentManager): Response
    {
        $content = new Content();
        $form = $this->createForm(AdminContentType::class, $content, [
            'attr' => [
                'action' => $this->generateUrl('admin.content.new')
            ]
        ]);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $content->addSection($contentManager->createEmptySection());
            $content->setUpdatedBy($this->getUser());
            $content->setCreatedBy($this->getUser());
            $entityManager->persist($content);
            $entityManager->flush();

            return $this->redirectToRoute('admin.content.sections.paragraph', ['id' => $content->getFirstParagraph()->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/content/new_content.html.twig', [
            'form' => $form
        ]);
    }



}
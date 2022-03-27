<?php

namespace App\Services;

use App\Entity\Content;
use App\Entity\Paragraph;
use App\Entity\Section;
use Doctrine\Common\Collections\ArrayCollection;

class ContentManager
{

    /**
     * Create a section with 1 empty paragraph (need to be attached to a Content entity)
     * @return Section
     */
    public function createEmptySection(): Section
    {
        $section = (new Section())
            ->setName("Section à remplir !");

        $section->addParagraph($this->createEmptyParagraph());
        return $section;
    }

    /**
     * Create an empty paragraph (need to be attached to a Section entity)
     * @return Paragraph
     */
    public function createEmptyParagraph(): Paragraph
    {
        return (new Paragraph())
            ->setName("Paragraphe à remplir !")
            ->setText("Vous pouvez ajouter du contenu à ce paragraphe ici !");
    }

    public function arrayToContent(ArrayCollection $arrayContent): Content {
        $content = new Content();
        $content->setKey($arrayContent->last());

        foreach ($arrayContent as $contentTitle => $sections) {
            if($contentTitle === 0) continue;
            $content->setName($contentTitle);

            $sPos = 1;
            foreach ($sections as $sectionName => $paragraphs) {
                $section = (new Section())->setName($sectionName)->setPosition($sPos);
                $content->addSection($section);

                $pPos = 1;
                foreach ($paragraphs as $paragraphName => $p) {
                    $section->addParagraph((new Paragraph())->setName($paragraphName)->setText($p)->setPosition($pPos));
                    $pPos++;
                }
                $sPos++;
            }

        }

        return $content;

    }

}
<?php

namespace App\Services;

use App\Entity\Paragraph;
use App\Entity\Section;

class ContentManager
{

    /**
     * Will create a section with 1 empty paragraph (need to be attached to a Content entity)
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
     * Will create an empty paragraph (need to be attached to a Section entity)
     * @return Paragraph
     */
    public function createEmptyParagraph(): Paragraph
    {
        return (new Paragraph())
            ->setName("Paragraphe à remplir !")
            ->setText("Vous pouvez ajouter du contenu à ce paragraphe ici !");
    }

}
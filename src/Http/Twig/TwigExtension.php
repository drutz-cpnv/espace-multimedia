<?php

namespace App\Http\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class TwigExtension extends AbstractExtension
{


    public function getFunctions(): array
    {
        return [
            new TwigFunction('icon', [$this, 'icon'], ['is_safe' => ['html']]),
            new TwigFunction('menu_active', [$this, 'menuActive'], ['is_safe' => ['html'], 'needs_context' => true]),
        ];
    }


    public function icon(string $name, bool $rounded = true): string
    {
        $class = 'icon material-icons';

        if ($rounded) {
            $class .= "-round";
        }

        return <<<HTML
<span class="$class">
$name
</span>
HTML;

    }

    public function menuActive(array $context, string $name): string
    {
        if (($context['menu'] ?? null) === $name) {
            return ' aria-current="page"';
        }
        return '';
    }

}
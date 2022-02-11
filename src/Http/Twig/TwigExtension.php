<?php

namespace App\Http\Twig;

use App\Entity\Order;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class TwigExtension extends AbstractExtension
{


    public function getFunctions(): array
    {
        return [
            new TwigFunction('icon', [$this, 'icon'], ['is_safe' => ['html']]),
            new TwigFunction('menu_active', [$this, 'menuActive'], ['is_safe' => ['html'], 'needs_context' => true]),
            new TwigFunction('dot', [$this, 'getStateDot'], ['is_safe' => ['html']]),
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

    public function getStateDot(Order $order): string
    {
        return <<<HTML
<div class="dots">
    <div class="dot" style="--dot-color: #{$order->getOrderStates()->last()->getState()->getColor()}" title="{$order->getOrderStates()->last()->getState()->getName()}"></div>
</div>
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
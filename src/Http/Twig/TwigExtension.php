<?php

namespace App\Http\Twig;

use App\Entity\Order;
use App\Entity\OrderState;
use App\Entity\State;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use Twig\TwigTest;

class TwigExtension extends AbstractExtension
{


    public function getFunctions(): array
    {
        return [
            new TwigFunction('icon', [$this, 'icon'], ['is_safe' => ['html']]),
            new TwigFunction('feather', [$this, 'feather'], ['is_safe' => ['html']]),
            new TwigFunction('menu_active', [$this, 'menuActive'], ['is_safe' => ['html'], 'needs_context' => true]),
            new TwigFunction('dot', [$this, 'getStateDot'], ['is_safe' => ['html']]),
            new TwigFunction('status_choices', [$this, 'statusChoices'], ['is_safe' => ['bool']]),
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

    public function feather(string $name): string
    {
        return <<<HTML
<i data-feather="$name"></i>
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


    public function statusChoices(OrderState $orderState, State $state): bool
    {

        $from = [
            'accepted' => [
                'refused',
                'cancelled',
                'in_progress',
                'pending',
            ],
            'pending' => [
                'refused',
                'accepted',
                'cancelled',
            ],
            'refused' => [
                'cancelled',
                'pending',
            ],
            'error' => [
                'cancelled',
                'late',
                'in_progress',
                'terminated',
            ],
            'late' => [
                'cancelled',
                'terminated',
            ],
            'terminated' => [],
            'in_progress' => [
                'cancelled',
                'error',
                'terminated',
            ],
            'cancelled' => [
                'pending'
            ]
        ];

        return in_array($state->getSlug(), $from[$orderState->getState()->getSlug()]);
    }

}
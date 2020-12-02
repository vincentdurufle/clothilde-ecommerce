<?php

namespace App\Twig;

use App\Entity\Item;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class LanguageExtension extends AbstractExtension
{
    public function getFilters()
    {
        return [
            new TwigFilter('localeField', [$this, 'getLocaleField']),
        ];
    }

    public function getLocaleField(Item $item, string $field, string $locale)
    {
        $fieldWithLocale = sprintf('get%s%s', $field, ucfirst($locale));

        return $item->{$fieldWithLocale}();
    }
}
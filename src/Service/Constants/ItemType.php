<?php

namespace App\Service\Constants;

use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * This class purpose is to list all available item categories
 */
final class ItemType
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public const MURAL_DECORATION = 'mural_decoration';
    public const CARPET = 'carpet';
    public const PILLOW = 'pillow';
    public const OTHER = 'other';

    public const VALUES = [
        self::MURAL_DECORATION,
        self::CARPET,
        self::PILLOW,
        self::OTHER
    ];

    public function getValuesTranslated(): array
    {
        $translated = [];
        foreach (self::VALUES as $value) {
            $translated[$this->translator->trans(sprintf('item.type.%s', $value), [], 'app')] = $value;
        }

        return $translated;
    }
}
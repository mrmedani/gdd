<?php

namespace App\Shared\Support;

/**
 * Palette de couleurs des catégories — SOURCE UNIQUE (déjà dupliquée
 * entre Dashboard et StatisticsIndex, elle doit toujours rendre la même
 * couleur pour la même catégorie aux deux pages).
 */
class CategoryPalette
{
    private const COLORS = [
        '#EF4444', '#3B82F6', '#10B981', '#F59E0B', '#8B5CF6',
        '#EC4899', '#06B6D4', '#F97316', '#14B8A6', '#6366F1',
        '#84CC16', '#D946EF', '#0EA5E9', '#EAB308', '#22C55E',
    ];

    public static function color(int $id): string
    {
        return self::COLORS[($id - 1) % count(self::COLORS)];
    }
}

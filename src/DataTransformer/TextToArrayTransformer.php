<?php


namespace App\DataTransformer;


use Symfony\Component\Form\DataTransformerInterface;

class TextToArrayTransformer implements DataTransformerInterface
{

    public function transform($tags): ?string
    {
        if (null === $tags) {
            return '';
        }

        return implode(',', $tags);
    }

    public function reverseTransform($tagsString): array
    {
        if (!$tagsString) {
            return [];
        }
        $exploded = explode(',', $tagsString);
        $tagsArray = array_map('trim', $exploded);

        return $tagsArray;
    }
}
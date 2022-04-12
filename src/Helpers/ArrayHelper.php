<?php

namespace App\Helpers;

use Symfony\Component\PropertyAccess\PropertyAccess;

class ArrayHelper
{
    static function getFromMultidimensional(string $key, array $data = [], int $chunk = 0): array
    {
        $output = array_map(function ($item) use ($key) {
            return $item[$key] ?? null;
        }, $data);

        if ($chunk > 0) {
            return array_chunk($output, $chunk);
        }

        return $output;
    }

    static function arrayWithKeyFromMultidimensional(array $list, string $key_name): array
    {
        $output = [];
        foreach ($list as $item) {
            $output[$item[$key_name]] = $item;
        }

        return $output;
    }

    static function arrayWithKeyFromObjectCollection(iterable $list, callable $key_value, string $key_multidimensional = ''): array
    {
        $output = [];
        foreach ($list as $item) {
            $key = $key_value($item);
            if (strlen($key_multidimensional) > 0) {
                $output[$key][$key_multidimensional] = $item;
                continue;
            }

            $output[$key] = $item;
        }

        return $output;
    }

    public static function multidimensionalSetKeyViaAccessPath(array $list, string $access_path): array
    {
        $propertyAccessor = PropertyAccess::createPropertyAccessor();

        $output = [];

        foreach ($list as $item) {
            $key = $propertyAccessor->getValue($item, $access_path);

            $output[$key] = $item;
        }

        return $output;
    }

    public static function multidimensionalMerge(array $first, array $second): array
    {
        $output = [];
        foreach ($first as $k => $v) {
            if (!isset($second[$k])) {
                continue;
            }

            $output[$k] = array_merge($v, $second[$k]);
        }

        foreach ($second as $k => $v) {
            $output[$k] = array_merge($output[$k] ?? [], $first[$k] ?? []);
        }

        foreach ($output as $k => $v) {
            if (empty($v)) {
                unset($output[$k]);
            }
        }

        return $output;
    }

    public static function multidimensionalSum(array $list, string $key): int|float
    {
        $sum = 0;
        foreach ($list as $item) {
            $sum += $item[$key];
        }

        return $sum;
    }
}
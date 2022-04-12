<?php

namespace App\Collection;

use JetBrains\PhpStorm\Pure;

class AvgCollection extends EntityCollection
{
    public function add(int|float $item)
    {
        $this->offsetSet('', $item);
    }

    #[Pure]
    public function calculate(): float|int
    {
        return ($this->count() > 0 ? $this->sum() / $this->count() : 0);
    }

    public function sum()
    {
        $sum = 0;
        foreach ($this as $item) {
            $sum += $item;
        }

        return $sum;
    }
}
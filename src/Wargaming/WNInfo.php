<?php

namespace App\Wargaming;

class WNInfo
{
    public function __construct(
        private float|int $wn,
        private float|int $weight
    )
    {
    }

    public function getWn(): float|int
    {
        return $this->wn;
    }

    public function setWN(float|int $wn): void
    {
        $this->wn = $wn;
    }

    public function getWeight(): float|int
    {
        return $this->weight;
    }

    public function setWeight(float|int $weight): void
    {
        $this->weight = $weight;
    }

    public function isValid(): bool
    {
        return $this->wn > 0;
    }

    public function getWnSum(): float|int
    {
        return $this->wn * $this->weight;
    }
}
<?php


namespace App\Interface;

use App\Collection\EntityCollection;

interface SaveRepositoryInterface
{
    public function saveMultiple(EntityCollection $tanks): void;
}
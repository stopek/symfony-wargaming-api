<?php

namespace App\Trait\Repository;

use App\Collection\EntityCollection;

trait StoreTrait
{
    public function saveMultiple(EntityCollection $items): void
    {
        foreach ($items as $item) {
            $this->_em->persist($item);
        }

        $this->_em->flush();
    }

    public function save($item)
    {
        $this->_em->persist($item);
        $this->_em->flush();
    }
}
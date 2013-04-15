<?php

namespace CS\SettingsBundle\Collection;

use Doctrine\Common\Collections\ArrayCollection;

class SettingsCollection extends ArrayCollection {

    /**
     * Gets the PHP array representation of this collection recursivley.
     *
     * @return array The PHP array representation of this collection.
     */
    public function toArray()
    {
        $array = array();

        foreach(parent::toArray() as $key => $value) {
            if($value instanceof self) {
                $array[$key] = $value->toArray();
            } else {
                $array[$key] = (string) $value;
            }
        }

        return $array;
    }
}
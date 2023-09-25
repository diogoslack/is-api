<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator;

#[ORM\Entity()]
#[ORM\Table(name: 'object_props')]
class ObjectsFieldsValues
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    private $id;
    
    #[ORM\ManyToOne(targetEntity: Fields::class)]
    #[ORM\JoinColumn(nullable: false)]
    private $fields;

    #[ORM\ManyToOne(targetEntity: Objects::class)]
    #[ORM\JoinColumn(nullable: false)]
    private $objects;
    
    #[ORM\Column(type: Types::TEXT)]
    private $value;

    public function getId()
    {
        return $this->id;
    }

    public function getFields()
    {
        return $this->fields;
    }

    public function setFields($fields)
    {
        $this->fields = $fields;
    }

    public function getObjects()
    {
        return $this->objects;
    }

    public function setObjects($objects)
    {
        $this->objects = $objects;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function setValue($value)
    {
        $this->value = $value;
    }
}

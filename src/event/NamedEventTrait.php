<?php

namespace hiapi\event;

/**
 * Trait NamedEventTrait
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
trait NamedEventTrait
{
    /**
     * @var string
     */
    private $name;
    
    public static function create(string $name, $target = null): self
    {
        $event = new self($target);
        $event->name = $name;
        
        return $event;
    }
    
    /**
     * @inheritdoc
     */
    public function getName()
    {
        return $this->name;
    }
}

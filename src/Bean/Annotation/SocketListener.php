<?php

namespace Swoft\Socket\Bean\Annotation;

/**
 * Class SocketListener
 * @package Swoft\Socket\Bean\Annotation
 * @Annotation
 * @Target("CLASS")
 */
class SocketListener
{
    /**
     * the events of listener
     *
     * @var array
     */
    private $event;

    /**
     * server name
     * @var string
     */
    private $name;

    /**
     * AutoController constructor.
     *
     * @param array $values
     */
    public function __construct(array $values)
    {
        if (isset($values['value']) && isset($values['name'])) {
            $this->event = (array)$values['value'];
            $this->name = (string)$values['name'];
        }

        if (isset($values['event']) && isset($values['name'])) {
            $this->event = (array)$values['event'];
            $this->name = (string)$values['name'];
        }
    }

    /**
     * @return array
     */
    public function getEvent(): array
    {
        return $this->event ?: [];
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name ?: '';
    }
}

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
     * AutoController constructor.
     *
     * @param array $values
     */
    public function __construct(array $values)
    {
        if (isset($values['value']) && isset($values['name'])) {
            $this->event[$values['name']] = (array)$values['value'];
        }

        if (isset($values['event']) && isset($values['name'])) {
            $this->event[$values['name']] = (array)$values['event'];
        }
    }

    /**
     * @return array
     */
    public function getEvent(): array
    {
        return $this->event ?: [];
    }
}

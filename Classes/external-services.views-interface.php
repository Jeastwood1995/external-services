<?php

namespace ExternalServices\Classes;

interface Views_Interface
{
    /**
     * @param string $view
     * @param object|null $class
     * @param bool $main
     * @param bool $ajax
     * @return mixed
     */
    public function returnView(string $view, object $class = null, bool $main = false, bool $ajax = false);

    /**
     * @param string $view
     * @returns \RuntimeException
     */
    public function validateView(string $view);

    /**
     * @param object $object
     * @return mixed
     */
    public function isTableObject(object $object);
}
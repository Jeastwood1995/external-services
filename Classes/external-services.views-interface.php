<?php

namespace ExternalServices\Classes;


interface Views_Interface
{

    public function returnView($view, $class = '');

    public function validateView($view);

    public function isTableObject($object);
}
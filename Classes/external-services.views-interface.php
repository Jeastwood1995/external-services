<?php

namespace ExternalServices\Classes;


interface viewsInterface
{

    public function returnView($view, $class = '');

    public function validateView($view);

    public function isTableObject($object);
}
<?php

namespace JeremyHarris\Build\Template;

use League\Plates\Engine as BaseEngine;
use JeremyHarris\Build\Template\Template;

class Engine extends BaseEngine
{

    /**
     * Makes a template
     *
     * @param string $name Template name
     * @return Template
     */
    public function make($name) {
        return new Template($this, $name);
    }

}

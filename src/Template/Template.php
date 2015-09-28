<?php

namespace JeremyHarris\Build\Template;

use League\Plates\Template\Template as BaseTemplate;

class Template extends BaseTemplate
{

    /**
     * Sets the layout (overridden to be made public)
     *
     * @param string $name Layout name
     * @param array $data Data
     * @return void
     */
    public function layout($name, array $data = array()) {
        parent::layout($name, $data);
    }

}

<?php

namespace NFePHP\Common\Tags;

interface TagInterface
{
    public function loadParameters(\stdClass $std);
    public function toNode();
}

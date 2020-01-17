<?php

namespace hiapi\endpoints;

interface BuilderFactoryInterface
{
    public function endpoint(string $className);
}

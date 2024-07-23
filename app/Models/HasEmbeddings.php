<?php

namespace App\Models;

interface HasEmbeddings
{
    public function getEmbeddings(): array;

    public function getId(): int;
}

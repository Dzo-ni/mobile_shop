<?php
namespace App\Core;

interface EventHandler{
    public function getData(): string;
    public function setData(string $data);
    public function handle():string;
}
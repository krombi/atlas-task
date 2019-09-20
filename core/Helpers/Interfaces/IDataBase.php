<?php
namespace Helpers\Interfaces;

interface IDataBase
{
    public function connect();
    public function query();
}
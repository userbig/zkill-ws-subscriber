<?php

namespace Main\Models;

class BaseModel
{
    protected $connection;

    public function __construct(array $attributes = [])
    {
        $this->connection = pg_connect("host= {$_ENV['POSTGRES_CON']} dbname={$_ENV['POSTGRES_DB']} user={$_ENV['POSTGRES_USER']} password={$_ENV['POSTGRES_PASSWORD']}");
        $this->appendAttributes($attributes);
    }

    public function appendAttributes(array $attributes)
    {
        foreach ($attributes as $key => $value) {
            $this->{$key} = $value;
        }

        return $this;
    }

    public function newInstance(array $attributes = [])
    {
        $model = new static((array) $attributes);

        return $model;
    }
}

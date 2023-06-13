<?php

namespace App;

class ConfigLine 
{
    public $host;
    public $hostName;
    public $user;
    public $port;

    public function validate(): bool
    {
        return $this->host && $this->hostName && $this->user;
    }

    private function getConnectionString(): string 
    {
        $connectionString = "ssh $this->user@$this->hostName";
        if ($this->port) {
            $connectionString .= " -p$this->port";
        }

        return $connectionString;
    }

    public function getAlias(): string 
    {
        return "alias $this->host='".$this->getConnectionString()."'";
    }
}
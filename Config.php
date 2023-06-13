<?php

namespace App;

require_once 'ConfigLine.php';

class Config 
{
    private array $lines;
    private string $startString = "# SSH CONFIG ALIASES";
    private string $endString = "# END SSH CONFIG ALIASES";

    public function __construct(string $path)
    {
        $this->parseFile($path);
    }

    private function parseFile(string $path) {
        $lines = file($path);
        
        $configLine = new ConfigLine();
        foreach ($lines as $number => $line) {
            $lineArr = explode(' ', $line);
            $value = trim($lineArr[count($lineArr) - 1]);

            if (str_contains($line, 'Host ')) {
                $configLine->host = $value;
            } elseif (str_contains($line, 'HostName ')) {
                $configLine->hostName = $value;
            } elseif (str_contains($line, 'User ')) {
                $configLine->user = $value;
            } elseif (str_contains($line, 'Port ')) {
                $configLine->port = $value;
            } elseif ($configLine->validate()) {
                $this->lines[] = $configLine;
                $configLine = new ConfigLine();
            } else {
                echo "Missing property on line $number\n";
            }
        }
    }

    public function makeAliases(): void 
    {
        $aliases = "$this->startString\n";
        foreach ($this->lines as $line) {
            $aliases .= $line->getAlias() . "\n";
        }
        $aliases .= $this->endString;

        $this->insertInBashrc($aliases);
    }

    private function insertInBashrc(string $aliases): void
    {
        $bashrcPath = '/home/' . get_current_user() . '/.bashrc';
        $bashrc = file_get_contents($bashrcPath);

        $startPos = strpos($bashrc, $this->startString);
        if ($startPos === false) {
            $bashrc .= "\n$aliases";
        } else {
            $endPos = strpos($bashrc, $this->endString);
            $bashrc = substr($bashrc, 0, $startPos-1) . "\n" . $aliases . substr($bashrc, $endPos+strlen($this->endString));
        }

        file_put_contents($bashrcPath, $bashrc);
    }
}
<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Mocks a simple command for use in testing
 */
namespace Opulence\Tests\Console\Commands\Mocks;
use Opulence\Console\Commands\Command;
use Opulence\Console\Responses\IResponse;

class SimpleCommand extends Command
{
    /**
     * @param string $name The name of the command
     * @param string $description A brief description of the command
     * @param string $helpText The help text of the command
     */
    public function __construct($name, $description, $helpText = "")
    {
        $this->setName($name);
        $this->setDescription($description);
        $this->setHelpText($helpText);

        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function define()
    {
        // Don't do anything
    }

    /**
     * {@inheritdoc}
     */
    protected function doExecute(IResponse $response)
    {
        $response->write("foo");
    }
}
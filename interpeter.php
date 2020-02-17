<?php

interface Command
{
    public function execute();
}

interface UndoableCommand extends Command
{
    public function undo();

    public function redo();
}

class MakePizza implements Command
{
    private Receiver $output;

    public function __construct(Receiver $console)
    {
        $this->output = $console;
    }

    public function execute()
    {
        $this->output->write('pizza');
    }
}

class AddCheese implements UndoableCommand
{
    private Receiver $output;

    public function __construct(Receiver $console)
    {
        $this->output = $console;
    }

    public function execute()
    {
        $this->output->execute('cheese');
    }

    public function undo()
    {
        $this->output->undo('cheese');
    }

    public function redo()
    {
        $this->output->redo('cheese');
    }
}

class AddMushroom implements UndoableCommand
{
    private Receiver $output;

    public function __construct(Receiver $console)
    {
        $this->output = $console;
    }

    public function execute()
    {
        $this->output->execute('mushroom');
    }

    public function undo()
    {
        $this->output->undo('mushroom');
    }

    public function redo()
    {
        $this->output->redo('mushroom');
    }
}

class AddBacon implements UndoableCommand
{
    private Receiver $output;

    public function __construct(Receiver $console)
    {
        $this->output = $console;
    }

    public function execute()
    {
        $this->output->execute('bacon');
    }

    public function undo()
    {
        $this->output->undo('bacon');
    }

    public function redo()
    {
        $this->output->redo('bacon');

    }
}

class Receiver
{
    private array $output = [];

    public function write($key)
    {
        $this->output[$key] = $key;
    }

    public function getOutput(): string
    {
        return "\n Output: \n" . implode(', ', $this->output);
    }

    public function execute($key)
    {
        $this->output[$key] = $key;
        $this->getOutput();
    }

    public function undo($key)
    {
        if (in_array($key, $this->output)) {
            unset($this->output[$key]);
        }
    }

    public function redo($key)
    {
        $this->output[$key] = $key;
    }
}

class Invoker
{
    private Command $command;

    public function setCommand(Command $cmd)
    {
        $this->command = $cmd;
    }

    public function run()
    {
        $this->command->execute();
    }
}


class Interpreter
{
    private $receiver;
    private $invoker;
    private $ingridient;
    const INGRIDIENTS = ['bacon', 'mushroom', 'cheese'];

    public function __construct($receiver, $invoker)
    {
        $this->receiver = $receiver;
        $this->invoker = $invoker;
    }

    public function interpret($stringIn)
    {
        $arrayIn = explode(" ", $stringIn);
        $returnString = NULL;

        if ('add' == $arrayIn[0]) {
            if ('bacon' == $arrayIn[1]) {
                $this->ingridient = new AddBacon($this->receiver);
            } elseif ('mushroom' == $arrayIn[1]) {
                $this->ingridient = new AddMushroom($this->receiver);
            } elseif ('cheese' == $arrayIn[1]) {
                $this->ingridient = new AddCheese($this->receiver);
            }
            echo 'process add for ' . $arrayIn[1];
            $this->ingridient->execute();
            $this->invoker->run();
            echo $this->receiver->getOutput() . "\n";
        } else if ('undo' == $arrayIn[0]) {
            if (in_array($arrayIn[1], self::INGRIDIENTS)) {
                $this->ingridient->undo();
                echo 'process undo for ' . $arrayIn[1];
            } else {
                echo 'can\'t do a process undo for ' . $arrayIn[1];
            }
            echo $this->receiver->getOutput() . "\n";
        } else if ('redo' == $arrayIn[0]) {
            if (in_array($arrayIn[1], self::INGRIDIENTS)) {
                $this->ingridient->redo();
                echo  'process redo for ' . $arrayIn[1];
            } else {
                echo 'can\'t do a process redo for ' . $arrayIn[1];
            }
            echo $this->receiver->getOutput() . "\n";

        } else {
            echo 'Can not process, can only process add, undo, redo';
        }
    }
}


$invoker = new Invoker();
$receiver = new Receiver();

$invoker->setCommand(new MakePizza($receiver));
$invoker->run();

echo $receiver->getOutput();

$interpreter = new Interpreter($receiver, $invoker);
$interpreter->interpret('add bacon');
$interpreter->interpret('add mushroom');
$interpreter->interpret('undo mushroom');
$interpreter->interpret('redo mushroom');


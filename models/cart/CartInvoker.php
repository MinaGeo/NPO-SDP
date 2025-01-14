<?php
class CartInvoker {
    private $onCommand;
    private $offCommand;

    public function setOnCommand(ICommand $command) {
        $this->onCommand = $command;
    }

    public function setOffCommand(ICommand $command) {
        $this->offCommand = $command;
    }

    public function on() {
        return $this->onCommand->execute();
    }

    public function off() {
        return $this->offCommand->execute();
    }
}
?>
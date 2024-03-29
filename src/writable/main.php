<?php
use function Amp\async;
use function Amp\delay;
use function CatPaw\Store\writable;

function main() {
    $time        = writable(time());
    $unsubscribe = $time->subscribe(fn ($time) => print("the time is $time\n"));

    //unsubscribing mid execution, you should see only 4 prints in the console (instead of 6)
    async(function() use ($unsubscribe) {
        delay(3.5);
        $unsubscribe();
    });
    
    ticktock(5, fn () => $time->set(time()));
}

function ticktock(int $iterations, callable $callback) {
    delay(1);
    $callback();
    $iterations--;
    if ($iterations > 0) {
        ticktock($iterations, $callback);
    }
}
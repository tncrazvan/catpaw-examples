<?php

namespace {

    use Amp\Http\Server\Response;
    use Amp\Http\Status;
    use CatPaw\Web\Attributes\Param;
    use CatPaw\Web\Attributes\StartWebServer;
    use CatPaw\Web\Utilities\Route;


    #[StartWebServer]
    function main() {
        $filter1 = fn(#[Param] int $value) => $value > 0 ? true : new Response(Status::BAD_REQUEST, [], "Bad request :/");

        Route::get(
            path    : "/{value}",
            callback: [
                $filter1,
                fn(#[Param] int $value) => $value,
            ]
        );
    }
}

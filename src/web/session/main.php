<?php

namespace {


    use CatPaw\Web\Attributes\Produces;
    use CatPaw\Web\Attributes\Session;
    use CatPaw\Web\Attributes\StartWebServer;
    use CatPaw\Web\Utilities\Route;

    #[StartWebServer]
    function main() {
        Route::get(
            "/",
            #[Produces("text/html")]
            function(
                #[Session]
                array &$session,
            ) {
                if (!isset($session['created'])) {
                    $session['created'] = time();
                }

                $contents = print_r($session, true);

                return <<<HTML
                    	this is my session <br /><pre>$contents</pre>
                    HTML;
            }
        );
    }
}
<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Symfony\Component\Console\Output\BufferedOutput;
use Illuminate\Support\Facades\Artisan;


class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function getArtisanCommand()
    {
        $output = new BufferedOutput;
        $cmd = array(
            'config:clear', 'config:cache', 'route:clear', 'route:cache',
            'view:clear', 'view:cache', 'clear-compiled', 'queue:restart', 'event:clear', 'event:cache', 'cache:clear'
        );

        echo "<center>";
        echo "========================================================================================== <br><br>";
        foreach ($cmd as $key) {
            // $key = 'optimize:clear';
            Artisan::call($key, [], $output);
            // shell_exec('php ../artisan ' . 'optimize:clear');
            // dump('php ../artisan ' . 'optimize:clear');
            // dump(nl2br($output->fetch()));
            echo nl2br($output->fetch());
        }
        echo "<br>====================================== All Cache Cleared =======================================";
        echo "</center>";
    }
}

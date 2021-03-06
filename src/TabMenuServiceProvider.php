<?php

namespace DNABeast\TabMenu;

use DNABeast\TabMenu\TabMenu;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class TabMenuServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {

        $this->publishes([
            __DIR__.'/config/tabmenu.php' => config_path('tabmenu.php'),
        ]);

        Blade::directive('menu', function($expression=null){
            return "<?= app('DNABeast/TabMenu/TabMenu')->build('";
        });
        Blade::directive('endmenu', function(){
            return "');?>";
        });

    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('DNABeast/TabMenu/TabMenu', function()
        {
            return new TabMenu;
        });
    }
}

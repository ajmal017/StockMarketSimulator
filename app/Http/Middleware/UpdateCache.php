<?php

namespace App\Http\Middleware;
use App\Tools\CacheDrive;
use Closure;
use Cache;
use Session;

use App\Investor;
use App\Market;
use App\Index;
use App\Config;
use App\Share;
use App\Currency;

class UpdateCache
{
    use CacheDrive;
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (!Cache::has('global_config'))
            $this->update_config_cache();
        if (!Cache::has('market'))
            $this->update_market_cache();
        if (!Cache::has('index'))
            $this->update_index_cache();
        if (!Cache::has('currency'))
            $this->update_currency_cache();
        
        return $next($request);
    }
}

<?php

namespace App\Http\Middleware;

use Illuminate\Http\Middleware\TrustHosts as Middleware;

class TrustHosts extends Middleware
{
    /**
     * @return array
     */
    public function hosts(): array
    {
        return [
            $this->allSubdomainsOfApplicationUrl(),
            $this->frontendUrl(),
            $this->mobileUrl(),
        ];
    }

    /**
     * @return string|void
     */
    protected function url($url)
    {
        if ($host = parse_url($url, PHP_URL_HOST)) {
            return '^(.+\.)?' . preg_quote($host) . '$';
        }
    }

    /**
     * @return string|null
     */
    protected function frontendUrl(): ?string
    {
        return $this->url($this->app['config']->get('app.frontend_url'));
    }

    /**
     * @return string|null
     */
    protected function mobileUrl(): ?string
    {
        return $this->url($this->app['config']->get('app.mobile_url'));
    }
}

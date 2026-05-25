<?php

namespace App;

use Illuminate\Foundation\Application;

/**
 * Custom Application for Vercel serverless deployment.
 * Redirects bootstrap/cache to /tmp which is the only writable path.
 */
class VercelApplication extends Application
{
    public function bootstrapPath($path = ''): string
    {
        return '/tmp/bootstrap' . ($path ? DIRECTORY_SEPARATOR . $path : $path);
    }
}

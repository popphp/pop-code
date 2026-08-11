<?php

namespace Pop\Code\Test\Reflection;

trait RoundTripExecutionTrait
{

    /**
     * Insert an autoload require statement immediately after a rendered fragment's namespace
     * declaration, then execute the assembled file in a real PHP subprocess.
     *
     * A rendered fragment (e.g. `(string) $class`) has neither a `<?php` tag nor an autoloader
     * of its own -- both are needed to actually execute it rather than just check `php -l`
     * syntax. The require can't simply be prepended before the fragment: PHP requires
     * `namespace` to be the very first statement in a file (only `declare()` may precede it),
     * and every fragment this helper is used with leads with one.
     *
     * @param  string $render
     * @return array{0: int, 1: array, 2: string}  [exit code, subprocess output lines, full file content]
     */
    protected function executeRenderedFragment(string $render): array
    {
        $autoload         = dirname(__DIR__, 2) . '/vendor/autoload.php';
        $requireStatement = 'require ' . var_export($autoload, true) . ';' . PHP_EOL;

        if (preg_match('/^(.*?namespace\s+[^;]+;\s*\n)/s', $render, $matches)) {
            $content = '<?php' . PHP_EOL . $matches[1] . $requireStatement . substr($render, strlen($matches[1]));
        } else {
            $content = '<?php' . PHP_EOL . $requireStatement . $render;
        }

        $tmpFile = sys_get_temp_dir() . '/pop-code-round-trip-' . uniqid() . '.php';
        file_put_contents($tmpFile, $content);
        exec('php ' . escapeshellarg($tmpFile) . ' 2>&1', $output, $exitCode);
        unlink($tmpFile);

        return [$exitCode, $output, $content];
    }

}

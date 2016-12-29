<?php
namespace doq;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Process\Process;

/**
 * Phar Compiler
 */
class Compiler
{
    const PHAR_NAME = 'doq.phar';

    /**
     * Compiles app into a single phar file
     *
     * @param  string            $pharFile The full path to the file to create
     * @throws \RuntimeException
     */
    public function compile($pharFile = self::PHAR_NAME)
    {
        if (file_exists($pharFile)) {
            unlink($pharFile);
        }

        $phar = new \Phar($pharFile, 0, self::PHAR_NAME);
        $phar->setSignatureAlgorithm(\Phar::SHA1);
        $phar->startBuffering();

        // Add source
        $finder = new Finder();
        $finder->files()
            ->ignoreVCS(true)
            ->name('*.php')
            ->notName('Compiler.php')
            ->notName('ClassLoader.php')
            ->in(__DIR__.'/..')
        ;
        foreach ($finder as $file) {
            $this->addFile($phar, $file);
        }

        // Add dependencies
        $finder = new Finder();
        $finder->files()
            ->ignoreVCS(true)
            ->name('*.php')
            ->name('LICENSE')
            ->exclude('Tests')
            ->exclude('tests')
            ->exclude('docs')
            ->in(__DIR__.'/../../vendor/symfony/')
            ->in(__DIR__.'/../../vendor/psr/')
        ;
        foreach ($finder as $file) {
            $this->addFile($phar, $file);
        }

        // Add composer autoloads
        $finder->files()
            ->ignoreVCS(true)
            ->name('*.php')
            ->in(__DIR__.'/../../vendor/composer/')
            ;
        foreach ($finder as $file) {
            $this->addFile($phar, $file);
        }
        $this->addFile($phar, new \SplFileInfo(__DIR__.'/../../vendor/autoload.php'));
        $this->addFile($phar, new \SplFileInfo(__DIR__.'/../../vendor/composer/ClassLoader.php'));

        // add binary
        $this->addBin($phar);

        // Stub
        $phar->setStub($this->getStub());

        // Save
        $phar->stopBuffering();
    }

    private function addFile($phar, $file, $strip = true)
    {
        $path = strtr(str_replace(dirname(dirname(__DIR__)).DIRECTORY_SEPARATOR, '', $file->getRealPath()), '\\', '/');
        $content = file_get_contents($file);
        if ($strip) {
            $content = $this->stripWhitespace($content);
        } elseif ('LICENSE' === basename($file)) {
            $content = "\n".$content."\n";
        }
        $phar->addFromString($path, $content);
    }

    private function addBin($phar)
    {
        $content = file_get_contents(__DIR__.'/../../bin/doq');
        $content = preg_replace('{^#!/usr/bin/env php\s*}', '', $content);
        $phar->addFromString('bin/doq', $content);
    }

    private function getStub()
    {
        return <<<'EOF'
#!/usr/bin/env php
<?php
Phar::mapPhar('doq.phar');
require 'phar://doq.phar/bin/doq';
__HALT_COMPILER();
EOF;
    }

    /**
     * Removes whitespace from a PHP source string while preserving line numbers.
     *
     * @param  string $source A PHP string
     * @return string The PHP string with the whitespace removed
     */
    private function stripWhitespace($source)
    {
        if (!function_exists('token_get_all')) {
            return $source;
        }
        $output = '';
        foreach (token_get_all($source) as $token) {
            if (is_string($token)) {
                $output .= $token;
            } elseif (in_array($token[0], array(T_COMMENT, T_DOC_COMMENT))) {
                $output .= str_repeat("\n", substr_count($token[1], "\n"));
            } elseif (T_WHITESPACE === $token[0]) {
                // reduce wide spaces
                $whitespace = preg_replace('{[ \t]+}', ' ', $token[1]);
                // normalize newlines to \n
                $whitespace = preg_replace('{(?:\r\n|\r|\n)}', "\n", $whitespace);
                // trim leading spaces
                $whitespace = preg_replace('{\n +}', "\n", $whitespace);
                $output .= $whitespace;
            } else {
                $output .= $token[1];
            }
        }
        return $output;
    }

}

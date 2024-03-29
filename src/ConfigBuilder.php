<?php

declare(strict_types=1);

namespace Sirix\CsFixerConfig;

use BadMethodCallException;
use Gordinskiy\LineLengthChecker\Rules\LineLengthLimit;
use PedroTroller\CS\Fixer\CodingStyle\LineBreakBetweenMethodArgumentsFixer;
use PedroTroller\CS\Fixer\CodingStyle\LineBreakBetweenStatementsFixer;
use PhpCsFixer\Config;
use PhpCsFixer\ConfigInterface;
use PhpCsFixer\Finder;

use function array_merge;
use function call_user_func_array;
use function get_class;
use function is_callable;
use function sprintf;

/**
 * @method self setRiskyAllowed(bool $flag)
 * @method self setUsingCache(bool $flag)
 */
final class ConfigBuilder
{
    private const DEFAULT_CONFIG_NAME = 'Sirix';

    private array $rules = [
        '@PER-CS2.0' => true,
        '@PhpCsFixer' => true,
        'array_syntax' => ['syntax' => 'short'],
        'array_indentation' => true,
        'ordered_imports' => [
            'sort_algorithm' => 'alpha',
            'imports_order' => [
                'const',
                'class',
                'function',
            ],
        ],
        'declare_strict_types' => true,
        'no_unused_imports' => true,
        'function_declaration' => ['closure_function_spacing' => 'none'],
        'not_operator_with_successor_space' => true,
        'concat_space' => ['spacing' => 'one'],
        'native_function_invocation' => [
            'include' => ['@all'],
            'scope' => 'all',
            'strict' => true,
        ],
        'global_namespace_import' => [
            'import_classes' => true,
            'import_constants' => true,
            'import_functions' => true,
        ],
        'Gordinskiy/line_length_limit' => true,
        'PedroTroller/line_break_between_method_arguments' => [
            'max-args' => 4,
            'max-length' => 120,
            'automatic-argument-merge' => true,
            'inline-attributes' => true,
        ],
        'PedroTroller/line_break_between_statements' => true,
    ];

    private Config $config;

    private function __construct()
    {
        $this->config = new Config(self::DEFAULT_CONFIG_NAME);
        $this->config
            ->registerCustomFixers([
                new LineLengthLimit(),
                new LineBreakBetweenMethodArgumentsFixer(),
                new LineBreakBetweenStatementsFixer(),
            ])
            ->setRiskyAllowed(true)
            ->setUsingCache(true)
        ;
    }

    /**
     * @throws BadMethodCallException
     */
    public function __call(string $name, array $arguments): self
    {
        $method = [$this->config, $name];

        if (is_callable($method)) {
            call_user_func_array($method, $arguments);

            return $this;
        }

        throw new BadMethodCallException(sprintf('Method "%s::%s" does not exists.', get_class($this->config), $name));
    }

    public static function create(): self
    {
        return new self();
    }

    public function inDir(string $dir): self
    {
        $this->getFinder()->in([$dir]);

        return $this;
    }

    public function addFiles(array $files): self
    {
        $this->getFinder()->append($files);

        return $this;
    }

    public function setRules(array $rules): self
    {
        $this->rules = array_merge($this->rules, $rules);

        return $this;
    }

    public function getConfig(): ConfigInterface
    {
        return $this->config->setRules($this->rules);
    }

    private function getFinder(): Finder
    {
        return $this->config->getFinder();
    }
}

<?php

declare(strict_types=1);

namespace Sirix\CsFixerConfig\Tests;

use LogicException;
use PhpCsFixer\Config;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Sirix\CsFixerConfig\ConfigBuilder;
use SplFileInfo;
use Traversable;

use function array_diff_assoc;
use function array_map;
use function iterator_to_array;
use function realpath;

/**
 * @internal
 *
 * @coversNothing
 */
class ConfigBuilderTest extends TestCase
{
    private ConfigBuilder $builder;

    protected function setUp(): void
    {
        $this->builder = ConfigBuilder::create();
    }

    #[Test]
    public function itRetrievesDefaultOptions(): void
    {
        $config = $this->builder->getConfig();

        self::assertEquals('Sirix', $config->getName());
        self::assertTrue($config->getRiskyAllowed());
        self::assertTrue($config->getUsingCache());
    }

    #[Test]
    public function itOverridesDefaultOptions(): void
    {
        /** @var Config $config */
        $config = $this->builder
            ->setRiskyAllowed(false)
            ->setUsingCache(false)
            ->getConfig()
        ;

        self::assertFalse($config->getRiskyAllowed());
        self::assertFalse($config->getUsingCache());
    }

    #[Test]
    public function itHasNoDirectoriesByDefault(): void
    {
        $this->expectException(LogicException::class);

        iterator_to_array($this->builder->getConfig()->getFinder());
    }

    #[Test]
    public function itAddsDirectories(): void
    {
        $finder = $this->builder
            ->inDir(__DIR__ . '/../src')
            ->inDir(__DIR__ . '/../test')
            ->getConfig()
            ->getFinder()
        ;

        $items = $this->finderToArray($finder);

        self::assertContains(__FILE__, $items);
        self::assertContains(realpath(__DIR__ . '/../src/ConfigBuilder.php'), $items);
    }

    #[Test]
    public function itAddsFiles(): void
    {
        $finder = $this->builder
            ->addFiles([__FILE__])
            ->getConfig()
            ->getFinder()
        ;

        $items = $this->finderToArray($finder);

        self::assertCount(1, $items);
        self::assertContains(__FILE__, $items);
    }

    #[Test]
    public function itAddsDefaultRules(): void
    {
        $rules = $this->builder
            ->getConfig()
            ->getRules()
        ;

        $expected = [
            '@PER-CS2.0' => true,
        ];

        self::assertEmpty(array_diff_assoc($expected, $rules));
    }

    #[Test]
    public function itOverridesDefaultRules(): void
    {
        $rules = $this->builder
            ->setRules(['@PER-CS2.0' => false])
            ->getConfig()
            ->getRules()
        ;

        $expected = [
            '@PER-CS2.0' => false,
        ];

        self::assertEmpty(array_diff_assoc($expected, $rules));
    }

    /**
     * @return array<string>
     */
    private function finderToArray(Traversable $finder): array
    {
        $map = static function(SplFileInfo $info): string {
            return $info->getRealPath();
        };

        return array_map($map, iterator_to_array($finder, false));
    }
}

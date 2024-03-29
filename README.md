Wrapper with pre-defined rules around the [PHP-CS-Fixer](https://github.com/FriendsOfPHP/PHP-CS-Fixer) package - A tool to automatically fix PHP Coding Standards issues.

## Installation


### Using Composer

The recommended way to install PHP CS Fixer is to use Composer in a dedicated composer.json file in your project, for example in the tools/php-cs-fixer directory:


1. Create a new tools/php-cs-fixer directory:

```bash
mkdir -p tools/php-cs-fixer
```

2. Install via composer:

```bash
composer req --dev --working-dir=tools/php-cs-fixer sirix/php-cs-fixer-config
```

    
### Configuration

1. Create PHP file and name it `.php-cs-fixer.dist.php` and place it inside root directory of project. It will be recognized by PHP CS Fixer automatically.

2. Contents of file should look like this:

```php
    <?php
    
    declare(strict_types=1);
    
    use Sirix\CsFixerConfig\ConfigBuilder;
    
    return ConfigBuilder::create()
        ->inDir(__DIR__ . '/src')
        ->inDir(__DIR__ . '/test')
        ->getConfig()
    ;
```
   
or, you can add additional rules to the configuration file:

```php
        <?php
        
        declare(strict_types=1);
        
        use Sirix\CsFixerConfig\ConfigBuilder;
        
        return ConfigBuilder::create()
            ->inDir(__DIR__ . '/config')
            ->inDir(__DIR__ . '/src')
            ->inDir(__DIR__ . '/test')
            ->setRules([
                'Gordinskiy/line_length_limit' => ['max_length' => 150],
                '@PHP83Migration' => true,
            ])
            ->getConfig()
        ;
```

3. Place `.php-cs-fixer.cache` file into `.gitignore`


## Usage/Examples

* Fix coding standards:

```bash
$ php tools/php-cs-fixer/vendor/bin/php-cs-fixer fix -v
```

* Check coding standards without applying the fix:

```bash
$ php tools/php-cs-fixer/vendor/bin/php-cs-fixer fix --dry-run
```

* Examine the proposed changes:

```bash
$ php tools/php-cs-fixer/vendor/bin/php-cs-fixer fix --dry-run -v --diff
```

    
## Documentation

* Full documentation about all fixers are available here - [PHP-CS-Fixer configuration UI](https://mlocati.github.io/php-cs-fixer-configurator/#version:3.52)

* The official [PHP-CS-Fixer documentation](https://github.com/FriendsOfPHP/PHP-CS-Fixer)

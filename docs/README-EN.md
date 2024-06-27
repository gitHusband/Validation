<p align="center"> <a href="README.md">中文</a> | English</p>

# Generate API documentation

Use [phpDocumentor](https://docs.phpdoc.org/3.0/guide/getting-started/index.html#getting-started) to automatically generate API documentation.

The generated documentation is located at: `docs/built/api/index.html`. You can view it directly by opening it in your browser.

## Generate documentation using Composer

### Install

1. Install phpdocumentor
```BASH
composer require phpdocumentor/phpdocumentor --dev --ignore-platform-reqs
```

2. Install phpdocumentor dependencies
```BASH
cd vendor/phpdocumentor/phpdocumentor
composer install
```

### Generate

```BASH
rm -rf docs/built && vendor/bin/phpdoc -c docs/phpdoc.xml
```

## Generate documentation using Docker

### Install Docker
略

### Generate

```BASH
rm -rf docs/built && docker run --rm -v $(pwd):/data phpdoc/phpdoc -c /data/docs/phpdoc.xml
```

## Debugging documentation

Open file `vendor/twig/twig/src/Environment.php`

```PHP
eval('?>'.$content);
```
Change to:
```PHP
$fileName = basename($name);
$fileName .= ".php";
$subPath = pathinfo($name, PATHINFO_DIRNAME);
$subPath = trim($subPath, '/');
if (!empty($subPath)) $subPath .= '/';
$classPath = $_SERVER['PWD'] . "/docs/template-to-php/default/" . $subPath;
$classFile = $classPath . $fileName;
// echo $name . "\n" . " - " . $classFile . "\n";
if (!file_exists($classPath)) {
    mkdir($classPath, 0777, true);
}
if (!file_exists($classFile)) {
    $handle = fopen($classFile, 'w');
    fwrite($handle, $content);
    fclose($handle);
}
include_once $classFile;
```
Convert the `tiwg` template to a `php` file and save it to `docs/template-to-php/default/`, and then include the file directly so that you can use XDEBUG breakpoint debugging.

<p align="center"><a href="README-EN.md">English</a> | 中文</p>

# 生成 API 文档

## 使用 Composer

### 安装

1. 安装 phpdocumentor
```BASH
composer require phpdocumentor/phpdocumentor --dev --ignore-platform-reqs
```

2. 安装 phpdocumentor 依赖
```BASH
cd vendor/phpdocumentor/phpdocumentor
composer install
```

### 生成文档

```BASH
rm -rf docs/built && vendor/bin/phpdoc -c docs/phpdoc.xml
```

## 使用 Docker

### 安装 Docker
略

### 生成文档

```BASH
rm -rf docs/built && docker run --rm -v $(pwd):/data phpdoc/phpdoc -c /data/docs/phpdoc.xml
```

### 修改调试文档

打开文件 `vendor/twig/twig/src/Environment.php`

```PHP
eval('?>'.$content);
```
改为：
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

这样可以使用 XDEBUG 断点调试

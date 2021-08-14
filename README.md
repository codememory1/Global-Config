# Global Configuration

## Установка

```
composer require codememory/global-config
```

## Команды

* `g-config:init` Инициализация конфигурации
* `g-config:merge` Merge конфигураций в один конфиг
    * Options:
        * `--all` Merge всей конфигурации
        * `--configPath={path}` Merge конкретной конфигурации
        * `--backup={before|after}` Создать backup конфигурации *before* перед Merge, *after* после Merge
* `g-config:init-from-backup` Инициализировать конфигурацию из backup файла
* `g-config:backup` Сделать backup глобальной конфигурации

## Методы GlobalConfig
* `setPath(): GlobalConfigInterface` Установить путь, где будет храниться конфигурация
    * string **$path**


* `setFilename(): GlobalConfigInterface` Установить имя файла, в котором будет находиться конфигурация
    * string **$filename**


* `setBackupFilename(): GlobalConfigInterface` Установить имя backup файла
    * string **$filename**


* `getPath(): string` Возвращает путь глобальной конфигурации


* `getFilename(): string` Возвращает имя файла конфигурации


* `getExtension(): string` Возвращает расширение файла конфигурации


* `getBackupFilename(): string` Возвращает имя backup файла


* `get(): mixed` Получить значение по ключу
    * string **$keys**


* `getAll(): array` Получить всю глобальную конфигурацию


* `exist(): bool` Проверить существование файла глобальной конфигурации

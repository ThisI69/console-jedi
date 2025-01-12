# Работа с модулями

Команды неймспейса `module` предназначена для управления модулями *1С-Битрикс*: установки, удаления, а также загрузки и
установки новых версий из [Marketplace](http://marketplace.1c-bitrix.ru).

## Важная информация

> Не все модули поддерживают режим автоматической установки, возможны проблемы с некоторыми сторонними решениями.

Существует два способа [установки модулей](https://dev.1c-bitrix.ru/learning/course/?COURSE_ID=43&LESSON_ID=3475):

* **Классический**. Привычный интерактивный режим установки через административную часть.  
  При этом вызываются методы `DoInstall` и `DoUninstall` объекта модуля.

* **Автоматический**. Выполняется на этапе установки продукта «1C-Битрикс: Управление сайтом». В **Console Jedi**
  используется этот способ.  
  При этом последовательно вызываются методы объекта модуля `InstallDB`, `InstallEvents`, `InstallFiles`.

В документации продукта 1С-Битрикс не описана возможность такой автоматической установки, но на практике любой модуль,
который положили в `/bitrix/modules/` до установки ядра, будет установлен вместе с ядром. Автоматическая установка
модуля также используется
при [загрузке решения из Marketplace на этапе установки](https://dev.1c-bitrix.ru/learning/course/?COURSE_ID=35&LESSON_ID=3181)
.

Разработчики сторонних модулей часто реализуют только методы `DoInstall` и `DoUninstall`, тем самым, делая невозможной
установку в автоматическом режиме.

> В своих модулях используйте `DoInstall` и `DoUninstall` только для вывода и обработки **пользовательского интерфейса**, а действия по установке/удалению модуля реализуйте в методах `InstallDB`, `InstallEvents`, `InstallFiles`.
>
> Корректную реализацию смотрите в [примере класса модуля](https://dev.1c-bitrix.ru/learning/course/?COURSE_ID=43&LESSON_ID=3223).

## Загрузка модуля (`module:load`)

Загружает модуль из Marketplace (если не загружен), устанавливает все обновления модуля и устанавливает его.

```
module:load [-ct|--confirm-thirdparty] [-nu|--no-update] [-ni|--no-register] [-b|--beta] [--] <module>
```

Опции |   | Описание
---|----|---
-ct | --confirm-thirdparty | Пропустить предупреждение об установке сторонних модулей
-nu | --no-update | Не устанавливать обновления
-ni | --no-register | Не устанавливать модуль (только загрузить)
-b  | --beta | Включить загрузку и установку бета-версий модуля
\<module\> | | Код модуля (vendor.module)

## Установка модуля (`module:register`)

Устанавливает существующий модуль.

```
module:register [-ct|--confirm-thirdparty] [--] <module>
```

Опции |   | Описание
---|----|---
-ct | --confirm-thirdparty | Пропустить предупреждение об установке сторонних модулей
\<module\> | | Код модуля (vendor.module)

## Удаление модуля (`module:unregister` и `module:remove`)

Удаляет модуль из системы.

`module:remove` дополнительно удаляет файлы модуля из `/bitrix/modules/` или `/local/modules/`.

```
module:unregister [-ct|--confirm-thirdparty] [--] <module>
module:remove [-ct|--confirm-thirdparty] [--] <module>
```

Опции |   | Описание
---|----|---
-ct | --confirm-thirdparty | Пропустить предупреждение об установке сторонних модулей
\<module\> | | Код модуля (vendor.module)

## Обновление модуля (`module:update`)

Устанавливает обновления указанного модуля из *Marketplace*.

> На данный момент установка обновлений модулей ядра не поддерживается.

```
module:update [-ct|--confirm-thirdparty] [-b|--beta] [--] <module>
```

Опции |   | Описание
---|----|---
-ct | --confirm-thirdparty | Пропустить предупреждение об установке сторонних модулей
-b  | --beta | Включить загрузку и установку бета-версий модуля
\<module\> | | Код модуля (vendor.module)

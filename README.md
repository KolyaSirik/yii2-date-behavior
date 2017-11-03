Yii2 Date Behavior
========================

## Installation

The preferred way to install this extension is through [composer](http://getcomposer.org/download/). Check the [composer.json](https://github.com/kartik-v/yii2-widget-datetimepicker/blob/master/composer.json) for this extension's requirements and dependencies. Read this [web tip /wiki](http://webtips.krajee.com/setting-composer-minimum-stability-application/) on setting the `minimum-stability` settings for your application's composer.json.

To install, either run

```
$ php composer.phar require kolyasiryk/yii2-date-behavior "*"
```

or add

```
"kolyasiryk/yii2-date-behavior": "*"
```

to the ```require``` section of your `composer.json` file.

## Usage

```php
    public function behaviors()
    {
        return [
            [
                'class' => \kolyasiryk\behaviors\DateBehavior::className(),
                'datetimeDisplayFormat' => 'php:d.m.Y H:i',
                'attributes' => [
                    'start_date' => 'datetime',
                    'end_date' => 'datetime',
                ],
            ],
        ];
    }
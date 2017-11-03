Yii2 Date Behavior
========================

Usage
-----
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
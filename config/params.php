<?php
Yii::setAlias('@root', realpath(dirname(__FILE__).'/..'));
return [
    'adminEmail' => 'admin@example.com',
    'salt' => 'abcdef',
    'sougouDictDir' => ROOT_PATH . '/upload/dict/sougou',
];

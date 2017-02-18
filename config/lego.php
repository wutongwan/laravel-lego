<?php

/**
 * Laravel Lego Config
 */

return [
    /**
     * Field 的配置
     */
    'field' => [
        /**
         * 字段的默认属性, 特殊用途字段(例如 自动补全)除外
         */
        'attributes' => [
            'class' => 'form-control',
        ],
    ],

    /**
     * 自定义的 Field
     */
    'user-defined-fields' => [
    ],

    /**
     * 默认 paginator 配置
     */
    'paginator' => [
        'per-page' => 100,
        'page-name' => 'page',
    ],
];

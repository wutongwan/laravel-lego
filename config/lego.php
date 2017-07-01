<?php

/**
 * Laravel Lego Config
 */

return [
    /**
     * 静态文件配置
     */
    'assets' => [
        /**
         * 下面静态文件是否全局自动引入，false 时，需要在外部自行引入相关静态文件
         */
        'global' => [
            'bootstrap' => true,
            'jQuery' => true,
        ],
    ],

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

        'provider' => [
            \Lego\Field\Provider\Checkboxes::class => [
                'separator' => '|'
            ]
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

    /**
     * 控件的配置项
     */
    'widgets' => [
        /**
         * form's configuration
         */
        'form' => [
            /**
             * 表单的默认 view
             */
            'default-view' => 'lego::default.form.horizontal',
        ],
        /**
         * filter's configuration
         */
        'filter' => [
            'default-view' => 'lego::default.filter.inline',
        ],
        /**
         * grid's configuration
         */
        'grid' => [
            /**
             * 移动端使用定制版 view
             */
            'responsive' => true,

            /**
             * Pipes
             */
            'pipes' => [
                \Lego\Widget\Grid\Pipes4Datetime::class,
                \Lego\Widget\Grid\Pipes4String::class,
            ],
        ],
    ]
];

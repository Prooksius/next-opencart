<aside class="main-sidebar">

    <section class="sidebar">

        <!-- Sidebar user panel -->
        <div class="user-panel">
            <div class="pull-left image">
                <img src="/admin/img/admin.jpg" class="img-circle" alt="User Image"/>
            </div>
            <div class="pull-left info">
                <p><?= Yii::$app->user->identity->fullname ?></p>

                <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
            </div>
        </div>

        <!-- search form
        <form action="#" method="get" class="sidebar-form">
            <div class="input-group">
                <input type="text" name="q" class="form-control" placeholder="Search..."/>
              <span class="input-group-btn">
                <button type='submit' name='search' id='search-btn' class="btn btn-flat"><i class="fa fa-search"></i>
                </button>
              </span>
            </div>
        </form>
         /.search form -->
        <?= dmstr\widgets\Menu::widget(
            [
                'options' => ['class' => 'sidebar-menu tree', 'data-widget'=> 'tree'],
                'items' => [
                    ['label' => Yii::t('app', 'Menu'), 'options' => ['class' => 'header']],
                    [
                      'label' => Yii::t('app', 'Catalog'),
                      'visible' => !Yii::$app->user->isGuest,
                      'icon' => 'tags',
                      'url' => '#',
                      'items' => [
                        [
                          'label' => Yii::t('app', 'Categories'), 
                          'visible' => !Yii::$app->user->isGuest,
                          'icon' => 'folder-open-o', 
                          'url' => ['/catalog/category'], 
                          'active' => strpos($this->context->route, 'catalog/category') === 0
                        ],
                        [
                          'label' => Yii::t('app', 'Products'), 
                          'visible' => !Yii::$app->user->isGuest,
                          'icon' => 'cube', 
                          'url' => ['/catalog/product'], 
                          'active' => strpos($this->context->route, 'catalog/product') === 0
                        ],
                        [
                          'label' => Yii::t('attribute', 'Attributes'), 
                          'visible' => !Yii::$app->user->isGuest,
                          'icon' => 'map-signs', 
                          'url' => '#', 
                          'items' => [
                            [
                              'label' => Yii::t('attribute', 'Attributes'), 
                              'visible' => !Yii::$app->user->isGuest,
                              'icon' => 'edit', 
                              'url' => ['/catalog/attribute'], 
                              'active' => strpos($this->context->route, 'catalog/attribute') === 0 && strpos($this->context->route, 'catalog/attribute-group') === false,
                            ],
                            [
                              'label' => Yii::t('attribute', 'Attribute groups'), 
                              'visible' => !Yii::$app->user->isGuest,
                              'icon' => 'pencil-square', 
                              'url' => ['/catalog/attribute-group'], 
                              'active' => strpos($this->context->route, 'catalog/attribute-group') === 0,
                            ],
                          ],
                        ],
                        [
                          'label' => Yii::t('option', 'Options'), 
                          'icon' => 'server', 
                          'url' => ['/catalog/option'], 
                          'visible' => !Yii::$app->user->isGuest,
                          'active' => strpos($this->context->route, 'catalog/option') === 0
                        ],
                        [
                          'label' => Yii::t('app', 'Filters'), 
                          'icon' => 'filter', 
                          'url' => ['/catalog/filter-group'], 
                          'visible' => !Yii::$app->user->isGuest,
                          'active' => strpos($this->context->route, 'catalog/filter-group') === 0 || strpos($this->context->route, 'catalog/filter') === 0
                        ],
                        [
                          'label' => Yii::t('product', 'Product Colors'), 
                          'icon' => 'object-group', 
                          'url' => ['/catalog/pcolor'], 
                          'visible' => !Yii::$app->user->isGuest,
                          'active' => strpos($this->context->route, 'catalog/pcolor') === 0
                        ],
                        [
                          'label' => Yii::t('app', 'Manufacturers'), 
                          'icon' => 'object-group', 
                          'url' => ['/catalog/manufacturer'], 
                          'visible' => !Yii::$app->user->isGuest,
                          'active' => strpos($this->context->route, 'catalog/manufacturer') === 0
                        ],
                        [
                          'label' => Yii::t('app', 'Downloads'), 
                          'icon' => 'download', 
                          'url' => ['/catalog/download'], 
                          'visible' => !Yii::$app->user->isGuest,
                          'active' => strpos($this->context->route, 'catalog/download') === 0
                        ],
                        [
                          'label' => Yii::t('app', 'Reviews'), 
                          'icon' => 'comments', 
                          'url' => ['/catalog/rewiew'], 
                          'visible' => !Yii::$app->user->isGuest,
                          'active' => strpos($this->context->route, 'catalog/rewiew') === 0
                        ],
                        [
                          'label' => Yii::t('app', 'FAQ'), 
                          'icon' => 'question-circle-o', 
                          'url' => ['/catalog/faq'], 
                          'visible' => !Yii::$app->user->isGuest,
                          'active' => strpos($this->context->route, 'catalog/faq') === 0
                        ],
                        [
                          'label' => Yii::t('app', 'Single Articles'),
                          'icon' => 'newspaper-o', 
                          'url' => ['/catalog/pages'],
                          'visible' => !Yii::$app->user->isGuest,
                          'active' => strpos($this->context->route, 'catalog/pages') === 0
                        ],
                        [
                          'label' => Yii::t('blog', 'Blog'), 
                          'visible' => !Yii::$app->user->isGuest,
                          'icon' => 'file-text-o', 
                          'url' => '#', 
                          'items' => [
                            [
                              'label' => Yii::t('blog', 'Blog Categories'), 
                              'visible' => !Yii::$app->user->isGuest,
                              'icon' => 'folder-open-o', 
                              'url' => ['/catalog/blog-category'], 
                              'active' => strpos($this->context->route, 'catalog/blog-category') === 0,
                            ],
                            [
                              'label' => Yii::t('blog', 'Blog Articles'), 
                              'visible' => !Yii::$app->user->isGuest,
                              'icon' => 'file-text-o', 
                              'url' => ['/catalog/blog-article'], 
                              'active' => strpos($this->context->route, 'catalog/blog-article') === 0,
                            ],
                          ],
                        ],
                      ],
                    ],
                    [
                      'label' => Yii::t('app', 'Design'),
                      'visible' => !Yii::$app->user->isGuest,
                      'icon' => 'delicious',
                      'url' => '#',
                      'items' => [
                        [
                          'label' => Yii::t('layout', 'Layouts'), 
                          'icon' => 'folder-open-o', 
                          'url' => ['/design/layout'], 
                          'active' => strpos($this->context->route, 'design/layout') === 0
                        ],
                        [
                          'label' => Yii::t('app', 'Main Menu'), 
                          'icon' => 'folder-open-o', 
                          'url' => ['/menu-main'], 
                          'active' => strpos($this->context->route, 'menu-main') === 0
                        ],
                        [
                          'label' => Yii::t('app', 'Sandwich Menu'), 
                          'icon' => 'bars', 
                          'url' => ['/menu-sandvich'], 
                          'active' => strpos($this->context->route, 'menu-sandvich') === 0
                        ],
                        [
                          'label' => Yii::t('app', 'Banners'), 
                          'visible' => !Yii::$app->user->isGuest,
                          'icon' => 'image', 
                          'url' => ['/design/banner'], 
                          'active' => strpos($this->context->route, 'design/banner') === 0
                        ],
                      ],
                    ],
                    [
                      'label' => Yii::t('module', 'Extensions'),
                      'visible' => !Yii::$app->user->isGuest,
                      'icon' => 'delicious',
                      'url' => '#',
                      'items' => [
                        [
                          'label' => Yii::t('module', 'Modules'), 
                          'icon' => 'folder-open-o', 
                          'url' => ['/extension/modules'], 
                          'active' => strpos($this->context->route, 'extension/module') === 0
                        ],
                        [
                          'label' => Yii::t('module', 'Delivery'), 
                          'icon' => 'folder-open-o', 
                          'url' => ['/extension/deliveries'], 
                          'active' => strpos($this->context->route, 'extension/deliver') === 0
                        ],
                        [
                          'label' => Yii::t('module', 'Payment'), 
                          'icon' => 'folder-open-o', 
                          'url' => ['/extension/payments'], 
                          'active' => strpos($this->context->route, 'extension/payment') === 0
                        ],
                        [
                          'label' => Yii::t('module', 'Totals'), 
                          'icon' => 'folder-open-o', 
                          'url' => ['/extension/totals'], 
                          'active' => strpos($this->context->route, 'extension/total') === 0
                        ],
                      ],
                    ],
                    [
                      'label' => Yii::t('customer', 'Customers'),
                      'visible' => !Yii::$app->user->isGuest,
                      'icon' => 'user',
                      'url' => '#',
                      'items' => [
                        [
                          'label' => Yii::t('customer', 'Customers'), 
                          'visible' => !Yii::$app->user->isGuest,
                          'icon' => 'user', 
                          'url' => ['/customer/customer'], 
                          'active' => strpos($this->context->route, 'customer/customer') === 0
                        ],
                        [
                          'label' => Yii::t('customer', 'Customer groups'), 
                          'visible' => !Yii::$app->user->isGuest,
                          'icon' => 'users', 
                          'url' => ['/customer/customer-group'], 
                          'active' => strpos($this->context->route, 'customer/customer-group') === 0
                        ],
                      ],
                    ],
                    [
                      'label' => Yii::t('order', 'Sales'),
                      'visible' => !Yii::$app->user->isGuest,
                      'icon' => 'money ',
                      'url' => '#',
                      'items' => [
                        [
                          'label' => Yii::t('order', 'Orders'), 
                          'visible' => !Yii::$app->user->isGuest,
                          'icon' => 'user', 
                          'url' => ['/sale/order'], 
                          'active' => strpos($this->context->route, 'sale/order') === 0
                        ],
                      ],
                    ],
                    [
                      'label' => Yii::t('localisation', 'Localisation'),
                      'visible' => !Yii::$app->user->isGuest,
                      'icon' => 'cog',
                      'url' => '#',
                      'items' => [
                        ['label' => Yii::t('app', 'Languages'), 'icon' => 'language', 'url' => ['/localisation/language']],
                        ['label' => Yii::t('currency', 'Currencies'), 'icon' => 'language', 'url' => ['/localisation/currency']],
                        [
                          'label' => YII::t('localisation', 'Translation groups'),
                          'icon' => 'globe', 
                          'url' => ['/localisation/translate-group'],
                          'active' => strpos($this->context->route, 'localisation/translate-group') === 0
                        ],
                        [
                          'label' => YII::t('app', 'Translations'),
                          'icon' => 'globe', 
                          'url' => ['/localisation/translation-word'],
                          'active' => strpos($this->context->route, 'localisation/translation-word') === 0
                        ],
                        [
                          'label' => Yii::t('app', 'Countries'),
                          'active' => strpos($this->context->route, 'localisation/country') === 0,
                          'icon' => 'globe',
                          'url' => ['/localisation/country'],
                        ],
                        [
                          'label' => Yii::t('app', 'Length units'),
                          'active' => strpos($this->context->route, 'localisation/length-class') === 0,
                          'icon' => 'globe',
                          'url' => ['/localisation/length-class'],
                        ],
                        [
                          'label' => Yii::t('app', 'Weight units'),
                          'active' => strpos($this->context->route, 'localisation/weight-class') === 0,
                          'icon' => 'globe',
                          'url' => ['/localisation/weight-class'],
                        ],
                        [
                          'label' => Yii::t('localisation', 'Stock statuses'),
                          'active' => strpos($this->context->route, 'localisation/stock-status') === 0,
                          'icon' => 'globe',
                          'url' => ['/localisation/stock-status'],
                        ],
                        [
                          'label' => Yii::t('localisation', 'Order statuses'),
                          'active' => strpos($this->context->route, 'localisation/order-status') === 0,
                          'icon' => 'globe',
                          'url' => ['/localisation/order-status'],
                        ],
                      ],
                    ],
                    [
                      'label' => Yii::t('app', 'Settings'),
                      'visible' => !Yii::$app->user->isGuest,
                      'icon' => 'bar-chart',
                      'url' => '#',
                      'items' => [
                        ['label' => Yii::t('app', 'Main settings'), 'icon' => 'tasks', 'url' => ['/settings']],
                        [
                          'label' => Yii::t('app', 'Admins'),
                          'visible' => !Yii::$app->user->isGuest,
                          'icon' => 'handshake-o', 
                          'url' => ['/user'], 'active' => strpos($this->context->route, 'user') === 0
                        ],
                      ],
                    ],
                    ['label' => Yii::t('app', 'Login'), 'url' => ['site/login'], 'visible' => Yii::$app->user->isGuest],
                    ['label' => Yii::t('app', 'Password reset'), 'url' => ['site/request-password-reset'], 'visible' => Yii::$app->user->isGuest],
 /*
                    [
                        'label' => 'Some tools',
                        'icon' => 'share',
                        'visible' => !Yii::$app->user->isGuest,
                        'url' => '#',
                        'items' => [
                            ['label' => 'Gii', 'icon' => 'file-code-o', 'url' => ['/gii'],],
                            ['label' => 'Debug', 'icon' => 'dashboard', 'url' => ['/debug'],],
                            [
                                'label' => 'Level One',
                                'icon' => 'circle-o',
                                'url' => '#',
                                'items' => [
                                    ['label' => 'Level Two', 'icon' => 'circle-o', 'url' => '#',],
                                    [
                                        'label' => 'Level Two',
                                        'icon' => 'circle-o',
                                        'url' => '#',
                                        'items' => [
                                            ['label' => 'Level Three', 'icon' => 'circle-o', 'url' => '#',],
                                            ['label' => 'Level Three', 'icon' => 'circle-o', 'url' => '#',],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
 */
                ],
            ]
        ) ?>

    </section>

</aside>

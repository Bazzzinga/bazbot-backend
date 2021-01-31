<aside class="main-sidebar">

    <section class="sidebar">

        <?= dmstr\widgets\Menu::widget(
            [
                'options' => ['class' => 'sidebar-menu tree', 'data-widget'=> 'tree'],
                'items' => [
                    ['label' => 'Gii', 'icon' => 'file-code-o', 'url' => ['/gii']],
                    ['label' => 'Debug', 'icon' => 'dashboard', 'url' => ['/debug']],
                    ['label' => 'Streams', 'icon' => 'dashboard', 'url' => ['/admin/streams']],
                    ['label' => 'Video', 'icon' => 'dashboard', 'url' => ['/admin/video']],
                    ['label' => 'Domains', 'icon' => 'dashboard', 'url' => ['/admin/domains']],
                    ['label' => 'Platforms', 'icon' => 'dashboard', 'url' => ['/admin/platforms']],
                    ['label' => 'Users', 'icon' => 'dashboard', 'url' => ['/admin/users']],
                ],
            ]
        ) ?>

    </section>

</aside>

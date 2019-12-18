<?php
/**
 * @var \Bonnier\Willow\Base\Controllers\Admin\NotFoundListController $this
 */
?>
<div class="wrap">
    <h1 class="wp-heading-inline">Not Found URLs</h1>
    <hr class="wp-header-end">

    <form id="bonnier-willow-base-overview-form" method="get">
        <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
        <?php
        foreach ($this->getNotices() as $notice) {
            $type = array_keys($notice)[0];
            $message = $notice[$type];
            ?>
            <div id="message" class="notice notice-<?php echo $type; ?> is-dismissible">
                <p>
                    <strong><?php echo ucfirst($type); ?>:</strong>
                    <?php echo $message; ?>
                </p>
            </div>
            <?php
        }
        if (!empty($this->locales)) {
            ?>
            <div style="height: 50px;">
                <h2 class="screen-reader-text">Filter Not Found list</h2>
                <ul class="subsubsub">
                    <?php
                    $allActive = 'current';
                    $allUrl = esc_url(add_query_arg(['notfound_locale' => null, 'notfound_ignored' => null]));
                    if ($this->request->query->has('notfound_locale') || $this->request->query->has('notfound_ignored')) {
                        $allActive = '';
                    }
                    echo sprintf('<li><a class="%s" href="%s">All</a> |</li>', $allActive, $allUrl);

                    foreach($this->locales as $notFoundLocale) {
                        $title = sprintf(
                                '%s (%s)',
                                strtoupper($notFoundLocale['locale']),
                                $notFoundLocale['amount']
                        );
                        $url = esc_url(add_query_arg(['notfound_locale' => $notFoundLocale['locale']]));
                        $active = '';
                        if ($this->request->get('notfound_locale') === $notFoundLocale['locale']) {
                            $active = 'current';
                        }
                        echo sprintf('<li><a class="%s" href="%s">%s</a> |</li>', $active, $url, $title);
                    }

                    $ignoredActive = '';
                    if ($this->request->get('notfound_ignored')) {
                        $ignoredActive = 'current';
                    }
                    $ignoredUrl = esc_url(add_query_arg(['notfound_ignored' => 1]));
                    echo sprintf('<li><a class="%s" href="%s">Ignored entriess</a></li>', $ignoredActive, $ignoredUrl);
                    ?>
                </ul>
            </div>
            <?php
        }
        $this->displaySearch();
        $this->display();
        ?>
    </form>
</div>

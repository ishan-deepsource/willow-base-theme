<?php
/**
 * @var \Bonnier\Willow\Base\Controllers\Admin\NotFoundController $this
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
                    <li>
                        <a class="<?php echo $this->request->get('notfound_locale') ? '' : 'current'; ?>"
                           href="<?php echo esc_url(add_query_arg(['notfound_locale' => null])); ?>">All</a>
                        |
                    </li>
                    <?php
                    foreach($this->locales as $notFoundLocale) {
                        $locale = $notFoundLocale['locale'];
                        $amount = $notFoundLocale['amount'];
                        ?>
                        <li>
                            <a class="<?php echo $this->request->get('notfound_locale') === $locale ? 'current' : ''; ?>"
                               href="<?php echo esc_url(add_query_arg(['notfound_locale' => $locale])); ?>"><?php echo strtoupper($locale) . ' (' . $amount . ')' ?></a>
                            |
                        </li>
                        <?php
                    }
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

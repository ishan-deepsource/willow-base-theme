<!DOCTYPE html>
<html <?php language_attributes(); ?> class="no-js no-svg">
    <head>
        <meta charset="<?php bloginfo('charset'); ?>">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="profile" href="http://gmpg.org/xfn/11">
        <meta name="fragment" content="!"> <!-- For SEO purposes -->
        <?php wp_head(); ?>
        <script type="text/javascript">
          window.wp = {
            options: {
              assetsUri: '<?php echo get_template_directory_uri() ?>',
              dateFormat: {
                timeZone: "<?php echo get_option('timezone_string') ?: 'Europe/Copenhagen' ?>"
              },
              language: {
                slug: "<?php echo pll_current_language() ?>",
              },
              imgixHost: "<?php echo getenv('AWS_S3_DOMAIN') ?>",
              env: "<?php echo env('WP_ENV') ?>"
            },
            object: <?php echo get_queried_object_json() ?>
          };
        </script>
    </head>
    <body>
        <?php do_action('body_start'); ?>
        <div id="app"></div>
        <?php wp_footer(); ?>
    </body>
</html>

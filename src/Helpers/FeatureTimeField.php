<?php

namespace Bonnier\Willow\Base\Helpers;

use Bonnier\Willow\Base\Models\FeatureDate;
use Bonnier\Willow\Base\Models\WpComposite;
use Illuminate\Support\Carbon;

class FeatureTimeField
{
    public static function register()
    {
        add_action('post_submitbox_misc_actions', [__CLASS__, 'generate']);
        add_action('save_post', [__CLASS__, 'save'], 10, 2);
    }

    public static function save(int $postID, \WP_Post $post)
    {
        if (
            $post->post_type !== WpComposite::POST_TYPE ||
            wp_is_post_revision($postID) ||
            wp_is_post_autosave($postID)
        ) {
            return;
        }
        if (!array_key_exists('feature_aa', $_POST) || !array_key_exists('hidden_feature_aa', $_POST)) {
            return;
        }
        $featureDate = Carbon::create(
            $_POST['feature_aa'],
            $_POST['feature_mm'],
            $_POST['feature_jj'],
            $_POST['feature_hh'],
            $_POST['feature_mn']
        );
        $originalDate = Carbon::create(
            $_POST['hidden_feature_aa'],
            $_POST['hidden_feature_mm'],
            $_POST['hidden_feature_jj'],
            $_POST['hidden_feature_hh'],
            $_POST['hidden_feature_mn']
        );
        if ($featureDate->equalTo($originalDate)) {
            return;
        }

        FeatureDate::updateOrCreate(['post_id' => $postID], ['timestamp' => $featureDate]);
    }

    public static function generate(\WP_Post $post)
    {
        if ($post->post_type !== WpComposite::POST_TYPE) {
            return;
        }
        self::scripts();
        $featureDate = FeatureDate::find($post->ID);
        $timestamp = Carbon::now()->format('Y-m-d H:i:s');
        if ($featureDate) {
            $timestamp = $featureDate->timestamp->format('Y-m-d H:i:s');
        } ?>
        <div class="misc-pub-section misc-pub-feature-date">
                <span id="feature_timestamp"><?php
                    if ($featureDate) {
                        $date = date_i18n(
                            __('M j, Y @ H:i'),
                            strtotime($featureDate->timestamp)
                        );
                    } else {
                        $date = __('Not set');
                    }
        printf(
                            'Featured on: %s',
                        '<b>' . $date . '</b>'
                    ); ?></span>
            <a href="#" id="edit_feature_timestamp" class="edit-timestamp hide-if-no-js" role="button"><span aria-hidden="true"><?php _e('Edit'); ?></span> <span class="screen-reader-text"><?php _e('Edit date and time'); ?></span></a>
            <fieldset id="timestampdiv" class="hide-if-js">
                <legend class="screen-reader-text">Date and time</legend>
                <?php self::renderPicker($timestamp); ?>
            </fieldset>
        </div>
        <?php
    }

    private static function renderPicker($postDate)
    {
        global $wp_locale;

        $time_adj = current_time('timestamp');
        $jj = mysql2date('d', $postDate, false);
        $mm = mysql2date('m', $postDate, false);
        $aa = mysql2date('Y', $postDate, false);
        $hh = mysql2date('H', $postDate, false);
        $mn = mysql2date('i', $postDate, false);
        $ss = mysql2date('s', $postDate, false);

        $month = '<label><span class="screen-reader-text">' . __('Month') . '</span><select id="mm" name="feature_mm">' . PHP_EOL;
        for ($i = 1; $i < 13; $i = $i +1) {
            $monthnum = zeroise($i, 2);
            $monthtext = $wp_locale->get_month_abbrev($wp_locale->get_month($i));
            $month .= "\t\t\t" . '<option value="' . $monthnum . '" data-text="' . $monthtext . '" ' . selected($monthnum, $mm, false) . '>';
            $month .= sprintf(__('%1$s-%2$s'), $monthnum, $monthtext) . "</option>\n";
        }
        $month .= '</select></label>';

        $day = '<label><span class="screen-reader-text">' . __('Day') . '</span><input type="text" id="jj" name="feature_jj" value="' . $jj . '" size="2" maxlength="2" autocomplete="off" /></label>';
        $year = '<label><span class="screen-reader-text">' . __('Year') . '</span><input type="text" id="aa" name="feature_aa" value="' . $aa . '" size="4" maxlength="4" autocomplete="off" /></label>';
        $hour = '<label><span class="screen-reader-text">' . __('Hour') . '</span><input type="text" id="hh" name="feature_hh" value="' . $hh . '" size="2" maxlength="2" autocomplete="off" /></label>';
        $minute = '<label><span class="screen-reader-text">' . __('Minute') . '</span><input type="text" id="mn" name="feature_mn" value="' . $mn . '" size="2" maxlength="2" autocomplete="off" /></label>';

        echo '<div class="timestamp-wrap">';
        /* translators: 1: month, 2: day, 3: year, 4: hour, 5: minute */
        printf(__('%1$s %2$s, %3$s @ %4$s:%5$s'), $month, $day, $year, $hour, $minute);

        echo '</div><input type="hidden" id="feature_ss" name="feature_ss" value="' . $ss . '" />';

        echo "\n\n";
        $map = array(
            'feature_mm' => $mm,
            'feature_jj' => $jj,
            'feature_aa' => $aa,
            'feature_hh' => $hh,
            'feature_mn' => $mn,
        );
        foreach ($map as $timeunit => $unit) {
            echo '<input type="hidden" id="hidden_' . $timeunit . '" name="hidden_' . $timeunit . '" value="' . $unit . '" />' . "\n";
        } ?>

        <p>
            <a href="#" class="save-timestamp hide-if-no-js button"><?php _e('OK'); ?></a>
            <a href="#" class="cancel-timestamp hide-if-no-js button-cancel"><?php _e('Cancel'); ?></a>
        </p>
        <?php
    }

    private static function scripts()
    {
        wp_register_script(
            'feature_date_script',
            get_theme_file_uri('/assets/js/feature_date.js'),
            [],
            filemtime(get_theme_file_path('/assets/js/feature_date.js'))
        );
        wp_enqueue_script('feature_date_script');
    }
}

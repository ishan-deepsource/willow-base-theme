<?php
/**
 * @var \Bonnier\Willow\Base\Controllers\Admin\NotFoundSettingsController $this
 */
?>
<div class="wrap">
    <h1 class="wp-heading-inline">Not Found Settings</h1>
    <hr class="wp-header-end">
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
    ?>
    <h2>Ignore extensions</h2>
    <p>To avoid false positives, you can enter fileendings, that should be ignored by this module</p>
    <form id="remove-ignore-extension" method="post">
        <?php if (!empty($this->ignoredExtensions)): ?>
            <p>Ignored extensions:
                <?php
                foreach($this->ignoredExtensions as $extension) {
                    echo sprintf(
                    '<code>.%s <button type="submit" name="remove-ignore-extension-submit" class="button button-link" value="%s">&times;</button></code>',
                        $extension,
                        $extension
                    );
                }
                ?>
            </p>
        <?php endif; ?>
    </form>
    <form id="add-ignore-extensions" method="post">
        <table class="form-table">
            <tbody>
                <tr>
                    <th>Extension</th>
                    <td>
                        <code>/some/url.</code>
                        <input
                            id="add-ignore-extension-input"
                            class="regular-text code"
                            name="add-ignore-extension-input"
                            type="text"
                            required
                            value=""
                            placeholder="eg. 'php', 'html', 'xhtml', 'js'"
                        />
                    </td>
                </tr>
            </tbody>
        </table>
        <p class="submit">
            <input
                id="add-ignore-extension-submit"
                class="button button-primary"
                type="submit"
                name="add-ignore-extension-submit"
                value="Save Changes"
            />
        </p>
    </form>
    <hr />
</div>
<style>
    .button.button-link {
        color: inherit;
        text-decoration: none;
    }
    .button.button-link:hover {
        background: none;
        color: #a00;
    }
</style>

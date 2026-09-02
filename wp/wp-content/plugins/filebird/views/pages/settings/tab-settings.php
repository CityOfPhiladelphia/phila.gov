<?php
defined( 'ABSPATH' ) || exit;

?>
<table class="form-table">
    <tr>
        <th scope="row">
            <label
                for="njt_fbv_folder_per_user"><?php esc_html_e( 'Each user has his own folders?', 'filebird' ); ?></label>
        </th>
        <td>
            <label class="njt-switch">
                <input type="checkbox" name="njt_fbv_folder_per_user" class="njt-submittable"
                    id="njt_fbv_folder_per_user" value="1"
                    <?php checked( get_option( 'njt_fbv_folder_per_user' ), '1' ); ?> />
                <span class="slider round">
                    <span class="njt-switch-cursor"></span>
                </span>
            </label>
        </td>
    </tr>
    <tr>
        <th scope="row">
            <label for="njt_fbv_show_breadcrumb"><?php esc_html_e( 'Show Breadcrumb', 'filebird' ); ?></label>
        </th>
        <td>
            <label class="njt-switch fbv-pro-feature">
                <input type="checkbox" name="showBreadCrumb" class="njt-submittable" id="njt_fbv_show_breadcrumb" disabled/>
                <span class="slider round">
                    <span class="njt-switch-cursor"></span>
                </span>
            </label>
        </td>
    </tr>
    <tr>
        <th scope="row">
            <label for="njt_fbv_allow_svg_upload"><?php esc_html_e( 'Allow built-in SVG upload & sanitization', 'filebird' ); ?></label>
        </th>
        <td>
            <label class="njt-switch">
                <input value="1" type="checkbox" name="njt_fbv_allow_svg_upload" class="njt-submittable" id="njt_fbv_allow_svg_upload" <?php checked( get_option( 'njt_fbv_allow_svg_upload' ), '1' ); ?> />/>
                <span class="slider round">
                    <span class="njt-switch-cursor"></span>
                </span>
            </label>
        </td>
    </tr>
    <tr>
        <th scope="row">
            <label><?php esc_html_e( 'Folder Counter', 'filebird' ); ?></label>
        </th>
        <td>
            <select name="folderCounterType" class="fbv-pro-feature">
                <option value="counter_file_in_folder" selected>
                    <?php esc_html_e( 'Count files in each folder', 'filebird' ); ?>
                </option>
                <option value="counter_file_in_folder_and_sub" disabled>
                    <?php esc_html_e( 'Count files in both parent folder and subfolders (PRO)', 'filebird' ); ?>
                </option>
            </select>
        </td>
    </tr>
    <tr>
        <th scope="row">
            <label><?php esc_html_e( 'FileBird Theme', 'filebird' ); ?></label>
        </th>
        <td>
            <div class="fbv-theme">
                <div class="fbv-radio-select-img">
                    <input type="radio" id="default" name="theme" value="default" checked/>
                    <label for="default">
                        <div class="fbv-radio-img-wrap">
                            <img src="<?php echo NJFB_PLUGIN_URL . 'assets/img/default.svg'; ?>">
                        </div>
                        <span><?php esc_html_e( 'FileBird Default', 'filebird' ); ?></span>
                    </label>
                </div>
                <div class="fbv-radio-select-img fbv-pro-feature">
                    <input type="radio" id="windows" name="theme" value="windows" disabled/>
                    <label for="windows">
                        <div class="fbv-radio-img-wrap">
                            <img src="<?php echo NJFB_PLUGIN_URL . 'assets/img/windows.svg'; ?>">
                        </div>
                        <span><?php esc_html_e( 'Windows 11', 'filebird' ); ?></span>
                    </label>
                </div>
                <div class="fbv-radio-select-img fbv-pro-feature">
                    <input type="radio" id="dropbox" name="theme" value="dropbox" disabled/>
                    <label for="dropbox">
                        <div class="fbv-radio-img-wrap">
                            <img src="<?php echo NJFB_PLUGIN_URL . 'assets/img/dropbox.svg'; ?>">
                        </div>
                        <span><?php esc_html_e( 'Dropbox', 'filebird' ); ?></span>
                    </label>
                </div>
            </div>
        </td>
    </tr>
</table>
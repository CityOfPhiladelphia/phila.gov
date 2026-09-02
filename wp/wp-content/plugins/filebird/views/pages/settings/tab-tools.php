<?php

defined( 'ABSPATH' ) || exit;

$apiKey    = get_option( 'fbv_rest_api_key', '' );
$languages = apply_filters( 'wpml_active_languages', null, array( 'skip_missing' => 0 ) );

?>

<div id="fbv-tools-setting">
	<table class="form-table">
		<tbody>
			<?php if ( $oldFolders > 0 ) : ?>
			<tr>
				<th scope="row">
					<label><?php esc_html_e( 'Import from old version', 'filebird' ); ?></label>
				</th>
				<td>
					<button type="button"
						class="button button-primary njt_fbv_import_from_old_now njt-button-loading"><?php esc_html_e( 'Update now', 'filebird' ); ?></button>
					<p class="description">
						<?php esc_html_e( 'By running this action, all folders created in version 3.9 & earlier installs will be imported.', 'filebird' ); ?>
					</p>
				</td>
			</tr>
			<?php endif; ?>
			<tr>
				<th scope="row">
					<label for="">
						<?php esc_html_e( 'REST API key', 'filebird' ); ?>
					</label>
				</th>
				<td>
					<input type="text" id="fbv_rest_api_key" class="regular-text <?php echo ( strlen( $apiKey ) === 0 ) ? 'hidden' : ''; ?>"
						value="<?php esc_attr_e( $apiKey ); ?>" onclick="this.select()" />
					<button type="button"
						class="button button-primary fbv_generate_api_key_now njt-button-loading"><?php esc_html_e( 'Generate', 'filebird' ); ?></button>
					<p class="description">
						<?php echo sprintf( esc_html__( 'Please see FileBird API for developers %1$shere%2$s.', 'filebird' ), '<a target="_blank" href="https://ninjateam.gitbook.io/filebird/integrations/developer-zone/apis">', '</a>' ); ?>
					</p>
				</td>
			</tr>
			<tr>
                <th scope="row">
                    <label for="">
                        <?php esc_html_e( 'Attachment Size', 'filebird' ); ?>
                    </label>
                </th>
                <td>
                    <div class="fbv-generate-attachment-size">
                        <button type="button"
                            class="button button-primary njt_fbv_generate_attachment_size njt-button-loading">
                            <?php esc_html_e( 'Generate', 'filebird' ); ?>
                        </button>
                        <span class="processing-status"></span>
                    </div>
                    <p class="description">
                        <?php esc_html_e( 'Generate attachment size used in "Sort by size" function.', 'filebird' ); ?>
                    </p>
                </td>
            </tr>
			<tr>
				<th colspan="2">
					<div class="fbv-text-divider">
						<span><?php esc_html_e( 'Danger Zone', 'filebird' ); ?></span>
					</div>
				</th>
			</tr>
			<tr>
				<th scope="row">
					<label><?php esc_html_e( 'Clear all data', 'filebird' ); ?></label>
				</th>
				<td>
					<button type="button"
						class="button njt_fbv_clear_all_data njt-button-loading"><?php esc_html_e( 'Clear', 'filebird' ); ?>
					</button>
					<p class="description">
						<?php esc_html_e( 'This action will delete all FileBird data, FileBird settings and bring you back to WordPress default media library.', 'filebird' ); ?>
					</p>
				</td>
			</tr>

            <?php if ( ! empty( $languages ) ) : ?>
            <tr>
                <th scope="row">
                    <label for="">
                        <?php esc_html_e( 'Sync WPML', 'filebird' ); ?>
                    </label>
                </th>
                <td>
                    <div class="fbv-generate-attachment-size">
                        <button type="button" class="button button-primary njt_fbv_sync_wpml njt-button-loading">
                            <?php esc_html_e( 'Sync', 'filebird' ); ?>
                        </button>
                        <span class="processing-status"></span>
                    </div>
                    <p class="description">
                        <?php esc_html_e( 'Assign WPML existing translated media to FileBird folders.', 'filebird' ); ?>
                    </p>
                </td>
            </tr>
            <?php endif; ?>
		</tbody>
	</table>
</div>
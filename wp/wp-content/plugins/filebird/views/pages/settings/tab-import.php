<div id="fbv-import-setting">
	<?php if ( ( $countEnhancedFolder + $countWpmlfFolder + $countWpmfFolder + $countRealMediaFolder + $countHappyFiles + $countPremioFolder + $countFemlFolder + $countMediamaticFolder ) > 0 ) : ?>
		<h2><?php esc_html_e( 'Import', 'filebird' ); ?></h2>
	<p>
		<?php esc_html_e( 'Import categories/folders from other plugins. We import virtual folders, your website will be safe, don\'t worry ;)', 'filebird' ); ?>
	</p>
	<table class="form-table">
		<tbody>
			<tr class="<?php echo esc_attr( $countEnhancedFolder <= 3 ? 'hidden' : '' ); ?>">
				<th scope="row">
					<label for="">
						<?php esc_html_e( 'Enhanced Media Library plugin by wpUXsolutions', 'filebird' ); ?>
					</label>
				</th>
				<td>
					<?php if ( $countEnhancedFolder > 0 ) : ?>
					<button class="button button-primary button-large njt-fb-import njt-button-loading"
						data-site="enhanced" type="button"
						data-count="<?php echo $countEnhancedFolder; ?>"><?php esc_html_e( 'Import Now', 'filebird' ); ?></button>
					<?php endif; ?>
					<p class="description">
						<?php
						echo sprintf( esc_html__( 'We found you have %1$s(%2$s)%3$s categories you created from %4$sEnhanced Media Library%5$s plugin.', 'filebird' ), '<strong>', esc_html( $countEnhancedFolder ), '</strong>', '<strong>', '</strong>' );
						if ( $countEnhancedFolder > 0 ) {
							echo sprintf( esc_html__( ' Would you like to import to %1$sFileBird%2$s?', 'filebird' ), '<strong>', '</strong>' );
						}
						?>
					</p>
				</td>
			</tr>
			<tr class="<?php echo esc_attr( $countWpmlfFolder <= 3 ? 'hidden' : '' ); ?>">
				<th scope="row">
					<label for="">
						<?php esc_html_e( 'WordPress Media Library Folders by Max Foundry', 'filebird' ); ?>
					</label>
				</th>
				<td>
					<?php if ( $countWpmlfFolder > 0 ) : ?>
					<button class="button button-primary button-large njt-fb-import njt-button-loading"
						data-site="wpmlf" type="button"
						data-count="<?php echo esc_attr( $countWpmlfFolder ); ?>"><?php esc_html_e( 'Import Now', 'filebird' ); ?></button>
					<?php endif; ?>
					<p class="description">
						<?php
						echo sprintf( esc_html__( 'We found you have %1$s(%2$s)%3$s categories you created from %4$sWordPress Media Library Folders%5$s plugin.', 'filebird' ), '<strong>', esc_html( $countWpmlfFolder ), '</strong>', '<strong>', '</strong>' );
						if ( $countWpmlfFolder > 0 ) {
							echo sprintf( esc_html__( ' Would you like to import to %1$sFileBird%2$s?', 'filebird' ), '<strong>', '</strong>' );
						}
						?>
					</p>
				</td>
			</tr>
			<tr class="<?php echo esc_attr( $countWpmfFolder <= 3 ? 'hidden' : '' ); ?>">
				<th scope="row">
					<label for="">
						<?php esc_html_e( 'WP Media folder by Joomunited', 'filebird' ); ?>
					</label>
				</th>
				<td>
					<?php if ( $countWpmfFolder > 0 ) : ?>
					<button class="button button-primary button-large njt-fb-import njt-button-loading" data-site="wpmf"
						type="button"
						data-count="<?php echo esc_attr( $countWpmfFolder ); ?>"><?php esc_html_e( 'Import Now', 'filebird' ); ?></button>
					<?php endif; ?>
					<p class="description">
						<?php
						echo sprintf( esc_html__( 'We found you have %1$s(%2$s)%3$s categories you created from %4$sWP Media folder%5$s plugin.', 'filebird' ), '<strong>', esc_html( $countWpmfFolder ), '</strong>', '<strong>', '</strong>' );
						if ( $countWpmfFolder > 0 ) {
							echo sprintf( esc_html__( ' Would you like to import to %1$sFileBird%2$s?', 'filebird' ), '<strong>', '</strong>' );
						}
						?>
					</p>
				</td>
			</tr>
			<tr class="<?php echo esc_attr( $countRealMediaFolder <= 3 ? 'hidden' : '' ); ?>">
				<th scope="row">
					<label for="">
						<?php esc_html_e( 'WP Real Media Library by devowl.io GmbH', 'filebird' ); ?>
					</label>
				</th>
				<td>
					<?php if ( $countRealMediaFolder > 0 ) : ?>
					<button class="button button-primary button-large njt-fb-import njt-button-loading"
						data-site="realmedia" type="button"
						data-count="<?php echo esc_attr( $countRealMediaFolder ); ?>"><?php esc_html_e( 'Import Now', 'filebird' ); ?></button>
					<?php endif; ?>
					<p class="description">
						<?php
						echo sprintf( esc_html__( 'We found you have %1$s(%2$s)%3$s categories you created from %4$sWP Real Media Library%5$s plugin.', 'filebird' ), '<strong>', esc_html( $countRealMediaFolder ), '</strong>', '<strong>', '</strong>' );
						if ( $countRealMediaFolder > 0 ) {
							echo sprintf( esc_html__( ' Would you like to import to %1$sFileBird%2$s?', 'filebird' ), '<strong>', '</strong>' );
						}
						?>
					</p>
				</td>
			</tr>
			<tr class="<?php echo esc_attr( $countHappyFiles <= 3 ? 'hidden' : '' ); ?>">
				<th scope="row">
					<label for="">
						<?php esc_html_e( 'HappyFiles by Codeer', 'filebird' ); ?>
					</label>
				</th>
				<td>
					<?php if ( $countHappyFiles > 0 ) : ?>
					<button class="button button-primary button-large njt-fb-import njt-button-loading"
						data-site="happyfiles" type="button"
						data-count="<?php echo esc_attr( $countHappyFiles ); ?>"><?php esc_html_e( 'Import Now', 'filebird' ); ?></button>
					<?php endif; ?>
					<p class="description">
						<?php
						echo sprintf( esc_html__( 'We found you have %1$s(%2$s)%3$s categories you created from %4$sHappyFiles%5$s plugin.', 'filebird' ), '<strong>', esc_html( $countHappyFiles ), '</strong>', '<strong>', '</strong>' );
						if ( $countHappyFiles > 0 ) {
							echo sprintf( esc_html__( ' Would you like to import to %1$sFileBird%2$s?', 'filebird' ), '<strong>', '</strong>' );
						}
						?>
					</p>
				</td>
			</tr>
			<tr class="<?php echo esc_attr( $countPremioFolder <= 3 ? 'hidden' : '' ); ?>">
				<th scope="row">
					<label for="">
						<?php esc_html_e( 'Folders by Premio', 'filebird' ); ?>
					</label>
				</th>
				<td>
					<?php if ( $countPremioFolder > 0 ) : ?>
					<button class="button button-primary button-large njt-fb-import njt-button-loading"
						data-site="premio" type="button"
						data-count="<?php echo esc_attr( $countPremioFolder ); ?>"><?php esc_html_e( 'Import Now', 'filebird' ); ?></button>
					<?php endif; ?>
					<p class="description">
						<?php
						echo sprintf( esc_html__( 'We found you have %1$s(%2$s)%3$s categories you created from %4$sFolders%5$s plugin.', 'filebird' ), '<strong>', esc_html( $countPremioFolder ), '</strong>', '<strong>', '</strong>' );
						if ( $countPremioFolder > 0 ) {
							echo sprintf( esc_html__( ' Would you like to import to %1$sFileBird%2$s?', 'filebird' ), '<strong>', '</strong>' );
						}
						?>
					</p>
				</td>
			</tr>
			<tr class="<?php echo esc_attr( $countFemlFolder <= 3 ? 'hidden' : '' ); ?>">
                <th scope="row">
                    <label for="">
                        <?php esc_html_e( 'WP Media Folders by Damien Barrère', 'filebird' ); ?>
                    </label>
                </th>
                <td>
                    <?php if ( $countFemlFolder > 0 ) : ?>
                    <button class="button button-primary button-large njt-fb-import njt-button-loading"
                        data-site="feml" type="button"
                        data-count="<?php echo esc_attr( $countFemlFolder ); ?>"><?php esc_html_e( 'Import Now', 'filebird' ); ?></button>
                    <?php endif; ?>
                    <p class="description">
                        <?php
                    echo sprintf( esc_html__( 'We found you have %1$s(%2$s)%3$s categories you created from %4$sWP Media Folders%5$s plugin.', 'filebird' ), '<strong>', esc_html( $countFemlFolder ), '</strong>', '<strong>', '</strong>' );
						if ( $countFemlFolder > 0 ) {
							echo sprintf( esc_html__( ' Would you like to import to %1$sFileBird%2$s?', 'filebird' ), '<strong>', '</strong>' );
						}
						?>
                    </p>
                </td>
            </tr>
			<tr class="<?php echo esc_attr( $countMediamaticFolder <= 3 ? 'hidden' : '' ); ?>">
				<th scope="row">
					<label for="">
						<?php esc_html_e( 'Mediamatic by Plugincraft ', 'filebird' ); ?>
					</label>
				</th>
				<td>
					<?php if ( $countMediamaticFolder > 0 ) : ?>
					<button class="button button-primary button-large njt-fb-import njt-button-loading"
						data-site="mediamatic" type="button"
						data-count="<?php echo esc_attr( $countMediamaticFolder ); ?>"><?php esc_html_e( 'Import Now', 'filebird' ); ?></button>
					<?php endif; ?>
					<p class="description">
						<?php
						echo sprintf( esc_html__( 'We found you have %1$s(%2$s)%3$s categories you created from %4$sFolders%5$s plugin.', 'filebird' ), '<strong>', esc_html( $countMediamaticFolder ), '</strong>', '<strong>', '</strong>' );
						if ( $countMediamaticFolder > 0 ) {
							echo sprintf( esc_html__( ' Would you like to import to %1$sFileBird%2$s?', 'filebird' ), '<strong>', '</strong>' );
						}
						?>
					</p>
				</td>
			</tr>
		</tbody>
	</table>
	<div class="fbv-row-breakline">
		<span class="fbv-breakline"></span>
	</div>
	<?php endif; ?>
	<h2><?php esc_html_e( 'Export', 'filebird' ); ?></h2>
	<table class="form-table">
		<tbody>
			<tr>
				<th scope="row">
					<label for="">
						<?php esc_html_e( 'Export CSV', 'filebird' ); ?>
					</label>
				</th>
				<td>
					<div class="flex-item-center">
						<button class="button button-primary button-large njt-fb-csv-export njt-button-loading"
							type="button">
							<?php esc_html_e( 'Export Now', 'filebird' ); ?>
						</button>
						<a id="njt-fb-download-csv" href="javascript:;" class="hidden">Download File</a>
					</div>
					<p class="description">
						<?php esc_html_e( 'The current folder structure will be exported.', 'filebird' ); ?>
					</p>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="">
						<?php esc_html_e( 'Import CSV', 'filebird' ); ?>
					</label>
				</th>
				<td>
                    <div class="flex-item-center">
                        <input type="file" accept=".csv" id="njt-fb-upload-csv" name="csv_file">
                    </div>
                    <p class="hidden">
                        Choose user folder:
                        <select id="njt-fb-csv-user-import">
                        </select>
                        <button disabled class="button button-large njt-fb-csv-import hidden njt-button-loading" type="button">
                            <?php esc_html_e( 'Import Now', 'filebird' ); ?>
                        </button>
                    </p>
                    <p id="njt-fb-csv-user-import-description"></p>
                    <p class="description">
                        <?php esc_html_e( 'Choose FileBird CSV file to import.', 'filebird' ); ?><br />
                        <?php esc_html_e( '(Please check to make sure that there is no duplicated name. The current folder structure is preserved.)', 'filebird' ); ?>
                        <a href="https://ninjateam.gitbook.io/filebird/settings/import-and-export-folder-structure" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Learn more', 'filebird' ); ?></a>
                        <br />
                    </p>
                </td>
			</tr>
		</tbody>
	</table>
</div>

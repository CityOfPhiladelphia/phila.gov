<?php

$pluginsAllowedTags = array(
	'a'       => array(
		'href'   => array(),
		'title'  => array(),
		'target' => array(),
	),
	'abbr'    => array( 'title' => array() ),
	'acronym' => array( 'title' => array() ),
	'code'    => array(),
	'pre'     => array(),
	'em'      => array(),
	'strong'  => array(),
	'ul'      => array(),
	'ol'      => array(),
	'li'      => array(),
	'p'       => array(),
	'br'      => array(),
);

foreach ( (array) $recommendedPlugins as $recommendedPlugin ) {
	if ( is_object( $recommendedPlugin ) ) {
		$recommendedPlugin = (array) $recommendedPlugin;
	}

		$pluginTitle = wp_kses( $recommendedPlugin['name'], $pluginsAllowedTags );

		// Remove any HTML from the description.
		$description = wp_strip_all_tags( $recommendedPlugin['short_description'] );

		$name = wp_strip_all_tags( $pluginTitle );

		$downloadLink = isset( $recommendedPlugin['download_link'] ) ? $recommendedPlugin['download_link'] : null;

		$compatible_php = true;
		$compatible_wp  = true;
		$tested_wp      = true;

		$actionLinks = array();

		$pluginStatus = '<span class="plugin-status-not-install">' . esc_html__( 'Not installed', 'filebird' ) . '</span>';

	if ( current_user_can( 'install_plugins' ) || current_user_can( 'update_plugins' ) ) {
		$pluginProVer = $this->check_pro_version_exists( $recommendedPlugin );
		if ( false === $pluginProVer ) {
			if ( 'yaypricing' === $recommendedPlugin['slug'] ) {
				$installStatus = array(
					'status'  => 'install',
					'url'     => $recommendedPlugin['download_link'],
					'version' => '',
					'file'    => $pluginProVer,
				);
			} else {
				$installStatus = install_plugin_install_status( $recommendedPlugin );
			}
		} else {
			$installStatus = array(
				'status'  => 'latest_installed',
				'url'     => false,
				'version' => '',
				'file'    => $pluginProVer,
			);
		}

		switch ( $installStatus['status'] ) {
			case 'install':
				if ( $installStatus['url'] ) {
					if ( $compatible_php && $compatible_wp ) {
						if ( 'yaypricing' === $recommendedPlugin['slug'] ) {
							$actionLinks[] = sprintf(
								'<a href="%s" target="_bank"><button class="button button-primary" data-install-url="%s" aria-label="%s">%s</button></a>',
								esc_attr( $downloadLink ),
								esc_attr( $downloadLink ),
								/* translators: %s: Plugin name and version. */
								esc_attr( sprintf( _x( 'Install %s now', 'plugin', 'filebird' ), $name ) ),
								__( 'Install Now', 'filebird' )
							);
						} else {
							$actionLinks[] = sprintf(
								'<button class="install-now button button-primary" data-install-url="%s" aria-label="%s">%s</button>',
								esc_attr( $downloadLink ),
								/* translators: %s: Plugin name and version. */
								esc_attr( sprintf( _x( 'Install %s now', 'plugin', 'filebird' ), $name ) ),
								__( 'Install Now', 'filebird' )
							);
						}
					} else {
						$actionLinks[] = sprintf(
							'<button type="button" class="button button-disabled" disabled="disabled">%s</button>',
							_x( 'Cannot Install', 'plugin', 'filebird' )
						);
					}
				}
				$pluginStatus = '<span class="plugin-status-not-install" data-plugin-url="' . esc_attr( $downloadLink ) . '">' . esc_html__( 'Not installed', 'filebird' ) . '</span>';
				break;

			case 'update_available':
				if ( $installStatus['url'] ) {
					if ( $compatible_php && $compatible_wp ) {
						if ( 'yaypricing' === $recommendedPlugin['slug'] ) {
							$actionLinks[] = sprintf(
								'<button class="button aria-button-if-js" data-plugin="%s" data-slug="%s" data-update-url="%s" aria-label="%s" data-name="%s">%s</button>',
								esc_attr( $installStatus['file'] ),
								esc_attr( $recommendedPlugin['slug'] ),
								esc_url( $installStatus['url'] ),
								/* translators: %s: Plugin name and version. */
								esc_attr( sprintf( _x( 'Update %s now', 'plugin', 'filebird' ), $name ) ),
								esc_attr( $name ),
								__( 'Update Now', 'filebird' )
							);
						} else {
							$actionLinks[] = sprintf(
								'<button class="update-now button aria-button-if-js" data-plugin="%s" data-slug="%s" data-update-url="%s" aria-label="%s" data-name="%s">%s</button>',
								esc_attr( $installStatus['file'] ),
								esc_attr( $recommendedPlugin['slug'] ),
								esc_url( $installStatus['url'] ),
								/* translators: %s: Plugin name and version. */
								esc_attr( sprintf( _x( 'Update %s now', 'plugin', 'filebird' ), $name ) ),
								esc_attr( $name ),
								__( 'Update Now', 'filebird' )
							);
						}
					} else {
						$actionLinks[] = sprintf(
							'<button type="button" class="button button-disabled" disabled="disabled">%s</button>',
							_x( 'Cannot Update', 'plugin', 'filebird' )
						);
					}
				}
				if ( is_plugin_active( $installStatus['file'] ) ) {
					$pluginStatus = '<span class="plugin-status-active">Active</span>';
				} else {
					$pluginStatus = '<span class="plugin-status-inactive" data-plugin-file="' . esc_attr( $installStatus['file'] ) . '">Inactive</span>';
				}
				break;

			case 'latest_installed':
			case 'newer_installed':
				if ( is_plugin_active( $installStatus['file'] ) ) {
					$pluginStatus  = '<span class="plugin-status-active">Active</span>';
					$actionLinks[] = sprintf(
						'<button type="button" class="button button-disabled" disabled="disabled">%s</button>',
						_x( 'Activated', 'plugin', 'filebird' )
					);
				} elseif ( current_user_can( 'activate_plugin', $installStatus['file'] ) ) {
					$pluginStatus = '<span class="plugin-status-inactive" data-plugin-file="' . esc_attr( $installStatus['file'] ) . '">Inactive</span>';
					if ( $compatible_php && $compatible_wp ) {
						$buttonText = __( 'Activate', 'filebird' );
						/* translators: %s: Plugin name. */
						$buttonLabel = _x( 'Activate %s', 'plugin', 'filebird' );
						$activateUrl = add_query_arg(
							array(
								'_wpnonce' => wp_create_nonce( 'activate-plugin_' . $installStatus['file'] ),
								'action'   => 'activate',
								'plugin'   => $installStatus['file'],
							),
							network_admin_url( 'plugins.php' )
						);

						if ( is_network_admin() ) {
							$buttonText = __( 'Network Activate', 'filebird' );
							/* translators: %s: Plugin name. */
							$buttonLabel = _x( 'Network Activate %s', 'plugin', 'filebird' );
							$activateUrl = add_query_arg( array( 'networkwide' => 1 ), $activateUrl );
						}

						$actionLinks[] = sprintf(
							'<button class="button activate-now" data-plugin-file="%1$s" aria-label="%2$s">%3$s</button>',
							esc_attr( $installStatus['file'] ),
							esc_attr( sprintf( $buttonLabel, $recommendedPlugin['name'] ) ),
							$buttonText
						);
					} else {
						$actionLinks[] = sprintf(
							'<button type="button" class="button button-disabled" disabled="disabled">%s</button>',
							_x( 'Cannot Activate', 'plugin', 'filebird' )
						);
					}
				} else {
					$actionLinks[] = sprintf(
						'<button type="button" class="button button-disabled" disabled="disabled">%s</button>',
						_x( 'Installed', 'plugin', 'filebird' )
					);
				}
				break;
		}
	}

		$detailsLink = self_admin_url(
			'plugin-install.php?tab=plugin-information&amp;plugin=' . $recommendedPlugin['slug'] .
			'&amp;TB_iframe=true&amp;width=600&amp;height=550'
		);

		$pluginIconUrl = $recommendedPlugin['icon'];

		/**
		 * Filters the install action links for a plugin.
		 *
		 * @since 2.7.0
		 *
		 * @param string[] $actionLinks An array of plugin action links. Defaults are links to Details and Install Now.
		 * @param array    $plugin       The plugin currently being listed.
		 */
		$actionLinks = apply_filters( 'plugin_install_action_links', $actionLinks, $recommendedPlugin );

	?>
	<div class="plugin-card plugin-card-<?php echo sanitize_html_class( $recommendedPlugin['slug'] ); ?>">
		<?php
		if ( ! $compatible_php || ! $compatible_wp ) {
			echo '<div class="notice inline notice-error notice-alt"><p>';
			if ( ! $compatible_php && ! $compatible_wp ) {
				echo esc_html__( 'This plugin doesn&#8217;t work with your versions of WordPress and PHP.', 'filebird' );
				if ( current_user_can( 'update_core' ) && current_user_can( 'update_php' ) ) {
					printf(
						/* translators: 1: URL to WordPress Updates screen, 2: URL to Update PHP page. */
						' ' . esc_html__( '<a href="%1$s">Please update WordPress</a>, and then <a href="%2$s">learn more about updating PHP</a>.', 'filebird' ),
						esc_url( self_admin_url( 'update-core.php' ) ),
						esc_url( wp_get_update_php_url() )
					);
					wp_update_php_annotation( '</p><p><em>', '</em>' );
				} elseif ( current_user_can( 'update_core' ) ) {
					printf(
						/* translators: %s: URL to WordPress Updates screen. */
						' ' . esc_html__( '<a href="%s">Please update WordPress</a>.', 'filebird' ),
						esc_url( self_admin_url( 'update-core.php' ) )
					);
				} elseif ( current_user_can( 'update_php' ) ) {
					printf(
						/* translators: %s: URL to Update PHP page. */
						' ' . esc_html__( '<a href="%s">Learn more about updating PHP</a>.', 'filebird' ),
						esc_url( wp_get_update_php_url() )
					);
					wp_update_php_annotation( '</p><p><em>', '</em>' );
				}
			} elseif ( ! $compatible_wp ) {
				echo esc_html__( 'This plugin doesn&#8217;t work with your version of WordPress.', 'filebird' );
				if ( current_user_can( 'update_core' ) ) {
					printf(
						/* translators: %s: URL to WordPress Updates screen. */
						' ' . esc_html__( '<a href="%s">Please update WordPress</a>.', 'filebird' ),
						esc_url( self_admin_url( 'update-core.php' ) )
					);
				}
			} elseif ( ! $compatible_php ) {
				echo esc_html__( 'This plugin doesn&#8217;t work with your version of PHP.', 'filebird' );
				if ( current_user_can( 'update_php' ) ) {
					printf(
						/* translators: %s: URL to Update PHP page. */
						' ' . esc_html__( '<a href="%s">Learn more about updating PHP</a>.', 'filebird' ),
						esc_url( wp_get_update_php_url() )
					);
					wp_update_php_annotation( '</p><p><em>', '</em>' );
				}
			}
			echo '</p></div>';
		}
		?>
		<div class="plugin-card-top">
			<div class="name column-name">
				<h3>
					<a href="<?php echo esc_url( $detailsLink ); ?>" class="thickbox open-plugin-details-modal">
					<?php echo wp_kses_post( $pluginTitle ); ?>
					<img src="<?php echo esc_url( $pluginIconUrl ); ?>" class="plugin-icon" alt="" />
					</a>
				</h3>
			</div>
			<div class="desc column-description">
				<p><?php echo wp_kses_post( $description ); ?></p>
			</div>
		</div>
		<div class="plugin-card-bottom">
			<div class="vers column-rating">
				<?php echo sprintf( '<span class="plugin-status" >%s: %s</span>', esc_html__( 'Status', 'filebird' ), wp_kses_post( $pluginStatus ) ); ?>
			</div>
			<div class="column-updated">
				<?php
				if ( $actionLinks ) {
					echo '<ul class="plugin-action-buttons"><li>' . wp_kses_post( implode( '</li><li>', $actionLinks ) ) . '</li></ul>';
				}
				?>
			</div>
		</div>
	</div>
<?php } ?>

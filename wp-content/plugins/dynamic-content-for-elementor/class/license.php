<?php

namespace DynamicContentForElementor;

class License {

	public $license_key;

	public function __construct() {
		$this->init();
	}

	public function init() {
		$this->activation_advisor();

		// gestisco lo scaricamento dello zip aggiornato inviando i dati della licenza
		add_filter('upgrader_pre_download', array( $this, 'filter_upgrader_pre_download' ), 10, 3);
	}

	public function activation_advisor() {
		update_option(DCE_PRODUCT_ID . '_license_activated', 1);
		update_option(DCE_PRODUCT_ID . '_license_key', 'dce-aaaa-bbbb-cccc-dddd-eeee-ff');
		update_option(DCE_PRODUCT_ID . '_license_expiration', '2030.06.01');
		
		$license_activated = get_option(DCE_PRODUCT_ID . '_license_activated');
		$tab_license = ( isset($_GET['tab']) && $_GET['tab'] == 'license' ) ? true : false;
		if ( ! $license_activated && ! $tab_license ) {
			add_action('admin_notices', '\DynamicContentForElementor\Notice::dce_admin_notice__license');
			add_filter('plugin_action_links_' . DCE_PLUGIN_BASE, '\DynamicContentForElementor\License::dce_plugin_action_links_license');
		}
	}

	// define the upgrader_pre_download callback
	public function filter_upgrader_pre_download( $false, $package, $instance ) {
		// ottengo lo slug del plugin corrente
		$plugin = false;
		if ( property_exists($instance, 'skin') ) {
			if ( $instance->skin ) {
				if ( property_exists($instance->skin, 'plugin') ) {
					// aggiornamento da pagina
					if ( $instance->skin->plugin ) {
						$pezzi = explode('/', $instance->skin->plugin);
						$plugin = reset($pezzi);
					}
				}
				if ( ! $plugin && isset($instance->skin->plugin_info['TextDomain']) ) {
					// aggiornamento ajax
					$plugin = $instance->skin->plugin_info['TextDomain'];
				}
			}
		}
		// agisco solo per il mio plugin
		if ( $plugin == 'dynamic-content-for-elementor' || isset($_POST['dce_version']) ) {
			return $this->upgrader_pre_download($package, $instance);
		}
		return $false;
	}

	public function upgrader_pre_download( $package, $instance = null ) {
		// ora verifico la licenza per l'aggiornamento
		$license = self::call_api('status-check', DCE_LICENSE, false, true);
		if ( ! self::is_active($license) ) {
			if ( ! DCE_LICENSE ) {
				// l'utente non ha ancora impostato alcun codice di licenza
				return new \WP_Error('no_license', __('You have not entered the license.', 'dynamic-content-for-elementor') . ' <a target="_blank" href="https://www.dynamic.ooo/pricing">' . __('If you don’t have one already, you should buy one', 'dynamic-content-for-elementor') . '</a>');
			}
			// qualcosa è andato storto...stampo tutti gli errori
			if ( is_wp_error($license) || $license['response']['code'] != 200 ) {
				return new \WP_Error('no_license', __('Error connecting to the server.', 'dynamic-content-for-elementor') . ' -- KEY: ' . DCE_LICENSE . ' - DOMAIN: ' . DCE_INSTANCE . ' - STATUS-CHECK: ' . var_export($license_dump, true));
			}
			// oppure semplicemente la licenza utilizzata non è attiva o valida
			return new \WP_Error('no_license', __('Your license is not valid', 'dynamic-content-for-elementor') . ' <a href="' . admin_url() . 'admin.php?page=dce_opt&tab=license&licence_check=1">' . __('Check it on the plugin settings', 'dynamic-content-for-elementor') . '</a>.');
		}
		if ( self::is_expired($license) ) {
			// la licenza è scaduta
			return new \WP_Error('no_license', __('Your license is not valid for plugin updates, it is probably expired', 'dynamic-content-for-elementor') . ' <a href="' . admin_url() . 'admin.php?page=dce_opt&tab=license&licence_check=1">' . __('Check it on the plugin settings', 'dynamic-content-for-elementor') . '</a>.');
		}

		// aggiungo quindi le info aggiuntive della licenza alla richiesta per abilitarmi al download
		$package .= ( strpos($package, '?') === false ) ? '?' : '&';
		$package .= 'license_key=' . DCE_LICENSE . '&license_instance=' . DCE_INSTANCE;
		if ( get_option('dce_beta', false) ) {
			$package .= '&beta=true';
		}
		self::plugin_backup();
		$download_file = download_url($package);
		if ( is_wp_error($download_file) ) {
			return new \WP_Error('download_failed', __('Error downloading the update package', 'dynamic-content-for-elementor'), $download_file->get_error_message());
		}
		return $download_file;
	}

	public static function plugin_backup() {
		// do a zip of current version
		$dce_backup = ! get_option('dce_backup_disable');
		if ( $dce_backup ) {
			// create zip in /wp-content/backup
			if ( ! is_dir(DCE_BACKUP_PATH) ) {
				mkdir(DCE_BACKUP_PATH, 0755, true);
			}
			// Add to the directory an empty index.php
			if ( ! is_file(DCE_BACKUP_PATH . '/index.php') ) {
				$phpempty = "<?php\n//Silence is golden.\n";
				file_put_contents(DCE_BACKUP_PATH . '/index.php', $phpempty);
			}
			$outZipPath = DCE_BACKUP_PATH . '/' . 'dynamic-content-for-elementor' . '_' . DCE_VERSION . '.zip';
			if ( is_file($outZipPath) ) {
				unlink($outZipPath);
			}

			$options = array(
				'source_directory' => DCE_PATH,
				'zip_filename' => $outZipPath,
				'zip_foldername' => 'dynamic-content-for-elementor',
			);

			if ( extension_loaded('zip') ) {
				Helper::zip_folder($options);
			}
		}
	}

	public static function call_api( $action, $license_key, $iNotice = false, $debug = false ) {
		global $wp_version;
		$args = array(
			'woo_sl_action' => $action,
			'licence_key' => $license_key,
			'product_unique_id' => DCE_PRODUCT_ID,
			'domain' => DCE_INSTANCE,
			'api_version' => '1.1',
			'wp-version' => $wp_version,
			'version' => DCE_VERSION,
		);
		$request_uri = DCE_LICENSE_URL . '/api.php?' . http_build_query($args);
		$data = wp_remote_get($request_uri);

		if ( is_wp_error($data) ) {
			if ( $debug ) {
				return $data;
			}
			//there was a problem establishing a connection to the API server
			Notice::dce_admin_notice__server_error(__('Could not receive an answer from the License Server, please retry later or contact support.', 'dynamic-content-for-elementor'));
			return false;
		}

		if ( $data['response']['code'] != 200 ) {
			if ( $debug ) {
				return $data;
			}
			Notice::dce_admin_notice__server_error(__('There was an error contacting the License Server, please retry later or contact support.', 'dynamic-content-for-elementor'));
			return false;
		}

		$data_body = json_decode($data['body']);
		if ( is_array($data_body) ) {
			$data_body = reset($data_body);
		}
		if ( isset($data_body->status) ) {
			if ( $data_body->status == 'success' ) {
				if ( ( $action == 'status-check' && ( $data_body->status_code == 's200' || $data_body->status_code == 's205' ) ) ||
						( $action == 'activate' && ( $data_body->status_code == 's100' || $data_body->status_code == 's101' ) ) ||
						( $action == 'deactivate' && $data_body->status_code == 's201' ) ||
						( $action == 'plugin_update' && $data_body->status_code == 's401' ) ) {
					//the license is active and the software is active
					$message = $data_body->message;
					$expiration_date = self::get_expiration_date($data);
					if ( $expiration_date ) {
						$message .= '. <b>Expiration date:</b> ' . $expiration_date;
						if ( self::is_expired($data) ) {
							update_option('dce_beta', false);
						}
					}
					if ( $iNotice ) {
						Notice::dce_admin_notice__success($message);
					} else {
						add_option('dce_notice', $message);
						add_action('admin_notices', 'Notice::dce_admin_notice__success');
					}
					//doing further actions like saving the license and allow the plugin to run
					if ( $debug ) {
						return $data;
					}
					return true;
				} else {
					if ( $debug ) {
						return $data;
					}
					if ( $iNotice ) {
						Notice::dce_admin_notice__warning($data_body->message);
					} else {
						add_option('dce_notice', $data_body->message . ' - domain: ' . DCE_INSTANCE);
						add_action('admin_notices', 'Notice::dce_admin_notice__warning');
					}
					update_option('dce_beta', false);
					return false;
				}
			} else {
				if ( $debug ) {
					return $data;
				}
				//there was a problem activating the license
				if ( $iNotice ) {
					Notice::dce_admin_notice__warning($data_body->message);
				} else {
					add_option('dce_notice', $data_body->message . ' - domain: ' . DCE_INSTANCE);
					add_action('admin_notices', 'Notice::dce_admin_notice__warning');
				}
				return false;
			}
		} else {
			if ( $debug ) {
				return $data;
			}
			//there was a problem establishing a connection to the API server
			add_action('admin_notices', 'Notice::dce_admin_notice__server_error');
			return false;
		}
	}

	public static function is_active( $data ) {
		if ( ! is_wp_error($data) ) {
			if ( isset($data['body']) ) {
				$data_body = json_decode($data['body']);
				if ( is_array($data_body) ) {
					$data_body = reset($data_body);
				}
				if ( isset($data_body->status) ) {
					if ( $data_body->status == 'success' ) {
						if ( ( ( $data_body->status_code == 's200' || $data_body->status_code == 's205' ) ) ||
								( ( $data_body->status_code == 's100' || $data_body->status_code == 's101' ) ) ||
								( $data_body->status_code == 's201' ) ||
								( $data_body->status_code == 's401' ) ) {
							return true;
						}
					}
				}
			}
		}
		return false;
	}

	public static function get_expiration_date( $data ) {
		if ( ! is_wp_error($data) ) {
			if ( isset($data['body']) ) {
				$data_body = json_decode($data['body']);
				if ( is_array($data_body) ) {
					$data_body = reset($data_body);
				}
				if ( property_exists($data_body, 'licence_expire') ) {
					if ( $data_body->licence_expire ) {
						return $data_body->licence_expire;
					}
				}
			}
		}
		return false;
	}

	public static function is_expired( $data ) {
		$expiration_date = self::get_expiration_date($data);
		if ( $expiration_date ) {
			if ( $expiration_date < date('Y-m-d') ) {
				return true;
			}
		}
		return false;
	}

	public static function show_license_form() {

		$licence_key = DCE_LICENSE;
		if ( isset($_POST['licence_key']) ) {
			if ( intval($_POST['licence_status']) ) {
				$res = self::call_api('deactivate', $licence_key);
				if ( $res ) {
					update_option(DCE_PRODUCT_ID . '_license_activated', 0);
				}
			}
			$licence_key = sanitize_text_field( $_POST['licence_key'] );
			if ( DCE_LICENSE != $licence_key || ! intval($_POST['licence_status']) ) {
				// aggiorno la chiave di licenza inserita
				update_option(DCE_PRODUCT_ID . '_license_key', $licence_key);
				// provo ad attivare con la nuova chiave
				$res = self::call_api('activate', $licence_key, true);

				// mi salvo lo stato della licenza per non effettuare troppe chiamate al server
				if ( $res ) {
					update_option(DCE_PRODUCT_ID . '_license_activated', 1);
					update_option(DCE_PRODUCT_ID . '_license_domain', base64_encode(DCE_INSTANCE));
				} else {
					update_option(DCE_PRODUCT_ID . '_license_activated', 0);
					$licence_key = '';
				}
			}
		}

		if ( isset($_POST['beta_status']) ) {
			if ( isset($_POST['dce_beta']) ) {
				update_option('dce_beta', 1);
			} else {
				update_option('dce_beta', 0);
			}
		}

		if ( isset($_POST['backup_status']) ) {
			if ( isset($_POST['dce_backup_disable']) ) {
				update_option('dce_backup_disable', 0);
			} else {
				update_option('dce_backup_disable', 1);
			}
		}

		$licence_check = isset($_GET['licence_check']) ? sanitize_text_field($_GET['licence_check']) : false;
		$license_data = self::call_api('status-check', $licence_key, $licence_check, true);
		if ( $license_data ) {
			$expiration_date = self::get_expiration_date($license_data);
			update_option(DCE_PRODUCT_ID . '_license_expiration', $expiration_date);
		}
		$licence_status = ( $licence_key && self::is_active($license_data) );

		$licence_key_hidden = '';
		$licence_pieces = explode('-', $licence_key);
		if ( isset($licence_pieces[1]) && isset($licence_pieces[2]) ) {
			$licence_pieces[1] = $licence_pieces[2] = 'xxxxxxxx';
			$licence_key_hidden = implode('-', $licence_pieces);
		}

		$dce_domain = base64_decode(get_option(DCE_PRODUCT_ID . '_license_domain'));
		$dce_activated = intval(get_option(DCE_PRODUCT_ID . '_license_activated', 0));
		$classes = ( $licence_status ) ? 'dce-success dce-notice-success' : 'dce-error dce-notice-error';
		if ( $dce_activated && $licence_status && $dce_domain && $dce_domain != DCE_INSTANCE ) {
			$classes = 'dce-warning dce-notice-warning';
		}
		?>
		<div class="dce-notice <?php echo $classes; ?>">
			<h2>LICENSE Status <a href="?<?php echo sanitize_text_field( $_SERVER['QUERY_STRING'] ); ?>&licence_check=1"><span class="dashicons dashicons-info"></span></a></h2>
			<form action="" method="post">
				<?php _e('Your key', 'dynamic-content-for-elementor'); ?>: <input type="text" name="licence_key" value="<?php
				if ( $dce_activated ) {
					echo $licence_key_hidden;
				}
				?>" id="licence_key" placeholder="dce-xxxxxxxx-xxxxxxxx-xxxxxxxx" style="width: 240px; max-width: 100%;">
				<input type="hidden" name="licence_status" value="<?php echo $licence_status; ?>" id="licence_status">
			<?php ( $licence_status ) ? submit_button('Deactivate', 'cancel') : submit_button('Save Key and Activate'); ?>
			</form>
		<?php
		if ( $licence_status ) {
			if ( $dce_domain && $dce_domain != DCE_INSTANCE ) {
				?>
					<p><strong style="color:#f0ad4e;"><?php _e('Your license is valid but there is something wrong: <b>License Mismatch</b>.', 'dynamic-content-for-elementor'); ?></strong></p>
					<p><?php _e('Your license key doesn\'t match your current domain. This is most likely due to a change in the domain URL. Please deactivate the license and reactivate it', 'dynamic-content-for-elementor'); ?></p>
				<?php } else { ?>
					<p><strong style="color:#46b450;"><?php _e('Your license is valid and active.', 'dynamic-content-for-elementor'); ?></strong></p>
					<p><?php _e('Thank you for using our plugin.', 'dynamic-content-for-elementor'); ?><br><?php _e('Feel free to create your new dynamic and creative website.', 'dynamic-content-for-elementor'); ?><br><?php _e('If you think that our widgets are fantastic do not forget to recommend it to your friends.', 'dynamic-content-for-elementor'); ?></p>
				<?php
				}
		} else {
			?>
				<p><?php _e('Enter your license here to keep the plugin updated, obtaining new widgets, future compatibility, more stability and security.', 'dynamic-content-for-elementor'); ?></p>
				<p><?php _e('You still don’t have one? Get it now!', 'dynamic-content-for-elementor'); ?> <a href="http://www.dynamic.ooo" class="button button-small" target="_blank"><?php _e('Visit our official page', 'dynamic-content-for-elementor'); ?></a></p>
		<?php } ?>
		</div>

		<?php

		if ( $licence_status ) {
			$dce_beta = get_option('dce_beta');
			?>
			<div class="dce-notice dce-warning dce-notice-warning">
				<h3><?php _e('Beta release', 'dynamic-content-for-elementor'); ?></h3>
				<form action="" method="post">
					<label><input type="checkbox" name="dce_beta" value="beta"<?php if ( $dce_beta ) {
						?> checked="checked"<?php } ?>> <?php _e('Enable BETA releases (IMPORTANT: do NOT enable if you need a stable version).', 'dynamic-content-for-elementor'); ?></label>
					<input type="hidden" name="beta_status" value="1" id="beta_status">
			<?php submit_button('Save my preference'); ?>
				</form>
			</div>

			<?php
			if ( extension_loaded('zip') ) {
				$dce_backup = ! get_option('dce_backup_disable');
				?>
				<div class="dce-notice dce-<?php echo $dce_backup ? 'success' : 'error'; ?> dce-notice-<?php echo $dce_backup ? 'success' : 'error'; ?>">
					<h3><?php _e('Safe upgrade', 'dynamic-content-for-elementor'); ?></h3>
					<form action="" method="post">
						<label><input type="checkbox" name="dce_backup_disable" value="backup"<?php if ( $dce_backup ) {
							?> checked="checked"<?php } ?>> <?php _e('Perform a plugin Backup of the current version before the update action that allows easy Rollback.', 'dynamic-content-for-elementor'); ?></label>
						<input type="hidden" name="backup_status" value="1" id="backup_status">
				<?php submit_button('Save my preference'); ?>
					</form>
				</div>
				<?php
			}

			$rollback_versions = array( DCE_VERSION => DCE_VERSION );

			$backups = glob(DCE_BACKUP_PATH . '/' . 'dynamic-content-for-elementor' . '_*.zip');
			if ( ! empty($backups) ) {
				foreach ( $backups as $bak ) {
					list($pkg, $bak_version) = explode('_', str_replace('.zip', '', basename($bak)));
					$rollback_versions[ $bak_version ] = $bak_version;
				}
				?>
				<div class="dce-notice dce-error dce-notice-error">
					<h3><?php _e('RollBack version', 'dynamic-content-for-elementor'); ?></h3>
					<form action="" method="post">
						<h4><?php _e('Your current version', 'dynamic-content-for-elementor'); ?>: <?php echo DCE_VERSION; ?></h4>
						<p><?php echo sprintf(__( 'Experiencing an issue with Dynamic Content for Elementor version %s? Rollback to a previous version before the issue appeares.', 'dynamic-content-for-elementor' ), DCE_VERSION); ?>
						<label><?php _e('Select version', 'dynamic-content-for-elementor'); ?>:</label>
						<select name="dce_version" id="dce_version">
							<?php
							if ( ! empty($rollback_versions) ) {
								foreach ( $rollback_versions as $aversion ) { ?>
									<option value="<?php echo $aversion; ?>"><?php echo $aversion; ?></option>
									<?php
								}
							}
							?>
						</select>
						<?php submit_button('Rollback NOW'); ?>
					</form>
				</div>
				<?php
			}
		}
	}

	public static function do_rollback() {

		// rollback or reinstall
		if ( isset($_POST['dce_version']) && sanitize_text_field( $_POST['dce_version'] ) ) {
			if ( $_POST['dce_version'] == DCE_VERSION ) {
				// same version...so no change :)
				$rollback = true;
			} else {
				$backup = DCE_BACKUP_PATH . '/' . 'dynamic-content-for-elementor' . '_' . sanitize_file_name( $_POST['dce_version'] ) . '.zip';
				if ( is_file($backup) ) {
					// from local backup
					$roll_url = DCE_BACKUP_URL . '/' . 'dynamic-content-for-elementor' . '_' . sanitize_file_name( $_POST['dce_version'] ) . '.zip';

				} else {
					// from server
					$roll_url = DCE_LICENSE_URL . '/last.php?v=' . sanitize_text_field( $_POST['dce_version'] );
				}

				ob_start();
				$wp_upgrader_skin = new \DynamicContentForElementor\Upgrader_Skin();
				$wp_upgrader = new \WP_Upgrader($wp_upgrader_skin);
				$wp_upgrader->init();
				$rollback = $wp_upgrader->run(array(
					'package' => $roll_url,
					'destination' => DCE_PATH,
					'clear_destination' => true,
				));
				$roll_status = ob_get_clean();
			}
			if ( $rollback ) {
				exit(wp_redirect('admin.php?page=dce_info'));
			} else {
				die($roll_status);
			}
		}
	}

	public static function check_for_updates( $file ) {
		// Verify updates
		$info = self::check_for_updates_url();
		$myUpdateChecker = \Puc_v4_Factory::buildUpdateChecker(
			$info,
			$file,
			'dynamic-content-for-elementor'
		);
	}
	public static function check_for_updates_url() {
		// Verify updates
		$info = DCE_LICENSE_URL . '/info.php?s=' . DCE_INSTANCE . '&v=' . DCE_VERSION;
		if ( DCE_LICENSE ) {
			$info .= '&k=' . DCE_LICENSE;
		}
		if ( get_option('dce_beta', false) ) {
			$info .= '&beta=true';
		}
		return $info;
	}

	public static function dce_plugin_action_links_license( $links ) {
		$links['license'] = '<a style="color:brown;" title="Activate license" href="' . admin_url() . 'admin.php?page=dce_opt&tab=license"><b>' . __('License', 'dynamic-content-for-elementor') . '</b></a>';
		return $links;
	}

	public static function dce_active_domain_check() {
		$dce_activated = intval(get_option(DCE_PRODUCT_ID . '_license_activated', 0));
		$dce_domain = base64_decode(get_option(DCE_PRODUCT_ID . '_license_domain'));
		if ( $dce_activated && $dce_domain && $dce_domain != DCE_INSTANCE ) {
			Notice::dce_admin_notice__warning(__('<b>License Mismatch</b><br>Your license key doesn\'t match your current domain. This is most likely due to a change in the domain URL. Please deactivate the license and reactivate it again. <a class="btn button" href="' . admin_url() . 'admin.php?page=dce_opt&tab=license">Reactivate License</a>', 'dynamic-content-for-elementor'));
			return false;
		}
		return true;
	}

	public static function dce_expired_license_notice() {
		$dce_expiration_date = get_option(DCE_PRODUCT_ID . '_license_expiration');
		if ( $dce_expiration_date ) {
			if ( $dce_expiration_date < date('Y-m-d') ) {
				Notice::dce_admin_notice__danger(__('<b>your License Expired on ' . $dce_expiration_date . '</b><br>Please renew your license or you can\'t get more plugin updates. <a class="btn button" target="_blank" href="https://www.dynamic.ooo">Extend your license now</a>', 'dynamic-content-for-elementor'));
				return false;
			}
		}
		return true;
	}

}

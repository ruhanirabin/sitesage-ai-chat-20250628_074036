function ssac_add_missing_ai_files() {
	// Load WP_Filesystem if needed.
	if ( ! function_exists( 'WP_Filesystem' ) ) {
		require_once ABSPATH . 'wp-admin/includes/file.php';
	}
	WP_Filesystem();
	global $wp_filesystem;

	$ai_dir = plugin_dir_path( __FILE__ ) . 'ai-files/';

	// Create directory if it doesn't exist.
	if ( ! $wp_filesystem->is_dir( $ai_dir ) ) {
		if ( ! $wp_filesystem->mkdir( $ai_dir, FS_CHMOD_DIR ) ) {
			error_log(
				sprintf(
					/* translators: %s: directory path */
					__( 'SiteSage AI Chat: Could not create directory %s', 'sitesage-ai-chat' ),
					$ai_dir
				)
			);
			return;
		}
	}

	// Default prompt files and contents.
	$default_files = array(
		'default-system-prompt.json' => wp_json_encode(
			array(
				'role'    => 'system',
				'content' => __( 'You are a helpful assistant for SiteSage AI Chat.', 'sitesage-ai-chat' ),
			),
			JSON_PRETTY_PRINT
		),
		'default-user-prompt.json'   => wp_json_encode(
			array(
				'role'    => 'user',
				'content' => __( 'Hello! How can I use SiteSage AI Chat today?', 'sitesage-ai-chat' ),
			),
			JSON_PRETTY_PRINT
		),
	);

	// Write each file if it doesn't exist.
	foreach ( $default_files as $filename => $content ) {
		$file_path = $ai_dir . $filename;
		if ( ! $wp_filesystem->exists( $file_path ) ) {
			$written = $wp_filesystem->put_contents( $file_path, $content . "\n", FS_CHMOD_FILE );
			if ( false === $written ) {
				error_log(
					sprintf(
						/* translators: %s: file path */
						__( 'SiteSage AI Chat: Failed to write AI file: %s', 'sitesage-ai-chat' ),
						$file_path
					)
				);
			}
		}
	}
}

register_activation_hook( __FILE__, 'ssac_add_missing_ai_files' );
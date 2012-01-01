<?php


if ( ! class_exists( 'PluginBuddyZip' ) ) {
	class PluginBuddyZip {
		function PluginBuddyZip() {
			
		}
		
		function add_directory_to_zip( $file, $directory, $options = array(), $disable_compression = false ) {
			$default_options = array(
				'excludes'		=> array(),
				'remove_path'	=> '',
				'add_path'		=> '',
				'append'		=> true,
				'overwrite'		=> false,
				'compress'		=> true,
			);
			
			$options = array_merge( $default_options, $options );
			extract( $options );
			
			
			if ( file_exists( $file ) ) {
				if ( true !== $append ) {
					if ( false === $overwrite )
						return array( 'error' => 'File already exists' );
					else if ( false === unlink( $file ) )
						return array( 'error' => 'Unable to remove existing file' );
				}
			}

			// Default trying of compatibility mode. If native works then turn this off.
			$go_compatibility = true;
			if ( $options['force_compatibility'] != true ) {
				if ( true === ( $result = $this->_add_directory_native_zip( $file, $directory, $options, $disable_compression ) ) ) {
					// Native worked so no compatibility!
					$go_compatibility = false;
				}
			} else {
				$result['error'] = 'Forced compatibility mode set in BackupBuddy settings.';
			}			
			if ( $go_compatibility == true ) {
				echo "<p>Native zip function unavailable or failed: {$result['error']}</p>\n";
				echo "<p>Falling back to compatibility method. Note that this method is slower and cannot exclude directories.</p>\n";
				flush();
				$this->_add_directory_to_zip_compat( $file, $directory, $options, $disable_compression );
			}
			
			
			// Ensure that file exists
			if ( ! file_exists( $file ) )
				return array( 'error' => 'Failed to create zip file' );
			
			
			
			
			return true;
		}
		
		function _add_directory_native_zip( $file, $directory, $options, $disable_compression = false ) {
/*			$zip_options = $this->get_native_zip_options();
			
			// Sanity check to make sure that the native zip is present
			if ( ! isset( $zip_options['r'] ) || ! isset( $zip_options['q'] ) )
				return false;
			
			// If files/directories need to be excluded, ensure that the zip command supports it
			if ( ! empty( $excludes ) && ! isset( $zip_options['x'] ) )
				return false;
			
			// If the zip file needs to be appended to, ensure that the zip command supports it
			if ( ( true === $append ) && ! isset( $zip_options['g'] ) )
				return false;*/
			
			
			$command = 'zip -q';
			
			if ( false !== $disable_compression )
				$command .= ' -0';
			
			if ( file_exists( $file ) ) {
				if ( true === $options['append'] ) {
					$command .= ' -g';
					
/*					clearstatcache();
					$last_modified = filemtime( $file );
					
					if ( false === $last_modified )
						unset( $last_modified );*/
				}
				else
					unlink( $file );
			}
			
			$command .= " -r '$file' . -i '*'";
			
			if ( ! empty( $options['excludes'] ) ) {
				$command .= ' -x';
				
				foreach ( ( array ) $options['excludes'] as $exclude ) {
					$exclude = preg_replace( '|[/\\\\]$|', '', $exclude );
					
					if ( preg_match( '|[/\\\\]|', $exclude ) )
						$command .= " '$exclude*'";
					else
						$command .= " '$exclude'";
				}
				
				echo 'Excluding one or more directories based on BackupBuddy Settings ... <br /><br />';
			}
			//echo $command . '<br /><br >';
			if ( file_exists( ABSPATH . 'zip.exe' ) ) {
				echo 'Attempting to use provided zip.exe for native Windows zip functionality.<br /><br />';
				$command = str_replace( '\'', '"', $command ); // Windows wants double quotes
				$command = ABSPATH . $command;
				//echo $command;
			}
			chdir( $directory );
			// Need to suppress this since it gives major forking warnings in Windows.
			if ( stristr( PHP_OS, 'WIN' ) ) { // Suppress exec warnings on windows
				@exec( $command, $exec_return_a, $exec_return_b);
			} else { // Allow exec warnings on windows
				exec( $command, $exec_return_a, $exec_return_b);
			}
			
			if ( ( ! file_exists( $file ) ) || ( $exec_return_b == '-1' ) ) {
				if ( file_exists( $file ) )
					unlink( $file );
				
				return array( 'error' => 'Backup file couldn\'t be created without entering slower compatibility mode.' );
			}
			
/*			if ( isset( $last_modified ) ) {
				clearstatcache();
				
				if ( filemtime( $file ) <= $last_modified )
					return false;
			}*/
			
			return true;
		}
		
/*		function get_native_zip_options() {
			$output = array();
			$return = 0;
			
			exec( 'zip -so', $output, $return );
			
			$options = array();
			
			foreach ( (array) $output as $option ) {
				if ( preg_match( '/^\s(..)\s\s(.{18})\s...\s...\s(.+)$/', $option, $matches ) ) {
					$option = trim( $matches[1] );
					$long_option = trim( $matches[2] );
					$description = trim( $matches[3] );
					
					$options[$option] = array(
						'long_option'	=> $long_option,
						'description'	=> $description,
					);
				}
			}
			
			return $options;
		}*/
		
		function _add_directory_to_zip_compat( $file, $directory, $options, $disable_compression ) {
			$default_options = array(
				'remove_path'	=> '',
				'add_path'		=> '',
			);
			

			
			require_once( ABSPATH . 'wp-admin/includes/class-pclzip.php' );
			
			$archive = new PclZip( $file );
	
			//unset( $options['add_path'] );
			
			//$arguments = array( $directory );

			//$options = $this->_remap_options( $options );
			
			//$arguments = array_merge( $arguments, $options );
			
			
			/*
			echo '<pre>';
			print_r( $arguments );
			echo '</pre>';
			*/
			
			if ( isset( $disable_compression ) && ( $disable_compression == '1' ) ) {
				//$options['no_compress'] = '';
				$arguments = array( $directory, PCLZIP_OPT_NO_COMPRESSION, PCLZIP_OPT_REMOVE_PATH, $directory);
			} else {
				$arguments = array( $directory, PCLZIP_OPT_REMOVE_PATH, $directory);
			}
			
			if ( file_exists( $file ) )
				$result = call_user_func_array( array( &$archive, 'add' ), $arguments );
			else
				$result = call_user_func_array( array( &$archive, 'create' ), $arguments );
			
			
			if ( 0 == $result )
				return false;
			
			return true;
		}
		
		function _rename_zip_path( $file, $original_path, $replacement_path ) {
			$original_path = preg_replace( '|^[/\\\\]+|', '', $original_path );
			$original_path = preg_replace( '|([/\\\\])+$|', '', $original_path );
			$original_path .= '/';
			
			$replacement_path = preg_replace( '|^[/\\\\]+|', '', $replacement_path );
			$replacement_path = preg_replace( '|[/\\\\]+$|', '', $replacement_path );
			
			if ( ! empty( $replacement_path ) )
				$replacement_path .= '/';
			
			$zip = new ZipArchive();
			
echo $file;
			$open_status = $zip->open( $file );
			
			if ( true !== $open_status ) {
//				return array( 'error' => $this->get_zip_archive_error_message( $open_status ) );
				return array( 'error' => 'Unable to rename path' );
			}
			
			$status = array(
				'removed'	=> 0,
				'renamed'	=> 0,
			);
			
			for ( $index = 0; $index < $zip->numFiles; $index++ ) {
				$name = $zip->getNameIndex( $index );
				
				if ( $name === $original_path ) {
					// Remove original container directory
					$zip->deleteIndex( $index );
					
					$status['removed']++;
				}
				else if ( preg_match( '/^' . preg_quote( $original_path, '/' ) . '(.+)/', $name, $matches ) ) {
					// Rename paths
					$zip->renameIndex( $index, "$replacement_path{$matches[1]}" );
					
					$status['renamed']++;
				}
			}
			
			$zip->close();
			
			
			return $status;
		}
		
		function get_zip_archive_error_message( $status ) {
			$statuses = array(
				'ZIPARCHIVE::ER_CHANGED'		=> 'Entry has been changed',
				'ZIPARCHIVE::ER_CLOSE'			=> 'Closing zip archive failed',
				'ZIPARCHIVE::ER_COMPNOTSUPP'	=> 'Compression method not supported',
				'ZIPARCHIVE::ER_CRC'			=> 'CRC error',
				'ZIPARCHIVE::ER_DELETED'		=> 'Entry has been deleted',
				'ZIPARCHIVE::ER_EOF'			=> 'Premature end of file',
				'ZIPARCHIVE::ER_EXISTS'			=> 'File already exists',
				'ZIPARCHIVE::ER_INCONS'			=> 'Requested file has inconsistent data',
				'ZIPARCHIVE::ER_INTERNAL'		=> 'Internal error',
				'ZIPARCHIVE::ER_INVAL'			=> 'Invalid argument',
				'ZIPARCHIVE::ER_MEMORY'			=> 'Unable to allocate enough memory',
				'ZIPARCHIVE::ER_MULTIDISK'		=> 'Unable to read multi-disk zip archives',
				'ZIPARCHIVE::ER_NOENT'			=> 'Requested file does not exist',
				'ZIPARCHIVE::ER_NOZIP'			=> 'Requested file is not a zip archive',
				'ZIPARCHIVE::ER_OPEN'			=> 'Unable to open requested file',
				'ZIPARCHIVE::ER_READ'			=> 'Read error',
				'ZIPARCHIVE::ER_REMOVE'			=> 'Unable to remove file',
				'ZIPARCHIVE::ER_RENAME'			=> 'Renaming temporary file failed',
				'ZIPARCHIVE::ER_SEEK'			=> 'Seek error',
				'ZIPARCHIVE::ER_TMPOPEN'		=> 'Unable to create a temporary file',
				'ZIPARCHIVE::ER_WRITE'			=> 'Write error',
				'ZIPARCHIVE::ER_ZIPCLOSED'		=> 'Containing zip archived closed',
				'ZIPARCHIVE::ER_ZLIB'			=> 'ZLIB error',
			);
			
			foreach ( (array) $statuses as $constant => $message ) {
				if ( $status == constant( $constant ) )
					return $message;
			}
			
			return 'Unknown error';
		}
		
		function _flatten_associative_array( $array ) {
			if ( ! $this->_is_associative_array( $array ) )
				return $array;
			
			$flat_array = array();
			
			array_walk( $array, array( &$this, '_flatten_associative_array_walk' ), &$flat_array );
			
			return $flat_array;
		}
		
		function _flatten_associative_array_walk( $value, $key, &$array ) {
			$array[] = $key;
			$array[] = $value;
		}
		
		function _is_associative_array( $array ) {
			return is_array( $array ) && ( array_keys( $array ) !== range( 0, count( $array ) - 1 ) );
		}
		
		function _remap_options( $options ) {
			$translate_options = array(
				'add_path'					=> PCLZIP_OPT_ADD_PATH,			// Path prefix added to archive files
				'remove_path'				=> PCLZIP_OPT_REMOVE_PATH,		// Path prefix removed from archive files
				'no_compress'				=> PCLZIP_OPT_NO_COMPRESSION	// TODO: This is causing backup to fail so disabled.
			);
			
			$new_options = array();
			
			foreach ( (array) $options as $name => $val ) {
				if ( isset( $translate_options[$name] ) )
					$new_options[$translate_options[$name]] = $val;
			}
			
			$new_options = $this->_flatten_associative_array( $new_options );
			
			
			return $new_options;
		}
	}
	
	new PluginBuddyZip();
}

/*
$options = array(
	'remove_path'	=> '/opt/lampp/htdocs/latest',
	'add_path'		=> 'www',
	'excludes'		=> '/opt/lampp/htdocs/latest/backupbuddy_backups/',
	'overwrite'		=> true,
	'append'		=> false,
);

PluginBuddyZip::add_directory_to_zip( dirname( __FILE__ ) . '/test.zip', '/opt/lampp/htdocs/latest', $options );
*/

?>

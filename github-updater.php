<?php

class GitHub_Plugin_Updater {
    private $slug;
    private $plugin_file;
    private $github_user;
    private $github_repo;
    private $github_api_url;

    public function __construct($plugin_file, $github_user, $github_repo) {
        $this->plugin_file = $plugin_file;
        $this->slug = plugin_basename($plugin_file);
        $this->github_user = $github_user;
        $this->github_repo = $github_repo;
        $this->github_api_url = "https://api.github.com/repos/{$github_user}/{$github_repo}/releases/latest";

        add_filter('pre_set_site_transient_update_plugins', [$this, 'check_for_update']);
        add_filter('plugins_api', [$this, 'plugin_info'], 10, 3);
        add_filter('upgrader_package_options', [$this, 'modify_zip_download'], 10, 1);
    }

    /**
     * Check GitHub for updates
     */
public function check_for_update($transient) {
    if (empty($transient->checked)) {
        return $transient;
    }

    // Get plugin data
    $plugin_data = get_plugin_data($this->plugin_file);
    $current_version = $plugin_data['Version'];

    // Get latest release from GitHub
    $response = wp_remote_get($this->github_api_url, ['headers' => ['User-Agent' => 'WordPress']]);
    if (is_wp_error($response)) {
        error_log('GitHub API request failed: ' . $response->get_error_message());
        return $transient;
    }

    // Log the response from GitHub for debugging
    $release_data = json_decode(wp_remote_retrieve_body($response));
    if (!$release_data || !isset($release_data->tag_name)) {
        error_log('GitHub API response invalid or missing tag_name');
        return $transient;
    }

    // Log the received version from GitHub
    error_log('GitHub latest release version: ' . $release_data->tag_name);

    $new_version = $release_data->tag_name;  // Get the tag name from GitHub
    $new_version = str_replace('v', '', $new_version);  // Remove 'v' if present

    // Log the version comparison
    error_log('Current plugin version: ' . $current_version);
    error_log('New version after comparison: ' . $new_version);

	$response = wp_remote_get($this->github_api_url, ['headers' => ['User-Agent' => 'WordPress']]);
	if (is_wp_error($response)) {
		error_log('GitHub API request failed: ' . $response->get_error_message());
		return $transient;
	}

	$release_data = json_decode(wp_remote_retrieve_body($response));
	if (!$release_data || !isset($release_data->tag_name)) {
		error_log('GitHub API response invalid or missing tag_name');
		return $transient;
	}



	if (version_compare($current_version, $new_version, '<')) {
		error_log('Update found: ' . $new_version); // Add some debugging to ensure this block is triggered
		$transient->response[$this->slug] = (object) [
			'slug'        => $this->slug,
			'new_version' => $new_version,
			'package'     => $release_data->assets[0]->browser_download_url ?? null,
			'url'         => $release_data->html_url,
		];
	}


    return $transient;
}


    /**
     * Provide plugin details in the update popup
     */
public function plugin_info($false, $action, $args) {
    if ($action !== 'plugin_information' || $args->slug !== $this->slug) {
        return $false;
    }

    $response = wp_remote_get($this->github_api_url, ['headers' => ['User-Agent' => 'WordPress']]);
    if (is_wp_error($response)) {
        error_log('GitHub API request failed for plugin info: ' . $response->get_error_message());
        return $false;
    }

    // Log the response from GitHub for debugging
    $release_data = json_decode(wp_remote_retrieve_body($response));
    if (!$release_data || !isset($release_data->tag_name)) {
        error_log('GitHub API response invalid or missing tag_name for plugin info');
        return $false;
    }

    // Log the received version from GitHub for plugin info
    error_log('GitHub latest release version for plugin info: ' . $release_data->tag_name);

    return (object) [
        'name'          => $this->slug,
        'slug'          => $this->slug,
        'version'       => $release_data->tag_name,
        'author'        => 'Your Name',
        'homepage'      => $release_data->html_url,
        'sections'      => ['description' => $release_data->body],
        'download_link' => $release_data->assets[0]->browser_download_url ?? null,
    ];
}

    /**
     * Modify the package URL to point to the correct GitHub zip file
     */
    public function modify_zip_download($options) {
        if (!empty($options['hook_extra']['plugin']) && $options['hook_extra']['plugin'] === $this->slug) {
            $response = wp_remote_get($this->github_api_url, ['headers' => ['User-Agent' => 'WordPress']]);
            if (!is_wp_error($response)) {
                $release_data = json_decode(wp_remote_retrieve_body($response));
                if ($release_data && isset($release_data->assets[0]->browser_download_url)) {
                    $options['package'] = $release_data->assets[0]->browser_download_url;
                }
            }
        }
        return $options;
    }
}


?>
<?php
namespace Static_Maker;

if (!defined('ABSPATH')) {
    exit;
}

class Queue
{

    protected static $table_name = 'staticmaker_queues';

    protected static $columns = array();
    protected $data = array();

    public static function table_name()
    {
        global $wpdb;
        return $wpdb->prefix . self::$table_name;
    }

    public static function create_table()
    {
        global $wpdb;

        $table_name = self::table_name();
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE $table_name (
          id int(20) NOT NULL AUTO_INCREMENT,
          post_id int(20),
          post_type varchar(255) NOT NULL,
          type varchar(20) NOT NULL,
          url varchar(255) DEFAULT '' NOT NULL,
          status varchar(20) DEFAULT 'waiting' NOT NULL,
          created datetime,
          process_started datetime,
          process_ended datetime,
          PRIMARY KEY (id)
        ) $charset_collate";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta($sql);
    }

    public static function receive_unprocessed_queues()
    {
        global $wpdb;
        $table_name = self::table_name();

        $options = get_option(PLUGIN_NAME);
        $limit = isset($options['queue_limit']) && !empty($options['queue_limit']) ? $options['queue_limit'] : '10';

        $sql = $wpdb->prepare("SELECT * FROM $table_name WHERE status = 'waiting' LIMIT %d", $limit);
        $queues = $wpdb->get_results($sql);
        $instances = array();

        foreach ($queues as $queue) {
            $instances[] = new self($queue);
        }

        return $instances;
    }

    public static function get_queue_count()
    {
        global $wpdb;
        $table_name = self::table_name();
        return $wpdb->get_var("SELECT COUNT(*) FROM $table_name");
    }

    public static function get_queues($args = array())
    {
        global $wpdb;
        $each_page = 25;
        $table_name = self::table_name();

        $query = "SELECT * FROM $table_name";

        if (isset($args['desc']) && $args['desc']) {
            $query .= ' ORDER BY id DESC';
        }

        if (isset($args['paged']) && $args['paged']) {
            $paged = $args['paged'];
        } else {
            $paged = 1;
        }

        $q = $wpdb->prepare($query . ' LIMIT %d OFFSET %d', $each_page, ($paged - 1) * $each_page);
        $queues = $wpdb->get_results($q);

        if (isset($args['output']) && $args['output'] === 'original') {
            return $queues;
        }

        $instances = array();
        foreach ($queues as $queue) {
            $instances[] = new self($queue);
        }

        return $instances;
    }

    public static function enqueue_by_id($id, $action = 'add', $parent = false)
    {
        global $wpdb;
        $table_name = self::table_name();
        $page_table_name = Page::table_name();
        $page = Page::get_page($id);
        $post_id = $page->post_id;
        $results = array();
        $url = preg_replace('/__trashed(\/?)$/', '$1', $page->permalink);

        // Process specified static maker id
        $query = $wpdb->prepare("SELECT EXISTS(SELECT * FROM $page_table_name WHERE id=%d AND active=1)", $id);
        if ($wpdb->get_var($query) !== '1') {return false;}

        // Check queue duplication
        if ($post_id) {
            $query = $wpdb->prepare("SELECT EXISTS(SELECT * FROM $table_name WHERE post_id=%d AND type=\"%s\" AND status=\"waiting\")", $post_id, $action);
        } else {
            $query = $wpdb->prepare("SELECT EXISTS(SELECT * FROM $table_name WHERE url=\"%s\" AND type=\"%s\" AND status=\"waiting\")", $url, $action);
        }
        if ($wpdb->get_var($query)) {
            $results[] = false;
            return $results;
        }

        $results[] = $wpdb->insert(
            $table_name,
            array(
                'post_id' => $post_id,
                'created' => current_time('mysql'),
                'type' => $action,
                'post_type' => $page->post_type,
                'url' => $url,
            )
        );

        // Process related if it has
        $post_ids = array();
        if (!empty($post_id) && $parent) {
            $post_ids = Page::get_related_pages($post_id);
        }
        foreach ($post_ids as $post_id) {
            $post = get_post($post_id);

            $query = $wpdb->prepare("SELECT EXISTS(SELECT * FROM $page_table_name WHERE post_id=%d AND active=1)", $post_id);
            if ($wpdb->get_var($query) !== '1') {continue;}

            // Check queue duplication
            $query = $wpdb->prepare("SELECT EXISTS(SELECT * FROM $table_name WHERE post_id=%d AND type=\"%s\" AND status=\"waiting\")", $post_id, $action);
            if ($wpdb->get_var($query)) {
                continue;
            }

            $url = preg_replace('/__trashed(\/?)$/', '$1', get_permalink($post_id));
            $results[] = $wpdb->insert(
                $table_name,
                array(
                    'post_id' => $post_id,
                    'created' => current_time('mysql'),
                    'type' => 'add',
                    'post_type' => $post->post_type,
                    'url' => $url,
                )
            );
        }

        return $results;
    }

    public static function enqueue_by_post_id($post_id, $action = 'add', $parent = false)
    {
        global $wpdb;
        $table_name = self::table_name();
        $page_table_name = Page::table_name();

        $post_ids = array($post_id);

        $results = array();
        $post = get_post($post_id, ARRAY_A);

        $query = $wpdb->prepare("SELECT EXISTS(SELECT * FROM $page_table_name WHERE post_id=%d AND active=1)", $post_id);
        if ($wpdb->get_var($query) !== '1') {return [false];}

        // Check queue duplication
        $query = $wpdb->prepare("SELECT EXISTS(SELECT * FROM $table_name WHERE post_id=%d AND type=\"%s\" AND status=\"waiting\")", $post_id, $action);
        if ($wpdb->get_var($query)) {
            return [false];
        }

        $url = preg_replace('/__trashed(\/?)$/', '$1', get_permalink($post_id));
        $lang_details = apply_filters('wpml_post_language_details', null, $post['ID']);
        $lang_code = !is_wp_error($lang_details) ? $lang_details['language_code'] : '';
        $url = apply_filters('wpml_permalink', $url, $lang_code);
        $url = substr($url, -1) === '/' ? $url : $url . '/';

        $results[] = $wpdb->insert(
            $table_name,
            array(
                'post_id' => $post_id,
                'created' => current_time('mysql'),
                'type' => $action,
                'post_type' => $post['post_type'],
                'url' => $url,
            )
        );

        // enqueue related posts or linkk if exist
        if ($parent) {
            $posts = Page::get_related_pages($post_id);
            foreach ($posts as $post) {
                switch ($post['type']) {
                    case 'post_id':
                        $results = array_merge($results, self::enqueue_by_post_id($post['data'], 'add', false));
                        break;
                    case 'url':
                        $results[] = self::enqueue_by_link($post['data'], 'add', '', true);
                        break;
                }
            }
        }

        return $results;
    }

    public static function enqueue_by_link($link, $action = 'add', $post_type = '', $without_managed = false)
    {
        global $wpdb;
        $table_name = self::table_name();
        $page_table_name = Page::table_name();

        $link = preg_replace('/__trashed(\/?)$/', '$1', $link);
        $link = substr($link, -1) === '/' ? $link : $link . '/';

        // append get_home_url() result if the url is not started with http
        if (substr($link, 0, 4) !== 'http') {
            $link = get_home_url() . $link;
        }

        if (!$without_managed) {
            $query = $wpdb->prepare("SELECT EXISTS(SELECT * FROM $page_table_name WHERE permalink=%s AND active=1)", $link);
            if ($wpdb->get_var($query) !== '1') {return false;}
        }

        // Check queue duplication
        $query = $wpdb->prepare("SELECT EXISTS(SELECT * FROM $table_name WHERE url=\"%s\" AND type=\"%s\" AND status=\"waiting\")", $link, $action);
        if ($wpdb->get_var($query)) {return false;}

        return $wpdb->insert(
            $table_name,
            array(
                'post_id' => '',
                'created' => current_time('mysql'),
                'type' => $action,
                'post_type' => $post_type,
                'url' => $link,
            )
        );
    }

    public function __construct($columns)
    {
        foreach ($columns as $key => $value) {
            $this->$key = $value;
        }
    }

    public function __get($field_name)
    {
        if (!array_key_exists($field_name, $this->data)) {
            throw new \Exception('Undefined variable for ' . get_called_class());
        } else {
            return $this->data[$field_name];
        }
    }

    public function __set($field_name, $field_value)
    {
        return $this->data[$field_name] = $field_value;
    }

    /**
     * Replace the 'NULL' string with NULL
     *
     * @param  string $query
     * @return string $query
     */
    public function wp_db_null_value($query)
    {
        return str_ireplace("'NULL'", "NULL", $query);
    }

    public function save()
    {
        global $wpdb;
        $table_name = self::table_name();
        $id = $this->data['id'];

        $exists_query = $wpdb->prepare("SELECT EXISTS(SELECT * FROM $table_name WHERE id = %d)", $id);

        if ($wpdb->get_var($exists_query) === '1') { // have
            // workaround for NULL value... (NULL will be '' automatically...)
            add_filter('query', array($this, 'wp_db_null_value'));

            $this->data['process_ended'] = $this->data['process_ended'] ?: 'NULL';

            $result = $wpdb->update($table_name, $this->data, array('id' => $id));

            remove_filter('query', array($this, 'wp_db_null_value'));

            return $result;
        } else {
            return $wpdb->insert($table_name, $this->data);
        }
    }

    public function dequeue()
    {
        if ($this->data['type'] === 'add') {
            if (FileUtil::export_single_file($this->data['url']) !== false) {
                if (WP_DEBUG) {
                    LogUtil::write_with_trace('ID: ' . $this->id . ', Post ID ' . $this->post_id . ': ' . 'URL: ' . $this->url . ', Desc: Success Add Type');
                }
                $this->mark_as_completed();
            } else {
                if (WP_DEBUG) {
                    LogUtil::write_with_trace('ID: ' . $this->id . ', Post ID ' . $this->post_id . ': ' . 'URL: ' . $this->url . ', Desc: Failed Add Type');
                }
                $this->mark_as_failed();
            }
        } else if ($this->data['type'] === 'remove') {
            if (FileUtil::remove_single_file($this->data['url']) !== false) {
                $this->mark_as_completed();

                Page::get_page_by_link($this->data['url'])->delete();

                if (WP_DEBUG) {
                    LogUtil::write_with_trace('ID: ' . $this->id . ', Post ID ' . $this->post_id . ': ' . 'URL: ' . $this->url . ', Desc: Success Remove Type');
                }
            } else {
                if (WP_DEBUG) {
                    LogUtil::write_with_trace('ID: ' . $this->id . ', Post ID ' . $this->post_id . ': ' . 'URL: ' . $this->url . ', Desc: Failed Remove Type');
                }
                $this->mark_as_failed();
            }
        } else {
            $this->mark_as_skipped();
        }
    }

    public function mark_as_processing()
    {
        $this->data['status'] = 'processing';
        $this->data['process_started'] = current_time('mysql');
        $this->save();
    }

    public function mark_as_completed()
    {
        $this->data['status'] = 'completed';
        $this->data['process_started'] = current_time('mysql');
        $this->save();
    }

    public function mark_as_failed()
    {
        $this->data['status'] = 'failed';
        $this->data['process_started'] = current_time('mysql');
        $this->save();
    }

    public function mark_as_skipped()
    {
        $this->data['status'] = 'skipped (unknown)';
        $this->data['process_started'] = current_time('mysql');
        $this->save();
    }

}

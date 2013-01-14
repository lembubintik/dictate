<?php namespace Dictate\String;

define('MB_STRING', (int) function_exists('mb_get_info'));

class String {

    /**
     * Illuminate application instance.
     *
     * @var Illuminate\Foundation\Application
     */
    protected $app;

    /**
     * The pluralizer instance.
     *
     * @var Pluralizer
     */
    public $pluralizerInstance;

    /**
     * Cache application encoding locally to save expensive calls to $this->app->get().
     *
     * @var string
     */
    public $encoding = null;

    public function __construct($app)
    {
        $this->app = $app;
    }

    /**
     * Get the appliction.encoding without needing to request it from $this->app->get() each time.
     *
     * @return string
     */
    protected function encoding()
    {
        return $this->encoding ?: $this->encoding = $this->app['config']->get('string::encoding');
    }

    /**
     * Get the length of a string.
     *
     * <code>
     *      // Get the length of a string
     *      $length = Str::length('Taylor Otwell');
     *
     *      // Get the length of a multi-byte string
     *      $length = Str::length('Τάχιστη')
     * </code>
     *
     * @param  string  $value
     * @return int
     */
    public function length($value)
    {
        return (MB_STRING) ? mb_strlen($value, $this->encoding()) : strlen($value);
    }

    /**
     * Convert a string to lowercase.
     *
     * <code>
     *      // Convert a string to lowercase
     *      $lower = Str::lower('Taylor Otwell');
     *
     *      // Convert a multi-byte string to lowercase
     *      $lower = Str::lower('Τάχιστη');
     * </code>
     *
     * @param  string  $value
     * @return string
     */
    public function lower($value)
    {
        return (MB_STRING) ? mb_strtolower($value, $this->encoding()) : strtolower($value);
    }

    /**
     * Convert a string to uppercase.
     *
     * <code>
     *      // Convert a string to uppercase
     *      $upper = Str::upper('Taylor Otwell');
     *
     *      // Convert a multi-byte string to uppercase
     *      $upper = Str::upper('Τάχιστη');
     * </code>
     *
     * @param  string  $value
     * @return string
     */
    public function upper($value)
    {
        return (MB_STRING) ? mb_strtoupper($value, $this->encoding()) : strtoupper($value);
    }

    /**
     * Convert a string to title case (ucwords equivalent).
     *
     * <code>
     *      // Convert a string to title case
     *      $title = Str::title('taylor otwell');
     *
     *      // Convert a multi-byte string to title case
     *      $title = Str::title('νωθρού κυνός');
     * </code>
     *
     * @param  string  $value
     * @return string
     */
    public function title($value)
    {
        if (MB_STRING)
        {
            return mb_convert_case($value, MB_CASE_TITLE, $this->encoding());
        }

        return ucwords(strtolower($value));
    }

    /**
     * Limit the number of characters in a string.
     *
     * <code>
     *      // Returns "Tay..."
     *      echo Str::limit('Taylor Otwell', 3);
     *
     *      // Limit the number of characters and append a custom ending
     *      echo Str::limit('Taylor Otwell', 3, '---');
     * </code>
     *
     * @param  string  $value
     * @param  int     $limit
     * @param  string  $end
     * @return string
     */
    public function limit($value, $limit = 100, $end = '...')
    {
        if ($this->length($value) <= $limit) return $value;

        if (MB_STRING)
        {
            return mb_substr($value, 0, $limit, $this->encoding()).$end;
        }

        return substr($value, 0, $limit).$end;
    }

    /**
     * Limit the number of chracters in a string including custom ending
     *
     * <code>
     *      // Returns "Taylor..."
     *      echo Str::limit_exact('Taylor Otwell', 9);
     *
     *      // Limit the number of characters and append a custom ending
     *      echo Str::limit_exact('Taylor Otwell', 9, '---');
     * </code>
     *
     * @param  string  $value
     * @param  int     $limit
     * @param  string  $end
     * @return string
     */
    public function limit_exact($value, $limit = 100, $end = '...')
    {
        if ($this->length($value) <= $limit) return $value;

        $limit -= $this->length($end);

        return $this->limit($value, $limit, $end);
    }

    /**
     * Limit the number of words in a string.
     *
     * <code>
     *      // Returns "This is a..."
     *      echo Str::words('This is a sentence.', 3);
     *
     *      // Limit the number of words and append a custom ending
     *      echo Str::words('This is a sentence.', 3, '---');
     * </code>
     *
     * @param  string  $value
     * @param  int     $words
     * @param  string  $end
     * @return string
     */
    public function words($value, $words = 100, $end = '...')
    {
        if (trim($value) == '') return '';

        preg_match('/^\s*+(?:\S++\s*+){1,'.$words.'}/u', $value, $matches);

        if ($this->length($value) == $this->length($matches[0]))
        {
            $end = '';
        }

        return rtrim($matches[0]).$end;
    }

    /**
     * Get the singular form of the given word.
     *
     * @param  string  $value
     * @return string
     */
    public function singular($value)
    {
        return $this->pluralizer()->singular($value);
    }

    /**
     * Get the plural form of the given word.
     *
     * <code>
     *      // Returns the plural form of "child"
     *      $plural = Str::plural('child', 10);
     *
     *      // Returns the singular form of "octocat" since count is one
     *      $plural = Str::plural('octocat', 1);
     * </code>
     *
     * @param  string  $value
     * @param  int     $count
     * @return string
     */
    public function plural($value, $count = 2)
    {
        return $this->pluralizer()->plural($value, $count);
    }

    /**
     * Get the pluralizer instance.
     *
     * @return Pluralizer
     */
    protected function pluralizer()
    {
        $config = $this->app['config']->get('string::strings');

        return $this->pluralizerInstance ?: $this->pluralizerInstance = new Pluralizer($config);
    }

    /**
     * Generate a URL friendly "slug" from a given string.
     *
     * <code>
     *      // Returns "this-is-my-blog-post"
     *      $slug = Str::slug('This is my blog post!');
     *
     *      // Returns "this_is_my_blog_post"
     *      $slug = Str::slug('This is my blog post!', '_');
     * </code>
     *
     * @param  string  $title
     * @param  string  $separator
     * @return string
     */
    public function slug($title, $separator = '-')
    {
        $title = $this->ascii($title);

        // Remove all characters that are not the separator, letters, numbers, or whitespace.
        $title = preg_replace('![^'.preg_quote($separator).'\pL\pN\s]+!u', '', $this->lower($title));

        // Replace all separator characters and whitespace by a single separator
        $title = preg_replace('!['.preg_quote($separator).'\s]+!u', $separator, $title);

        return trim($title, $separator);
    }

    /**
     * Convert a string to 7-bit ASCII.
     *
     * This is helpful for converting UTF-8 strings for usage in URLs, etc.
     *
     * @param  string  $value
     * @return string
     */
    public function ascii($value)
    {
        $foreign = $this->app->get('string::ascii');

        $value = preg_replace(array_keys($foreign), array_values($foreign), $value);

        return preg_replace('/[^\x09\x0A\x0D\x20-\x7E]/', '', $value);
    }

    /**
     * Convert a string to an underscored, camel-cased class name.
     *
     * This method is primarily used to format task and controller names.
     *
     * <code>
     *      // Returns "Task_Name"
     *      $class = Str::classify('task_name');
     *
     *      // Returns "Taylor_Otwell"
     *      $class = Str::classify('taylor otwell')
     * </code>
     *
     * @param  string  $value
     * @return string
     */
    public function classify($value)
    {
        $search = array('_', '-', '.', '/');

        return str_replace(' ', '_', $this->title(str_replace($search, ' ', $value)));
    }

    /**
     * Return the "URI" style segments in a given string.
     *
     * @param  string  $value
     * @return array
     */
    public function segments($value)
    {
        return array_diff(explode('/', trim($value, '/')), array(''));
    }

    /**
     * Generate a random alpha or alpha-numeric string.
     *
     * <code>
     *      // Generate a 40 character random alpha-numeric string
     *      echo Str::random(40);
     *
     *      // Generate a 16 character random alphabetic string
     *      echo Str::random(16, 'alpha');
     * <code>
     *
     * @param  int     $length
     * @param  string  $type
     * @return string
     */
    public function random($length, $type = 'alnum')
    {
        return substr(str_shuffle(str_repeat($this->pool($type), 5)), 0, $length);
    }

    /**
     * Get the character pool for a given type of random string.
     *
     * @param  string  $type
     * @return string
     */
    protected function pool($type)
    {
        switch ($type)
        {
            case 'alpha':
                return 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

            case 'alnum':
                return '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

            default:
                throw new \Exception("Invalid random string type [$type].");
        }
    }

}
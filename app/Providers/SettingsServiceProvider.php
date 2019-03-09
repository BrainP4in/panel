<?php

namespace Pterodactyl\Providers;

use DB;
use Psr\Log\LoggerInterface as Log;
use Illuminate\Database\QueryException;
use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Encryption\Encrypter;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Contracts\Config\Repository as ConfigRepository;
use Pterodactyl\Contracts\Repository\SettingsRepositoryInterface;

class SettingsServiceProvider extends ServiceProvider
{
    /**
     * @var \Illuminate\Contracts\Config\Repository
     */
    protected $config;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $log;

    /**
     * @var \Pterodactyl\Contracts\Repository\SettingsRepositoryInterface
     */
    protected $settings;

    /**
     * An array of configuration keys to override with database values
     * if they exist.
     *
     * @var array
     */
    public $keys = [
        'app:name',
        'app:locale',
        'recaptcha:enabled',
        'recaptcha:secret_key',
        'recaptcha:website_key',
        'pterodactyl:guzzle:timeout',
        'pterodactyl:guzzle:connect_timeout',
        'pterodactyl:console:count',
        'pterodactyl:console:frequency',
        'pterodactyl:auth:2fa_required',
        'oauth2:enabled',
        'oauth2:required',
        'oauth2:all_drivers',
    ];

    /**
     * Keys specific to the mail driver that are only grabbed from the database
     * when using the SMTP driver.
     *
     * @var array
     */
    protected $emailKeys = [
        'mail:host',
        'mail:port',
        'mail:from:address',
        'mail:from:name',
        'mail:encryption',
        'mail:username',
        'mail:password',
    ];

    /**
     * Keys that are encrypted and should be decrypted when set in the
     * configuration array.
     *
     * @var array
     */
    protected static $encrypted = [
        'mail:password',
    ];

    /**
     * @var \Pterodactyl\Providers\SettingsServiceProvider
     */
    protected static $instance;

    /**
     * SettingsServiceProvider constructor.
     *
     * @param $app
     */
    public function __construct($app)
    {
        parent::__construct($app);

        static::$instance = $this;
        $this->injectOAuth2Providers();
    }

    /**
     * @return SettingsServiceProvider
     */
    public static function getInstance()
    {
        return static::$instance;
    }

    /**
     * Boot the service provider.
     *
     * @param \Illuminate\Contracts\Config\Repository $config
     * @param \Illuminate\Contracts\Encryption\Encrypter $encrypter
     * @param \Psr\Log\LoggerInterface $log
     * @param \Pterodactyl\Contracts\Repository\SettingsRepositoryInterface $settings
     */
    public function boot(ConfigRepository $config , Encrypter $encrypter, Log $log, SettingsRepositoryInterface $settings)
    {
        $this->config = $config;
        $this->log = $log;
        $this->settings = $settings;

        // Only set the email driver settings from the database if we
        // are configured using SMTP as the driver.
        if ($config->get('mail.driver') === 'smtp') {
            $this->keys = array_merge($this->keys, $this->emailKeys);
        }

        try {
            $values = $settings->all()->mapWithKeys(function ($setting) {
                return [$setting->key => $setting->value];
            })->toArray();
        } catch (QueryException $exception) {
            $log->notice('A query exception was encountered while trying to load settings from the database: ' . $exception->getMessage());

            return;
        }

        foreach ($this->keys as $key) {
            $value = array_get($values, 'settings::' . $key, $config->get(str_replace(':', '.', $key)));
            if (in_array($key, self::$encrypted)) {
                try {
                    $value = $encrypter->decrypt($value);
                } catch (DecryptException $exception) {
                }
            }

            switch (strtolower($value)) {
                case 'true':
                case '(true)':
                    $value = true;
                    break;
                case 'false':
                case '(false)':
                    $value = false;
                    break;
                case 'empty':
                case '(empty)':
                    $value = '';
                    break;
                case 'null':
                case '(null)':
                    $value = null;
            }

            $config->set(str_replace(':', '.', $key), $value);
        }
    }

    /**
     * @return array
     */
    public static function getEncryptedKeys(): array
    {
        return self::$encrypted;
    }

    /**
     * Inject OAuth2 providers into $keys
     */
    public function injectOAuth2Providers()
    {
        // As this hasn't been booted yet we need to manually get the value from DB
        foreach (preg_split('~,~', DB::table('settings')->where('key', '=', 'settings::oauth2:all_drivers')->value('value')) as $provider) {
            $array = [
                'oauth2:providers:' . $provider . ':status',
                'oauth2:providers:' . $provider . ':package',
                'oauth2:providers:' . $provider . ':listener',
                'oauth2:providers:' . $provider . ':client_id',
                'oauth2:providers:' . $provider . ':client_secret',
                'oauth2:providers:' . $provider . ':scopes',
                'oauth2:providers:' . $provider . ':widget_html',
                'oauth2:providers:' . $provider . ':widget_css',
            ];
            $this->keys = array_merge($this->keys, $array);
        }
    }

    public function updateOAuth2Config() {
        try {
            $values = $this->settings->all()->mapWithKeys(function ($setting) {
                return [$setting->key => $setting->value];
            })->toArray();
        } catch (QueryException $exception) {
            $this->log->notice('A query exception was encountered while trying to load settings from the database: ' . $exception->getMessage());

            return;
        }

        foreach ($this->keys as $key) {
            $value = array_get($values, 'settings::' . $key, $this->config->get(str_replace(':', '.', $key)));

            switch (strtolower($value)) {
                case 'true':
                case '(true)':
                    $value = true;
                    break;
                case 'false':
                case '(false)':
                    $value = false;
                    break;
                case 'empty':
                case '(empty)':
                    $value = '';
                    break;
                case 'null':
                case '(null)':
                    $value = null;
            }

            $this->config->set(str_replace(':', '.', $key), $value);
        }
    }
}

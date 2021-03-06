<?php

namespace Kurt\LiveCoding;

use Kurt\LiveCoding\Exceptions\InvalidClientIdException;
use Kurt\LiveCoding\Exceptions\InvalidClientSecretException;
use Kurt\LiveCoding\Exceptions\InvalidRedirectUrlException;
use Kurt\LiveCoding\Exceptions\InvalidScopeException;
use Kurt\LiveCoding\Exceptions\InvalidStorageException;
use Kurt\LiveCoding\Scopes\ReadScope;
use Kurt\LiveCoding\Scopes\Scope;
use Kurt\LiveCoding\Storages\SessionStorage;
use Kurt\LiveCoding\Storages\Storage;

/**
 * Class Client
 * @package Kurt\LiveCoding
 */
class Client
{
    /**
     * LiveCodingTV application client id.
     *
     * @var string
     */
    protected $id;

    /**
     * LiveCodingTV application client secret.
     *
     * @var string
     */
    protected $secret;

    /**
     * LiveCodingTV application redirect url.
     *
     * @var string
     */
    protected $redirectUrl;

    /**
     * Scope instance.
     *
     * @var \Kurt\LiveCoding\Scopes\Scope
     */
    protected $scope;

    /**
     * Storage instance.
     *
     * @var \Kurt\LiveCoding\Storages\Storage
     */
    protected $storage;

    /**
     * Exception instances.
     *
     * @var \Exception
     */
    protected $exceptions = [
        'id'          => InvalidClientIdException::class,
        'secret'      => InvalidClientSecretException::class,
        'redirectUrl' => InvalidRedirectUrlException::class,
        'scope'       => InvalidScopeException::class,
        'storage'     => InvalidStorageException::class,
    ];

    /**
     * [__construct description].
     *
     * @param array $credentials
     */
    public function __construct($credentials)
    {
        $this->initializeCredentials($credentials);
    }

    /**
     * [initializeCredentials description].
     *
     * @param array $credentials
     *
     * @return void
     */
    private function initializeCredentials($credentials)
    {
        $credentials = $this->mergeWithDefaults($credentials);

        foreach ($credentials as $key => $value) {
            $this->checkCredential([$key => $value]);

            $this->$key = $value;
        }
    }

    /**
     * [mergeWithDefaults description].
     *
     * @param array $credentials
     *
     * @return array
     */
    private function mergeWithDefaults($credentials)
    {
        $defaults = $this->getDefaults();

        $defaultKeys = array_keys($defaults);

        foreach (array_keys($credentials) as $key) {
            if (!in_array($key, $defaultKeys)) {
                unset($credentials[$key]);
            }
        }

        return array_merge($defaults, $credentials);
    }

    /**
     * [getDefaults description].
     *
     * @return array
     */
    private function getDefaults()
    {
        return [
            'id'          => '',
            'secret'      => '',
            'redirectUrl' => '',
            'scope'       => new ReadScope(),
            'storage'     => new SessionStorage(),
        ];
    }

    /**
     * [checkCredential description].
     *
     * @param array $credential
     *
     * @return void
     */
    private function checkCredential($credential)
    {
        list($key, $value) = each($credential);

        switch ($key) {
            case 'scope':
                if (!$value instanceof Scope) {
                    $this->throwExceptionFor($key);
                }
                break;
            case 'storage':
                if (!$value instanceof Storage) {
                    $this->throwExceptionFor($key);
                }
                break;
            default:
                if (empty($value)) {
                    $this->throwExceptionFor($key);
                }
        }
    }

    /**
     * [throwExceptionFor description].
     *
     * @param string $key
     *
     * @throws \Exception
     *
     * @return void
     */
    private function throwExceptionFor($key)
    {
        throw new $this->exceptions[$key]();
    }

    /**
     * [getId description].
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * [getSecret description].
     *
     * @return string
     */
    public function getSecret()
    {
        return $this->secret;
    }

    /**
     * [getRedirectUrl description].
     *
     * @return string
     */
    public function getRedirectUrl()
    {
        return $this->redirectUrl;
    }

    /**
     * [getScope description].
     *
     * @return Scope
     */
    public function getScope()
    {
        return $this->scope;
    }

    /**
     * [getStorage description].
     *
     * @return Storage
     */
    public function getStorage()
    {
        return $this->storage;
    }
}

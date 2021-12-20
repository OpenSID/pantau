<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Desa extends Model
{
    use HasFactory;

    /** {@inheritdoc} */
    protected $table = 'desa';

    /** {@inheritdoc} */
    public $timestamps = false;

    /** {@inheritdoc} */
    protected $casts = [
        'tgl_rekam_lokal' => 'datetime',
        'tgl_rekam_hosting' => 'datetime',
        'tgl_akses_lokal' => 'datetime',
        'tgl_akses_hosting' => 'datetime',
    ];

    /** {@inheritdoc} */
    protected $guarded = [];

    /**
     * Define a one-to-one relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function akses()
    {
        return $this->hasOne(Akses::class);
    }

    /**
     * Set ip lokal.
     *
     * ```php
     * $value = [
     *   'url'        => 'http://opensid-premium.test',
     *   'ip_address' => '127.0.0.1',
     * ];
     * ```
     *
     * @param array $value
     * @return void
     */
    public function setIpLokalAttribute(array $value)
    {
        if (is_local($value['url']) || is_local($value['ip_address'])) {
            $this->attributes['ip_lokal'] = $value['ip_address'];
        }
    }

    /**
     * Set ip hosting.
     *
     * ```php
     * $value = [
     *   'url'        => 'http://opensid-premium.test',
     *   'ip_address' => '127.0.0.1',
     * ];
     * ```
     *
     * @param array $value
     * @return void
     */
    public function setIpHostingAttribute(array $value)
    {
        if (is_local($value['url']) || is_local($value['ip_address'])) {
            $this->attributes['ip_hosting'] = $value['ip_address'];
        }
    }

    /**
     * Set versi lokal.
     *
     * ```php
     * $value = [
     *   'url'        => 'http://opensid-premium.test',
     *   'ip_address' => '127.0.0.1',
     *   'version'    => '21.12-premium-beta01',
     * ];
     * ```
     * @param array $value
     * @return void
     */
    public function setVersiLokalAttribute(array $value)
    {
        if (is_local($value['url']) || is_local($value['ip_address'])) {
            $this->attributes['versi_lokal'] = $value['version'];
        }
    }

    /**
     * Set versi hosting.
     *
     * ```php
     * $value = [
     *   'url'        => 'http://opensid-premium.test',
     *   'ip_address' => '127.0.0.1',
     *   'version'    => '21.12-premium-beta01',
     * ];
     * ```
     * @param array $value
     * @return void
     */
    public function setVersiHostingAttribute(array $value)
    {
        if (is_local($value['url']) || is_local($value['ip_address'])) {
            $this->attributes['versi_hosting'] = $value['version'];
        }
    }

    /**
     * Set url lokal.
     *
     * ```php
     * $value = [
     *   'url'        => 'http://opensid-premium.test',
     *   'ip_address' => '127.0.0.1',
     * ];
     * ```
     *
     * @param mixed $value
     * @return void
     */
    public function setUrlLokalAttribute($value)
    {
        if (is_local($value['url']) || is_local($value['ip_address'])) {
            $this->attributes['url_lokal'] = $value['url'];
        }
    }

    /**
     * Set url hosting.
     *
     * ```php
     * $value = [
     *   'url'        => 'http://opensid-premium.test',
     *   'ip_address' => '127.0.0.1',
     * ];
     * ```
     *
     * @param mixed $value
     * @return void
     */
    public function setUrlHostingAttribute($value)
    {
        if (is_local($value['url']) || is_local($value['ip_address'])) {
            $this->attributes['url_hosting'] = $value['url'];
        }
    }

    /**
     * Set tanggal akses lokal.
     *
     * ```php
     * $value = [
     *   'url'        => 'http://opensid-premium.test',
     *   'ip_address' => '127.0.0.1',
     * ];
     * ```
     *
     * @param array $value
     * @return void
     */
    public function setTglAksesLokalAttribute(array $value)
    {
        if (is_local($value['url']) || is_local($value['ip_address'])) {
            $this->attributes['tgl_akses_lokal'] = now();
        }
    }

    /**
     * Set tanggal akses hosting.
     *
     * ```php
     * $value = [
     *   'url'        => 'http://opensid-premium.test',
     *   'ip_address' => '127.0.0.1',
     * ];
     * ```
     *
     * @param array $value
     * @return void
     */
    public function setTglAksesHostingAttribute(array $value)
    {
        if (is_local($value['url']) || is_local($value['ip_address'])) {
            $this->attributes['tgl_akses_hosting'] = now();
        }
    }
}

<?php

namespace Pterodactyl\Models;

use Sofa\Eloquence\Eloquence;
use Sofa\Eloquence\Validable;
use Illuminate\Database\Eloquent\Model;
use Sofa\Eloquence\Contracts\CleansAttributes;
use Sofa\Eloquence\Contracts\Validable as ValidableContract;

class Mod extends Model implements CleansAttributes, ValidableContract
{
    use Eloquence, Validable;

    /**
     * The resource name for this model when it is transformed into an
     * API representation using fractal.
     */
    const RESOURCE_NAME = 'mod';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'mods';

    /**
     * Fields that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'mod_id', 'name', 'comprehensive', 'steam_id', 'install_script', 'steam_username', 'steam_password',
    ];

    /**
     * @var array
     */
    protected static $applicationRules = [
        'name' => 'required',
        'comprehensive' => 'sometimes',
        'steam_id' => 'sometimes',
        'install_script' => 'sometimes',
        'steam_username' => 'sometimes',
        'steam_password' => 'sometimes',
    ];

    /**
     * @var array
     */
    protected static $dataIntegrityRules = [
        'name' => 'string',
        'comprehensive' => 'boolean',
        'steam_id' => 'nullable|string',
        'install_script' => 'nullable|string',
        'egg_id' => 'exists:eggs,id',
    ];

    /**
     * Cast values to correct type.
     *
     * @var array
     */
    protected $casts = [
        'egg_id' => 'integer',
        'comprehensive' => 'boolean',
    ];

    /**
     * Parameters for search querying.
     *
     * @var array
     */
    protected $searchableColumns = [
        'name' => 10,
        'id' => 8,
        'egg.name' => 6,
    ];

    /**
     * Gets egg associated with a service pack.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function mod()
    {
        return $this->belongsTo(Mod::class);
    }

    public function mod_installed()
    {
        return $this->hasMany(Mod_installed::class);
    }

    public function nests()
    {
        return $this->belongsTo(Nest::class);    
    }

    public function egg()
    {
        return $this->belongsTo(Egg::class);    
    }
}

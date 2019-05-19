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
        'mod_id', 'egg_id', 'name', 'comprehensive', 'multiple', 'install_script', 'update_script', 'uninstall_script', 'install_script_container', 'install_script_entry',
    ];

    /**
     * @var array
     */
    protected static $applicationRules = [
        'name' => 'required',
        'comprehensive' => 'sometimes',
        'multiple' => 'sometimes',
        'install_script' => 'sometimes',
        'update_script' => 'sometimes',
        'uninstall_script' => 'sometimes',
        'install_script_container' => 'required',
        'install_script_entry' => 'required',
    ];

    /**
     * @var array
     */
    protected static $dataIntegrityRules = [
        'name' => 'string',
        'comprehensive' => 'boolean',
        'multiple' => 'boolean',
        'install_script' => 'nullable|string',
        'update_script' => 'nullable|string',
        'uninstall_script' => 'nullable|string',
        'egg_id' => 'exists:eggs,id',
        'mod_variable_id' => 'exists:mod_variable,id'
    ];

    /**
     * Cast values to correct type.
     *
     * @var array
     */
    protected $casts = [
        'egg_id' => 'integer',
        'comprehensive' => 'boolean',
        'multiple' => 'boolean',
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

    public function mod_variable()
    {
        return $this->hasMany(Mod_variable::class);
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

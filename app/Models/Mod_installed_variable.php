<?php

namespace Pterodactyl\Models;

use Sofa\Eloquence\Eloquence;
use Sofa\Eloquence\Validable;
use Illuminate\Database\Eloquent\Model;
use Sofa\Eloquence\Contracts\CleansAttributes;
use Sofa\Eloquence\Contracts\Validable as ValidableContract;

class Mod_installed_variable extends Model implements CleansAttributes, ValidableContract
{
    use Eloquence, Validable;

    /**
     * The resource name for this model when it is transformed into an
     * API representation using fractal.
     */
    const RESOURCE_NAME = 'mod_installed_variable';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'mod_installed_variable';

    /**
     * Fields that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'mod_installed_variable_id', 'variable_id', 'variable_value',
    ];

    /**
     * @var array
     */
    protected static $applicationRules = [
        'name' => 'required',
        'env_variable' => 'required',
        'multiple' => 'sometimes',
        'mod_id' => 'required',
        'steam_username' => 'sometimes',
    ];

    /**
     * @var array
     */
    protected static $dataIntegrityRules = [
        'variable_id' => 'exists:mod_variables,id',
    ];

    /**
     * Cast values to correct type.
     *
     * @var array
     */
    protected $casts = [
        'variable_id' => 'integer',
    ];

    /**
     * Parameters for search querying.
     *
     * @var array
     */
    protected $searchableColumns = [

    ];

    /**
     * Gets egg associated with a service pack.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function mod_variables()
    {
        return $this->belongsTo(Mod_variable::class, 'variable_id');
    }

}

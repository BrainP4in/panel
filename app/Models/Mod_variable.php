<?php

namespace Pterodactyl\Models;

use Sofa\Eloquence\Eloquence;
use Sofa\Eloquence\Validable;
use Illuminate\Database\Eloquent\Model;
use Sofa\Eloquence\Contracts\CleansAttributes;
use Sofa\Eloquence\Contracts\Validable as ValidableContract;

class Mod_variable extends Model implements CleansAttributes, ValidableContract
{
    use Eloquence, Validable;

    /**
     * The resource name for this model when it is transformed into an
     * API representation using fractal.
     */
    const RESOURCE_NAME = 'mod_variable';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'mod_variables';

    /**
     * Fields that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'mod_variable_id', 'mod_id', 'env_variable', 'default_value', 'name', 'description', 'rules', 'input_type', 'user_viewable', 'user_editable',
    ];

    /**
     * @var array
     */
    protected static $applicationRules = [
        'name' => 'required',
        'env_variable' => 'required',
        'multiple' => 'sometimes',
        'input_type' => 'required',
        'mod_id' => 'required',
        'user_viewable' => 'sometimes',
        'user_editable' => 'sometimes',
    ];

    /**
     * @var array
     */
    protected static $dataIntegrityRules = [
        'name' => 'string',
        'install_script' => 'nullable|string',
        'mod_id' => 'exists:mods,id',
    ];

    /**
     * Cast values to correct type.
     *
     * @var array
     */
    protected $casts = [
        'mod_id' => 'integer',
        'user_viewable' => 'integer',
        'user_editable' => 'integer',
    ];

    /**
     * Parameters for search querying.
     *
     * @var array
     */
    protected $searchableColumns = [
        'name' => 10,
        'id' => 8,
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

    public function mod_installed_variable()
    {
        return $this->hasMany(Mod_installed_variable::class);
    }
}

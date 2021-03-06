<?php

namespace Pterodactyl\Models;

use Sofa\Eloquence\Eloquence;
use Sofa\Eloquence\Validable;
use Illuminate\Database\Eloquent\Model;
use Sofa\Eloquence\Contracts\CleansAttributes;
use Sofa\Eloquence\Contracts\Validable as ValidableContract;

class Mod_installed extends Model implements CleansAttributes, ValidableContract
{
    use Eloquence, Validable;

    /**
     * The resource name for this model when it is transformed into an
     * API representation using fractal.
     */
    const RESOURCE_NAME = 'mod_installed';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'mods_installed';

    /**
     * Fields that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'mod_installed_id', 
    ];

    /**
     * @var array
     */
    protected static $applicationRules = [

    ];

    /**
     * @var array
     */
    protected static $dataIntegrityRules = [
        'mod_id' => 'exists:mods,id',
        'server_id' => 'exists:servers,id',
    ];

    /**
     * Cast values to correct type.
     *
     * @var array
     */
    protected $casts = [
        'mod_id' => 'integer',
        'server_id' => 'integer',
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
    public function mod_installed()
    {
        return $this->belongsTo(Mod_installed::class);
    }

    public function mod()
    {
        return $this->belongsTo(Mod::class);
    }

    public function mod_installed_variable()
    {
        return $this->hasMany(Mod_installed_variable::class);
    }

    public function server()
    {
        return $this->belongsTo(Server::class);
    }

}

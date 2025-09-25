<?php

namespace App\Models\Back\Settings;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class Settings extends Model
{
    use HasFactory;

    protected $table = 'settings';
    protected $guarded = ['id', 'created_at', 'updated_at'];

    public static function get(string $code, string $key)
    {
        $row = static::where('code', $code)->where('key', $key)->first();

        if ($row) {
            return $row->json ? collect(json_decode($row->value)) : $row->value;
        }
        return collect();
    }

    public static function insert(string $code, string $key, $value, bool $json)
    {
        return static::insertGetId([
            'code'       => $code,
            'key'        => $key,
            'value'      => $value,
            'json'       => $json,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);
    }

    public static function edit(int $id, string $code, string $key, $value, bool $json)
    {
        return static::where('id', $id)->update([
            'code'       => $code,
            'key'        => $key,
            'value'      => $value,
            'json'       => $json,
            'updated_at' => Carbon::now()
        ]);
    }

    public static function reset(string $code, string $key, $value, bool $json = true)
    {
        $row = static::where('code', $code)->where('key', $key)->first();
        $val = $json ? json_encode([$value]) : $value;

        if ($row) {
            return static::edit($row->id, $code, $key, $val, $json);
        }
        return static::insert($code, $key, $val, $json);
    }
}

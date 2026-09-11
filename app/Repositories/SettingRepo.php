<?php

namespace App\Repositories;


use App\Models\Setting;

class SettingRepo
{
    public function update($type, $desc)
    {
        return Setting::where('type', $type)->update(['description' => $desc]);
    }

    public function getSetting($type)
    {
        return Setting::where('type', $type)->get();
    }

    public function all()
    {
        return Setting::all();
    }

    public function createOrUpdate($type, $desc)
    {
        $setting = Setting::where('type', $type)->first();
        if ($setting) {
            $setting->update(['description' => $desc]);
            return $setting;
        }
        return Setting::create(['type' => $type, 'description' => $desc]);
    }
}
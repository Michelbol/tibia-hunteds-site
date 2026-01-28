<?php

namespace App\Setting;

use App\Models\Setting;

class SettingService {

    public function saveGuildName(string $configValue): Setting {
        return $this->createOrUpdate(SettingConfig::GUILD_NAME->value, $configValue);
    }
    public function createOrUpdate(string $configName, string $configValue): Setting {
        $setting = $this->firstSettingByName($configName);
        if (is_null($setting)) {
            return $this->saveNewSetting($configName, $configValue);
        }
        return $this->updateSetting($setting, $configValue);
    }

    private function saveNewSetting(string $configName, string $configValue): Setting {
        $setting = new Setting();
        $setting->name = $configName;
        $setting->value = $configValue;
        $setting->save();
        return $setting;
    }

    private function updateSetting(Setting $setting, string $configValue): Setting {
        $setting->value = $configValue;
        $setting->save();
        return $setting;
    }

    private function firstSettingByName(string $configName) {
        return Setting::where('name', $configName)->first();
    }

    public function getGuildName(): ?Setting {
        return $this->firstSettingByName(SettingConfig::GUILD_NAME->value);
    }
}

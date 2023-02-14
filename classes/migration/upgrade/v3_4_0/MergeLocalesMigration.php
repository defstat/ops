<?php

/**
 * @file classes/migration/upgrade/v3_4_0/MergeLocalesMigration.php
 *
 * Copyright (c) 2014-2021 Simon Fraser University
 * Copyright (c) 2000-2021 John Willinsky
 * Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * @class MergeLocalesMigration
 *
 * @brief Change Locales from locale_countryCode localization folder notation to locale localization folder notation
 */

namespace APP\migration\upgrade\v3_4_0;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use PKP\install\DowngradeNotSupportedException;

class MergeLocalesMigration extends \PKP\migration\upgrade\v3_4_0\MergeLocalesMigration
{
    protected string $CONTEXT_TABLE = 'servers';
    protected string $CONTEXT_SETTINGS_TABLE = 'server_settings';
    protected string $CONTEXT_COLUMN = 'server_id';

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        parent::up();

        // publication_galleys
        $publicationGalleys = DB::table('publication_galleys')
            ->get();

        foreach ($publicationGalleys as $publicationGalley) {
            $this->updateSingleValueLocale($publicationGalley->locale, 'publication_galleys', 'locale', 'galley_id', $publicationGalley->galley_id);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        throw new DowngradeNotSupportedException();
    }

    protected function getSettingsTables(): Collection
    {
        return collect([
            'server_settings' => 'server_id',
            'publication_galley_settings' => 'galley_id',
            'section_settings' => 'section_id',
        ])->merge(parent::getSettingsTables());
    }
}

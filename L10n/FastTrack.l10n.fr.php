<?php
/**
 * FastTrack French localization
 *
 * @author Frederic BAYLE
 */

namespace FastTrack\L10n;

use FastTrack\LocalizationData;

$LocaleFr = new LocalizationData();

$LocaleFr->Name = 'fr';
$LocaleFr->DateFormat = '/^(?<day>[0-9]{1,2})\/(?<month>[0-9]{1,2})\/(?<year>[0-9]{4,})(?:\s+(?<hour>[0-9]{1,2}):(?<minutes>[0-9]{1,2})(?::(?<seconds>[0-9]{1,2}))?)?/';
$LocaleFr->DecimalSeparator = ',';

return $LocaleFr;
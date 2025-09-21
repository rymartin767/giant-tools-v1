<?php

namespace App\Data;

class AirportNotes
{
    public static function getNotes(): array
    {
        return [
            'KJFK' => [
                'departure' => [
                    'runway' => '31L',
                    'typical_delays' => '6-9 AM, 4-7 PM',
                    'terminal_specific_procedures' => 'Verify terminal-specific procedures and runway assignments.',
                ],
                'arrival' => [
                    'runway' => '131',
                    'typical_delays' => 'Monitor typical delays during peak arrival times. Check for runway construction updates.',
                ],
            ],
            'KLAX' => [
                'departure' => [
                    'runway' => '25L/R',
                    'typical_delays' => 'Monitor Santa Ana winds, check for typical morning fog patterns.',
                    'terminal_specific_procedures' => 'Verify runway configurations for your departure direction.',
                ],
                'arrival' => [
                    'runway' => '24L/R',
                    'typical_delays' => 'Monitor Santa Ana wind conditions. Check for typical afternoon delays due to weather.',
                ],
            ],
            'KORD' => [
                'departure' => [
                    'runway' => '10L/R',
                    'typical_delays' => '7-9 AM, 4-6 PM',
                    'terminal_specific_procedures' => 'Monitor weather patterns affecting runway usage.',
                ],
                'arrival' => [
                    'runway' => '28L/R',
                    'typical_delays' => 'Monitor typical afternoon thunderstorm patterns.',
                    'terminal_specific_procedures' => 'Check for runway construction updates affecting arrivals.',
                ],
            ],
            'KATL' => [
                'departure' => [
                    'runway' => '26L/R',
                    'typical_delays' => '2-6 PM',
                    'terminal_specific_procedures' => 'Monitor typical afternoon thunderstorm patterns. Check for runway construction updates.',
                ],
                'arrival' => [
                    'runway' => '08L/R',
                    'typical_delays' => 'Monitor thunderstorm patterns affecting approach.',
                    'terminal_specific_procedures' => 'Check for terminal-specific arrival procedures.',
                ],
            ],
            'KDEN' => [
                'departure' => [
                    'runway' => '16L/R',
                    'typical_delays' => '2-6 PM',
                    'terminal_specific_procedures' => 'Monitor typical afternoon thunderstorm patterns and mountain wave conditions.',
                ],
                'arrival' => [
                    'runway' => '34L/R',
                    'typical_delays' => 'Monitor mountain wave conditions and afternoon thunderstorms.',
                    'terminal_specific_procedures' => 'Check for runway temperature effects.',
                ],
            ],
            'KDFW' => [
                'departure' => [
                    'runway' => '17L/R',
                    'typical_delays' => '3-7 PM',
                    'terminal_specific_procedures' => 'Monitor typical afternoon thunderstorm patterns. Check for runway construction updates.',
                ],
                'arrival' => [
                    'runway' => '35L/R',
                    'typical_delays' => 'Monitor thunderstorm patterns affecting approach.',
                    'terminal_specific_procedures' => 'Check for terminal-specific arrival procedures.',
                ],
            ],
            'KIAH' => [
                'departure' => [
                    'runway' => '26L/R',
                    'typical_delays' => '2-6 PM',
                    'terminal_specific_procedures' => 'Monitor typical afternoon thunderstorm patterns and runway conditions during rain.',
                ],
                'arrival' => [
                    'runway' => '08L/R',
                    'typical_delays' => 'Monitor thunderstorm patterns and runway conditions during rain.',
                    'terminal_specific_procedures' => 'Check for terminal-specific procedures.',
                ],
            ],
            'KPHX' => [
                'departure' => [
                    'runway' => '25L/R',
                    'typical_delays' => '3-6 PM',
                    'terminal_specific_procedures' => 'Monitor typical afternoon thunderstorm patterns. Check for runway temperature effects.',
                ],
                'arrival' => [
                    'runway' => '07L/R',
                    'typical_delays' => 'Monitor thunderstorm patterns and runway temperature effects.',
                    'terminal_specific_procedures' => 'Check for terminal-specific procedures.',
                ],
            ],
            'KCLT' => [
                'departure' => [
                    'runway' => '18L/R',
                    'typical_delays' => '2-6 PM',
                    'terminal_specific_procedures' => 'Monitor typical afternoon thunderstorm patterns and runway conditions during rain.',
                ],
                'arrival' => [
                    'runway' => '36L/R',
                    'typical_delays' => 'Monitor thunderstorm patterns and runway conditions during rain.',
                    'terminal_specific_procedures' => 'Check for terminal-specific procedures.',
                ],
            ],
            'KEWR' => [
                'departure' => [
                    'runway' => '22L/R',
                    'typical_delays' => '6-9 AM, 4-7 PM',
                    'terminal_specific_procedures' => 'Ensure proper runway assignments.',
                ],
                'arrival' => [
                    'runway' => '04L/R',
                    'typical_delays' => 'Monitor typical delays during peak arrival times.',
                    'terminal_specific_procedures' => 'Check for runway construction updates.',
                ],
            ],
        ];
    }

    public static function getDepartureNotes(string $airportCode): ?array
    {
        $notes = self::getNotes();

        return $notes[$airportCode]['departure'] ?? null;
    }

    public static function getArrivalNotes(string $airportCode): ?array
    {
        $notes = self::getNotes();

        return $notes[$airportCode]['arrival'] ?? null;
    }
}

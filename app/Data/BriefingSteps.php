<?php

namespace App\Data;

class BriefingSteps
{
    final public static function getSteps(): array
    {
        return [
            '1.0' => [
                'title' => 'Aircraft Status',
                'description' => 'Review aircraft status and operational requirements',
                'items' => [
                    [
                        'text' => 'Daily Check / Autoland Status',
                        'notes' => [
                            'type' => 'text',
                            'content' => 'Verify daily inspection completed and autoland system operational status',
                        ],
                    ],
                    [
                        'text' => 'DMI Review',
                        'notes' => [
                            'type' => 'modal',
                            'title' => 'DMI Review Requirements',
                            'content' => 'Review the Deferred Maintenance Items (DMI) list. Check for any MEL/CDL items that may affect the flight. Ensure all deferred items are within their allowable time limits and any operational restrictions are clearly understood.',
                        ],
                        'subItems' => [
                            [
                                'text' => 'Aerodata Requirements',
                                'notes' => [
                                    'type' => 'text',
                                    'content' => 'Verify aircraft performance data is current and applicable',
                                ],
                            ],
                        ],
                    ],
                    [
                        'text' => 'ETOPS / Verification Flight',
                        'conditions' => ['etops' => [true]],
                        'notes' => [
                            'type' => 'modal',
                            'title' => 'ETOPS and Verification Flight',
                            'content' => 'Confirm ETOPS certification is valid if operating extended range twin-engine operations. Verify if a verification flight is required following maintenance.',
                        ],
                        'subItems' => [
                            [
                                'text' => '3-Way Call / DDG Sect.1',
                                'notes' => [
                                    'type' => 'text',
                                    'content' => 'Complete three-way briefing call and review Dispatch Deviation Guide Section 1',
                                ],
                            ],
                        ],
                    ],
                    [
                        'text' => 'Aircraft Differences',
                        'notes' => [
                            'type' => 'modal',
                            'title' => 'Aircraft Differences',
                            'content' => 'Discuss any unique equipment configurations, avionics differences, or operational characteristics specific to this aircraft that may differ from fleet standard. Review any placards or notices in the cockpit.',
                        ],
                        'subItems' => [
                            [
                                'text' => 'Fire Suppression',
                                'notes' => [
                                    'type' => 'modal',
                                    'title' => 'Fire Suppression Systems',
                                    'content' => 'Verify all cargo compartment fire suppression systems are operational. Review fire detection and suppression procedures. Confirm fire extinguisher locations and serviceability.',
                                ],
                            ],
                        ],
                    ],
                    [
                        'text' => 'Live Animals & Perishables',
                        'conditions' => ['liveAnimals' => [true]],
                        'notes' => [
                            'type' => 'modal',
                            'title' => 'Live Animals and Perishables',
                            'content' => 'Live animals must only be loaded in bulk cargo compartments. Verify proper ventilation is being used. Ensure all required documentation is present. Review special handling requirements and emergency procedures.',
                        ],
                        'subItems' => [
                            [
                                'text' => 'Live Animals Only in Bulk',
                                'notes' => [
                                    'type' => 'text',
                                    'content' => 'Confirm live animals are loaded only in bulk cargo area',
                                ],
                            ],
                            [
                                'text' => 'Vent Used',
                                'notes' => [
                                    'type' => 'text',
                                    'content' => 'Verify cargo compartment ventilation system is operational and in use',
                                ],
                            ],
                        ],
                    ],
                    [
                        'text' => 'Hazmat',
                        'conditions' => ['hazmat' => [true]],
                        'notes' => [
                            'type' => 'modal',
                            'title' => 'Hazardous Materials',
                            'content' => 'Review all hazardous materials documentation. Ensure Red Book (Emergency Response Guidance for Aircraft Incidents Involving Dangerous Goods) is on board. Review NOTOC (Notification to Captain) for all dangerous goods. Understand location, quantity, and emergency procedures for each hazmat item.',
                        ],
                        'subItems' => [
                            [
                                'text' => 'Red Book on Board!',
                                'notes' => [
                                    'type' => 'text',
                                    'content' => 'Emergency Response Guidance for Aircraft Incidents Involving Dangerous Goods must be on board',
                                ],
                            ],
                            [
                                'text' => 'NOTOC Review',
                                'notes' => [
                                    'type' => 'text',
                                    'content' => 'Review Notification to Captain for all dangerous goods on board',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            '2.0' => [
                'title' => 'Fuel Plan',
                'description' => 'Review and verify fuel planning and reserves',
                'items' => [
                    'Actual vs Planned',
                    [
                        'text' => 'FMC Reserve vs OFP REMF',
                        'notes' => [
                            'type' => 'text',
                            'content' => 'B767 G/A = 15 MINS & 2600 LBS',
                        ],
                    ],
                    [
                        'text' => 'Enroute Considerations',
                        'notes' => [
                            'type' => 'text',
                            'content' => 'OFP CF',
                        ],
                    ],
                    [
                        'text' => 'Enroute WX / Sigmets',
                        'notes' => [
                            'type' => 'text',
                            'content' => 'Review enroute weather conditions and significant meteorological information',
                        ],
                    ],
                ],
            ],
            '3.0' => [
                'title' => 'Weather, Runway Conditions',
                'description' => 'Review weather conditions and runway status',
                'items' => [
                    [
                        'text' => 'Cold Weather Considerations',
                        'conditions' => ['coldWeatherOps' => [true]],
                        'notes' => [
                            'type' => 'text',
                            'content' => 'Review cold weather operational procedures and limitations',
                        ],
                        'subItems' => [
                            [
                                'text' => 'ENG A/I ON AFTER START',
                                'notes' => [
                                    'type' => 'text',
                                    'content' => 'Engine anti-ice must be turned on after engine start in cold weather conditions',
                                ],
                            ],
                            [
                                'text' => 'Contam Taxi or Remote Pad?',
                                'notes' => [
                                    'type' => 'text',
                                    'content' => 'FLAPS UP TAXI REQUIRED',
                                ],
                            ],
                        ],
                    ],
                    [
                        'text' => 'Wet or Contaminated Runway',
                        'notes' => [
                            'type' => 'text',
                            'content' => 'Review runway conditions and required performance adjustments',
                        ],
                        'subItems' => [
                            [
                                'text' => 'Aerodata Updated',
                                'notes' => [
                                    'type' => 'text',
                                    'content' => 'Verify performance data has been updated for runway conditions',
                                ],
                            ],
                        ],
                    ],
                    [
                        'text' => 'ATIS: LLWS',
                        'conditions' => ['llws' => [true]],
                        'notes' => [
                            'type' => 'text',
                            'content' => 'Low Level Wind Shear reported on ATIS',
                        ],
                        'subItems' => [
                            [
                                'text' => 'Aerodata Updated',
                                'notes' => [
                                    'type' => 'text',
                                    'content' => 'Performance data must be updated for wind shear considerations',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            '4.0' => [
                'title' => 'Terrain/Obstacles',
                'description' => 'Review terrain and obstacle considerations',
                'items' => [
                    [
                        'text' => 'Accel Height vs Area MEA',
                        'notes' => [
                            'type' => 'modal',
                            'title' => 'Acceleration Height vs Area MEA',
                            'content' => 'Compare the acceleration height with the Minimum Enroute Altitude (MEA) for the departure area. Ensure adequate terrain clearance during initial climb with flaps extended. If acceleration height is below area MEA, special procedures may be required.',
                        ],
                    ],
                    [
                        'text' => 'High Altitude Considerations',
                        'notes' => [
                            'type' => 'modal',
                            'title' => 'High Altitude Airport Operations',
                            'content' => 'Review high altitude airport performance considerations including reduced engine performance, longer takeoff distances, and altered climb performance. Verify oxygen requirements and pressurization system functionality.',
                        ],
                        'subItems' => [
                            [
                                'text' => 'CFIT Review',
                                'notes' => [
                                    'type' => 'modal',
                                    'title' => 'Controlled Flight Into Terrain (CFIT) Review',
                                    'content' => 'Review CFIT prevention procedures: Verify terrain awareness system functionality, review minimum safe altitudes, brief departure and approach procedures with special attention to terrain, confirm altitude callouts and monitoring procedures.',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            '5.0' => [
                'title' => 'NOTAMs',
                'description' => 'Review Notices to Airmen',
                'items' => [
                    [
                        'text' => 'Review NOTAMs',
                        'notes' => [
                            'type' => 'text',
                            'content' => 'Review all applicable Notices to Airmen for the departure, enroute, and arrival airports',
                        ],
                    ],
                ],
            ],
            '6.0' => [
                'title' => 'Taxi Plan/Hot Spots',
                'description' => 'Review taxi procedures and hotspots',
                'items' => [
                    [
                        'text' => 'Taxi Plan',
                        'notes' => [
                            'type' => 'text',
                            'content' => 'Review taxi route to departure runway and any taxi restrictions',
                        ],
                        'subItems' => [
                            [
                                'text' => 'Hotspots',
                                'notes' => [
                                    'type' => 'text',
                                    'content' => 'Review taxi route hot spots and any taxi restrictions',
                                ],
                            ],
                            [
                                'text' => 'Wingtip Restrictions (767 = 156\' 1" / 767W = 166\' 11")',
                                'notes' => [
                                    'type' => 'modal',
                                    'title' => 'Wingtip Clearance Restrictions',
                                    'content' => 'Review wingtip clearance requirements for taxiways and gate areas. Be aware of any narrow taxiway restrictions or special procedures required for your aircraft type. Boeing 767 wingspan: 156 feet 1 inch. Boeing 767 Widebody wingspan: 166 feet 11 inches.',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            '7.0' => [
                'title' => 'RTO',
                'description' => 'Review rejected takeoff procedures',
                'items' => [
                    [
                        'text' => 'Criterian',
                        'notes' => [
                            'type' => 'modal',
                            'title' => 'RTO Criteria',
                            'content' => 'Review the rejected takeoff criteria and decision-making process. Brief the conditions under which a takeoff will be rejected, including high-speed and low-speed abort considerations, crew responsibilities, and communication procedures.',
                        ],
                    ],
                    [
                        'text' => 'Immediate Action Items',
                        'notes' => [
                            'type' => 'modal',
                            'title' => 'Immediate Action Items',
                            'content' => 'Review immediate action items that must be taken during a rejected takeoff. Ensure crew coordination and clear communication of emergency procedures.',
                        ],
                        'subItems' => [
                            [
                                'text' => 'Confirm Threat Removed or Evacuate',
                                'notes' => [
                                    'type' => 'text',
                                    'content' => 'Verify the threat has been eliminated or initiate evacuation procedures as appropriate',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            '8.0' => [
                'title' => 'EFP, Return, TO Alternate',
                'description' => 'Review engine failure procedures and alternates',
                'items' => [
                    [
                        'text' => 'EFP',
                        'notes' => [
                            'type' => 'modal',
                            'title' => 'Engine Failure Procedures (EFP)',
                            'content' => 'Brief the engine failure procedures including initial actions, aircraft control, configuration management, and navigation to the planned route or alternate airport. Review single-engine performance limitations and fuel considerations.',
                        ],
                        'subItems' => [
                            [
                                'text' => 'Accel Height',
                                'notes' => [
                                    'type' => 'text',
                                    'content' => 'Review acceleration height for the departure - altitude at which flaps may be retracted following an engine failure',
                                ],
                            ],
                        ],
                    ],
                    [
                        'text' => 'T/O ALTN',
                        'notes' => [
                            'type' => 'modal',
                            'title' => 'Takeoff Alternate Airport',
                            'content' => 'Review the takeoff alternate airport selection criteria and requirements. Brief the airport facilities, weather, fuel required, and procedures for diversion in the event of an engine failure or other emergency shortly after takeoff.',
                        ],
                        'subItems' => [
                            [
                                'text' => '375 Miles',
                                'notes' => [
                                    'type' => 'text',
                                    'content' => 'Maximum distance from departure airport to takeoff alternate (375 NM for two-engine aircraft)',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            '9.0' => [
                'title' => 'Highest Threat Navigation',
                'description' => 'Review highest threat navigation considerations',
                'items' => [
                    [
                        'text' => 'Identify Highest Threat',
                        'notes' => [
                            'type' => 'text',
                            'content' => 'Identify and brief the highest threat to navigation for this flight',
                        ],
                    ],
                ],
            ],
        ];
    }
}

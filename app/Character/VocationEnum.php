<?php

namespace App\Character;

enum VocationEnum: string {
    case MS = 'Master Sorcerer';
    case ED = 'Elder Druid';
    case RP = 'Royal Paladin';
    case EK = 'Elite Knight';
    case EM = 'Exalted Monk';
    case M = 'Monk';
    case S = 'Sorcerer';
    case D = 'Druid';
    case P = 'Paladin';
    case K = 'Knight';
    case N = 'None';
}

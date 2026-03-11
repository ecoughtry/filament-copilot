<?php

declare(strict_types=1);

namespace EslamRedaDiv\FilamentCopilot\Enums;

enum CapabilityType: string
{
    case Read = 'read';
    case Search = 'search';
    case Sort = 'sort';
    case Fill = 'fill';
    case Save = 'save';
    case Draft = 'draft';
    case Execute = 'execute';
    case Filter = 'filter';
    case Interact = 'interact';
}

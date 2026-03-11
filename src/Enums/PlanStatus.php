<?php

declare(strict_types=1);

namespace EslamRedaDiv\FilamentCopilot\Enums;

enum PlanStatus: string
{
    case Proposed = 'proposed';
    case Approved = 'approved';
    case Rejected = 'rejected';
    case Executing = 'executing';
    case Completed = 'completed';
    case Failed = 'failed';
}

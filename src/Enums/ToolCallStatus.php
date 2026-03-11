<?php

declare(strict_types=1);

namespace EslamRedaDiv\FilamentCopilot\Enums;

enum ToolCallStatus: string
{
    case Pending = 'pending';
    case Approved = 'approved';
    case Rejected = 'rejected';
    case Executed = 'executed';
    case Failed = 'failed';
}

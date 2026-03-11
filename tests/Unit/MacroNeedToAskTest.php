<?php

use EslamRedaDiv\FilamentCopilot\Enums\CapabilityType;
use EslamRedaDiv\FilamentCopilot\Macros\MacroRegistrar;

it('stores needToAsk flag for components', function () {
    $component = new stdClass;

    MacroRegistrar::setNeedToAsk($component, CapabilityType::Fill, true);

    expect(MacroRegistrar::getNeedToAsk($component, CapabilityType::Fill))->toBeTrue();
    expect(MacroRegistrar::getNeedToAsk($component, CapabilityType::Read))->toBeFalse();
});

it('returns false by default for needToAsk', function () {
    $component = new stdClass;

    expect(MacroRegistrar::getNeedToAsk($component, CapabilityType::Fill))->toBeFalse();
    expect(MacroRegistrar::getNeedToAsk($component))->toBeFalse();
});

it('returns true for any needToAsk when type is null', function () {
    $component = new stdClass;

    MacroRegistrar::setNeedToAsk($component, CapabilityType::Save, true);

    expect(MacroRegistrar::getNeedToAsk($component))->toBeTrue();
});

it('gets all needToAsk flags for a component', function () {
    $component = new stdClass;

    MacroRegistrar::setNeedToAsk($component, CapabilityType::Fill, true);
    MacroRegistrar::setNeedToAsk($component, CapabilityType::Save, false);
    MacroRegistrar::setNeedToAsk($component, CapabilityType::Read, true);

    $flags = MacroRegistrar::getNeedToAskFlags($component);

    expect($flags)->toBeArray()
        ->and($flags)->toHaveKey('fill')
        ->and($flags['fill'])->toBeTrue()
        ->and($flags['save'])->toBeFalse()
        ->and($flags['read'])->toBeTrue();
});

it('sets capability and needToAsk together', function () {
    $component = new stdClass;

    MacroRegistrar::setCapability($component, CapabilityType::Execute, true);
    MacroRegistrar::setNeedToAsk($component, CapabilityType::Execute, true);

    expect(MacroRegistrar::hasCapability($component, CapabilityType::Execute))->toBeTrue();
    expect(MacroRegistrar::getNeedToAsk($component, CapabilityType::Execute))->toBeTrue();
});

it('sets description alongside needToAsk', function () {
    $component = new stdClass;

    MacroRegistrar::setDescription($component, 'Test description');
    MacroRegistrar::setNeedToAsk($component, CapabilityType::Fill, true);

    expect(MacroRegistrar::getDescription($component))->toBe('Test description');
    expect(MacroRegistrar::getNeedToAsk($component, CapabilityType::Fill))->toBeTrue();
});

it('supports Interact capability type for widgets', function () {
    $component = new stdClass;

    MacroRegistrar::setCapability($component, CapabilityType::Interact, true);
    MacroRegistrar::setNeedToAsk($component, CapabilityType::Interact, true);

    expect(MacroRegistrar::hasCapability($component, CapabilityType::Interact))->toBeTrue();
    expect(MacroRegistrar::getNeedToAsk($component, CapabilityType::Interact))->toBeTrue();
});

it('stores widget description via static methods', function () {
    $component = new stdClass;

    MacroRegistrar::setDescription($component, 'Revenue overview widget');

    expect(MacroRegistrar::getDescription($component))->toBe('Revenue overview widget');
});

<?php

declare(strict_types=1);

namespace PHPSTORM_META {
    expectedArguments(
        \Fom\Annotations\Attribute\Platform::__construct(),
        0,
        \Fom\Annotations\Attribute\Platform::EDITION_ANY,
        \Fom\Annotations\Attribute\Platform::EDITION_COMMUNITY,
        \Fom\Annotations\Attribute\Platform::EDITION_COMMERCE,
        \Fom\Annotations\Attribute\Platform::EDITION_B2B
    );
    expectedArguments(
        \Fom\Annotations\Attribute\Platform::__construct(),
        1,
        \Fom\Annotations\Attribute\Platform::VERSION_ANY
    );
    expectedArguments(
        \Fom\Annotations\Attribute\Platform::__construct(),
        2,
        \Fom\Annotations\Attribute\Platform::COMPARISON_LESS_THAN,
        \Fom\Annotations\Attribute\Platform::COMPARISON_LESS_THAN_OR_EQUAL,
        \Fom\Annotations\Attribute\Platform::COMPARISON_GREATER_THAN,
        \Fom\Annotations\Attribute\Platform::COMPARISON_GREATER_THAN_OR_EQUAL,
        \Fom\Annotations\Attribute\Platform::COMPARISON_EQUAL,
        \Fom\Annotations\Attribute\Platform::COMPARISON_NOT_EQUAL
    );
    expectedArguments(
        \Fom\Annotations\Attribute\Operator::__construct(),
        0,
        \Fom\Annotations\Attribute\Operator::OPERATOR_AND,
        \Fom\Annotations\Attribute\Operator::OPERATOR_OR
    );
}

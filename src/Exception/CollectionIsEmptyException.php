<?php

declare(strict_types=1);

namespace Version\Exception;

use LogicException;

class CollectionIsEmptyException extends LogicException implements ExceptionInterface
{
}

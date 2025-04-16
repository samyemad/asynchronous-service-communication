<?php
namespace App\Tests\Unit\InternalAuthorization\Domain\Model;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use App\InternalAuthorization\Domain\Model\AuthorizationDecisionId;

class AuthorizationDecisionIdTest extends TestCase
{
    public function test_valid_authorization_decision_id_is_created(): void
    {
        $uuid = '123e4567-e89b-12d3-a456-426614174000';
        $authId = new AuthorizationDecisionId($uuid);

        $this->assertSame($uuid, $authId->value());
    }

    public function test_invalid_authorization_decision_id_throws_exception(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid AuthorizationDecisionId format');

        new AuthorizationDecisionId('not-a-valid-uuid');
    }

    public function test_equals_returns_true_for_same_value(): void
    {
        $uuid = '123e4567-e89b-12d3-a456-426614174000';
        $id1 = new AuthorizationDecisionId($uuid);
        $id2 = new AuthorizationDecisionId($uuid);

        $this->assertTrue($id1->equals($id2));
    }

    public function test_equals_returns_false_for_different_values(): void
    {
        $id1 = new AuthorizationDecisionId('123e4567-e89b-12d3-a456-426614174000');
        $id2 = new AuthorizationDecisionId('123e4567-e89b-12d3-a456-426614174111');

        $this->assertFalse($id1->equals($id2));
    }
}


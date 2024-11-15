<?php

namespace SlimFramework\Entity\User;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use League\OAuth2\Server\Entities\UserEntityInterface;
use SlimFramework\Entity\Entity;

class UserEntity extends Entity implements UserEntityInterface
{
    protected $table = 'user';

    /**
     * @var int
     */
    public int $id;

    /**
     * @return int
     */
    public function getIdentifier(): int
    {
        return $this->getAttribute('id');
    }

    /**
     * @return BelongsTo
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(ClientEntity::class, 'oauth2_client_id', 'id');
    }

    /**
     * @return BelongsTo|
     */
    public function profile(): BelongsTo
    {
        return $this->belongsTo(ProfileEntity::class, 'profile_id', 'id');
    }

    /**
     * @return BelongsTo
     */
    public function token(): BelongsTo
    {
        return $this->belongsTo(AccessTokenEntity::class);
    }
}

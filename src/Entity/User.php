<?php

namespace App\Entity;

use App\Repository\UserRepository;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @ORM\HasLifecycleCallbacks
 */
class User extends EntityBase implements UserInterface
{
    /**
     * @Groups("auth_user")
     * @ORM\Column(name="created_at", type="datetime", nullable=false)
     */
    protected $created_at;

    /**
     * @Groups("auth_user")
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Groups("auth_user")
     * @ORM\Column(type="integer")
     */
    private int $account_id;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $api_token;

    /** @ORM\Column(type="string", nullable=true) */
    private ?string $wg_api_token;

    /**
     * @Groups({"auth_user", "g_player_stats_history"})
     * @ORM\ManyToOne(targetEntity=Player::class, inversedBy="users")
     */
    private $player;

    /**
     * @Groups({"auth_user"})
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $deleted_at;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $expired_at;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $psn_token;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $live_token;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private $expiry;

    /**
     * @Groups({"auth_user"})
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $user_agent;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAccountId(): ?int
    {
        return $this->account_id;
    }

    public function setAccountId(int $account_id): self
    {
        $this->account_id = $account_id;

        return $this;
    }

    public function getApiToken(): ?string
    {
        return $this->api_token;
    }

    public function setApiToken(?string $api_token): self
    {
        $this->api_token = $api_token;

        return $this;
    }

    public function getWgApiToken(): string
    {
        return $this->wg_api_token;
    }

    public function setWgApiToken(?string $wg_api_token): self
    {
        $this->wg_api_token = $wg_api_token;

        return $this;
    }

    public function getUserIdentifier()
    {
        return $this->api_token;
    }

    public function getRoles()
    {
        $roles = ['ROLE_PLAYER'];

        $player = $this->getPlayer();
        if (!$player) {
            return $roles;
        }

        $clan = $player->getClan();
        if (!$clan) {
            return $roles;
        }

        if ($clan->isUserCreatorOrLeader($player)) {
            $roles[] = 'ROLE_LEADER';
        }

        return $roles;
    }

    public function getPlayer(): ?Player
    {
        return $this->player;
    }

    public function setPlayer(?Player $player): self
    {
        $this->player = $player;

        return $this;
    }

    public function getPassword()
    {
        // TODO: Implement getPassword() method.
    }

    public function getSalt()
    {
        // TODO: Implement getSalt() method.
    }

    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }

    public function getUsername()
    {
        // TODO: Implement getUsername() method.
    }

    public function __call(string $name, array $arguments)
    {
        // TODO: Implement @method string getUserIdentifier()
    }

    public function getDeletedAt(): ?DateTimeInterface
    {
        return $this->deleted_at;
    }

    public function setDeletedAt(?DateTimeInterface $deleted_at): self
    {
        $this->deleted_at = $deleted_at;

        return $this;
    }

    public function getExpiredAt(): ?DateTimeInterface
    {
        return $this->expired_at;
    }

    public function setExpiredAt(?DateTimeInterface $expired_at): self
    {
        $this->expired_at = $expired_at;

        return $this;
    }

    public function getPsnToken(): ?string
    {
        return $this->psn_token;
    }

    public function setPsnToken(?string $psn_token): self
    {
        $this->psn_token = $psn_token;

        return $this;
    }

    public function getLiveToken(): ?string
    {
        return $this->live_token;
    }

    public function setLiveToken(?string $live_token): self
    {
        $this->live_token = $live_token;

        return $this;
    }

    public function getExpiry(): ?string
    {
        return $this->expiry;
    }

    public function setExpiry(?string $expiry): self
    {
        $this->expiry = $expiry;

        return $this;
    }

    public function getUserAgent(): ?string
    {
        return $this->user_agent;
    }

    public function setUserAgent(?string $user_agent): self
    {
        $this->user_agent = $user_agent;

        return $this;
    }
}
